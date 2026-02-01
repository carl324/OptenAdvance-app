<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reporte;
use App\Models\Empresa;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use Illuminate\Support\Facades\Log;
class ReporteController extends Controller
{
    // Límite máximo de filas permitidas en exportación (protección de rendimiento)
    private const MAX_EXPORT_ROWS = 10000;

    // Flag informativo: los reportes representan datos históricos
    // es_dato_historico = true
    public function index(Request $request)
    {
        $tipo = $request->input('tipo', 'ventas');

        // Validación defensiva de inputs: fechas, estado y order
        $fecha_inicio = $this->sanitizeDate($request->input('fecha_inicio'));
        $fecha_fin = $this->sanitizeDate($request->input('fecha_fin'));
        $estado = $this->sanitizeEstado($request->input('estado'));

        $empresa = Empresa::first();

        // Preparar filtros (se pasan sólo valores saneados)
        $filtros = [
            'fecha_inicio' => $fecha_inicio,
            'fecha_fin' => $fecha_fin,
            'estado' => $estado,
            'order' => $this->sanitizeOrder($request->input('order', 'desc')),
        ];

        // Usar el modelo Reporte para obtener los datos
        switch ($tipo) {
            case 'ventas':
                // Ahora mostramos ventas (no detalle)
                $data = Reporte::ventas($filtros)->paginate(15)->withQueryString();
                break;
            
            case 'inventario_movimientos':
                $data = Reporte::inventarioMovimientos($filtros)->paginate(15)->withQueryString();
                break;
            
            default:
                $data = collect();
        }

        // Añadir campo informativo por fila: origen_reporte (solo para UI)
        if (isset($data) && method_exists($data, 'getCollection')) {
            $data->setCollection($data->getCollection()->map(function ($item) use ($tipo) {
                if (($tipo ?? '') === 'inventario_movimientos') {
                    $item->origen_reporte = 'inventario';
                } else {
                    $item->origen_reporte = 'venta';
                }
                return $item;
            }));
        }

        // Flag explicito para dejar claro que reportes muestran datos históricos
        $es_dato_historico = true;

        return view('reportes.index', compact('empresa', 'tipo', 'fecha_inicio', 'fecha_fin', 'data', 'es_dato_historico'));
    }

    /**
     * Obtiene los detalles de una venta específica (para el modal)
     * Carga relaciones para evitar N+1 queries
     */
    public function ventaDetalles($id)
    {
        $empresa = Empresa::first();
        
        // Eager loading de relaciones para evitar N+1
        $detalles = \App\Models\VentaDetalle::with(['producto', 'venta.factura'])
            ->where('venta_id', $id)
            ->get();

        if ($detalles->isEmpty()) {
            return response()->json(['error' => 'No se encontraron detalles'], 404);
        }

        // Usar relaciones cargadas, no hacer queries adicionales
        return response()->json([
            'detalles' => $detalles->map(function($d) {
                return [
                    'id' => $d->id,
                    'venta_id' => $d->venta_id,
                    'producto_id' => $d->producto_id,
                    'producto' => [
                        'nombre' => optional($d->producto)->nombre ?? 'Producto #' . $d->producto_id
                    ],
                    'cantidad' => $d->cantidad,
                    'precio_unitario' => $d->precio_unitario,
                    'iva' => $d->iva,
                    'subtotal' => $d->subtotal,
                ];
            }),
            'venta_id' => $id,
            'factura_id' => optional($detalles->first()->venta->factura)->id ?? null,
            'cobra_iva' => $empresa && $empresa->cobra_iva
        ]);
    }

    public function export(Request $request)
    {
        return $this->exportExcel($request);
    }

    /**
     * Exporta el reporte a Excel según el tipo seleccionado
     * Respeta todos los filtros aplicados: fecha_inicio, fecha_fin, estado
     */
    public function exportExcel(Request $request)
    {
        $tipo = $request->input('tipo', 'ventas');
        $empresa = Empresa::first();

        // Preparar filtros - IMPORTANTES: estos se aplican tanto en vista como en exportación
        // Se aplican saneamientos idénticos a los usados en la vista para mantener consistencia
        $filtros = [
            'fecha_inicio' => $this->sanitizeDate($request->input('fecha_inicio')),
            'fecha_fin' => $this->sanitizeDate($request->input('fecha_fin')),
            'estado' => $this->sanitizeEstado($request->input('estado')),
            'order' => $this->sanitizeOrder($request->input('order', 'desc')),
        ];

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        switch ($tipo) {
            case 'ventas':
                // Protección: no permitir exportaciones masivas que puedan agotar memoria
                $count = Reporte::ventasDetalle($filtros)->count();
                if ($count > self::MAX_EXPORT_ROWS) {
                    return response()->json([
                        'success' => false,
                        'code' => 'EXPORT_LIMIT_EXCEEDED',
                        'message' => 'El reporte es demasiado grande para exportar. Reduzca el rango de fechas o contacte a soporte.'
                    ], 413);
                }

                $this->exportVentasCompletas($sheet, $filtros, $empresa);
                $filename = 'reporte_ventas_completo';
                break;
            
            case 'inventario_movimientos':
                $count = Reporte::inventarioMovimientos($filtros)->count();
                if ($count > self::MAX_EXPORT_ROWS) {
                    return response()->json([
                        'success' => false,
                        'code' => 'EXPORT_LIMIT_EXCEEDED',
                        'message' => 'El reporte es demasiado grande para exportar. Reduzca el rango de fechas o contacte a soporte.'
                    ], 413);
                }

                $this->exportInventarioMovimientos($sheet, $filtros);
                $filename = 'reporte_inventario_movimientos';
                break;

            case 'cajas':
                // Contar registros en rango sin paginación
                $queryCount = \DB::table('cajas as c')
                    ->whereBetween('c.fecha_apertura', [$filtros['fecha_inicio'] . ' 00:00:00', $filtros['fecha_fin'] . ' 23:59:59']);

                $count = $queryCount->count();
                if ($count > self::MAX_EXPORT_ROWS) {
                    return response()->json([
                        'success' => false,
                        'code' => 'EXPORT_LIMIT_EXCEEDED',
                        'message' => 'El reporte es demasiado grande para exportar. Reduzca el rango de fechas o contacte a soporte.'
                    ], 413);
                }

                $this->exportCajas($sheet, $filtros);
                $filename = 'reporte_cajas';
                break;
            
            default:
                $filename = 'reporte';
        }

        $filename .= '_' . now()->format('Ymd_His') . '.xlsx';

        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function() use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        ]);
    }

    /**
     * Exporta el reporte de ventas completo (ventas + detalle) a Excel
     */
    private function exportVentasCompletas($sheet, $filtros, $empresa)
    {
        $sheet->setTitle('Ventas Completas');
        
        // Headers (añadimos Vendedor y Rol)
        $headers = ['Fecha', 'Venta ID', 'N° Factura', 'Cliente', 'Vendedor', 'Rol', 'Producto', 'Cantidad', 'Precio Unit.', 'Subtotal'];

        // Verificar si hay datos históricos con IVA > 0
        $detalles = Reporte::ventasDetalle($filtros)->get();
        $hayIvaHistorico = $detalles->contains(function($d) {
            return ($d->iva ?? 0) > 0;
        });

        // Mostrar columna IVA si la empresa cobra IVA O si hay datos históricos con IVA
        $mostrarIva = ($empresa && $empresa->cobra_iva) || $hayIvaHistorico;

        if ($mostrarIva) {
            $headers[] = 'IVA';
        }
        
        $headers[] = 'Total';
        $headers[] = 'Estado';
        
        $col = 'A';
        foreach ($headers as $h) {
            $sheet->setCellValue($col . '1', $h);
            $sheet->getStyle($col . '1')->getFont()->setBold(true);
            $sheet->getStyle($col . '1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $col++;
        }

        $row = 2;
$totalCantidad = 0;
$totalSubtotal = 0;
$totalIva = 0;
$totalTotal = 0;

// Cargar usuarios relacionados en una sola consulta para evitar N+1
$userIds = $detalles->map(function($d) {
    return optional($d->venta)->user_id;
})->filter()->unique()->values()->all();
$users = \App\Models\User::whereIn('id', $userIds)->get()->keyBy('id');

// Variables para coloreo alternado por factura
$facturaAnterior = null;
$colorActual = 0; // 0 = blanco, 1 = verde claro
$colores = [
    'FFFFFF', // Blanco
    'E8F5E9'  // Verde claro suave
];

foreach ($detalles as $d) {
    // Origen del registro (solo para UI / export)
    $d->origen_reporte = 'venta_detalle';
    $fecha = optional($d->venta->created_at)->format('Y-m-d H:i');
    $facturaNumero = optional($d->venta->factura)->numero ?? '-';
    
    // Detectar cambio de factura para alternar color
    if ($facturaAnterior !== null && $facturaAnterior !== $facturaNumero) {
        $colorActual = ($colorActual + 1) % 2; // Alterna entre 0 y 1
    }
    $facturaAnterior = $facturaNumero;
            $cliente = (optional($d->venta->factura)->cliente_nombre ?? $d->venta->cliente) ?? '-';
            $producto = optional($d->producto)->nombre ?? '#' . $d->producto_id;
            // Usar subtotal directamente (ya incluye IVA desde BD)
            $total = $d->subtotal;

            // Obtener vendedor y rol desde colección precargada
            $ventaUserId = optional($d->venta)->user_id;
            $vendedor = $users->get($ventaUserId);
            $vendedorNombre = optional($vendedor)->name ?? '-';
            $vendedorRol = optional($vendedor)->role ?? '-';

            // Preparar fila de valores acorde al orden de $headers
            $values = [];
            $values[] = $fecha;
            $values[] = $d->venta_id;
            $values[] = $facturaNumero;
            $values[] = $cliente;
            $values[] = $vendedorNombre;
            $values[] = $vendedorRol;
            $values[] = $producto;
            $values[] = (float)$d->cantidad;
            $values[] = (float)$d->precio_unitario;
            $values[] = (float)$d->subtotal;

            if ($mostrarIva) {
                $values[] = (float)$d->iva;
            }

            $values[] = (float)$total;
            $values[] = $d->venta->estado ?? '-';

            // Escribir valores en hoja
            // Escribir valores en hoja
$col = 'A';
foreach ($values as $val) {
    $sheet->setCellValue($col . $row, $val);
    $col++;
}

// Aplicar color de fondo a toda la fila
$lastCol = Coordinate::stringFromColumnIndex(count($headers));
$sheet->getStyle('A' . $row . ':' . $lastCol . $row)
      ->getFill()
      ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
      ->getStartColor()
      ->setRGB($colores[$colorActual]);

            $totalCantidad += (float)$d->cantidad;
            $totalSubtotal += (float)$d->subtotal;
            $totalIva += (float)($d->iva ?? 0);
            $totalTotal += (float)$total;

            $row++;
        }

        // Totales: ubicar columnas dinámicamente según headers
        $idxProducto = array_search('Producto', $headers);
        $idxCantidad = array_search('Cantidad', $headers);
        $idxSubtotal = array_search('Subtotal', $headers);
        $idxIva = array_search('IVA', $headers);
        $idxTotal = array_search('Total', $headers);

        if ($idxProducto !== false) {
            $colProducto = Coordinate::stringFromColumnIndex($idxProducto + 1);
            $sheet->setCellValue($colProducto . $row, 'TOTALES');
            $sheet->getStyle($colProducto . $row)->getFont()->setBold(true);
        }
        if ($idxCantidad !== false) {
            $colCantidad = Coordinate::stringFromColumnIndex($idxCantidad + 1);
            $sheet->setCellValue($colCantidad . $row, $totalCantidad);
        }
        if ($idxSubtotal !== false) {
            $colSubtotal = Coordinate::stringFromColumnIndex($idxSubtotal + 1);
            $sheet->setCellValue($colSubtotal . $row, $totalSubtotal);
        }

        if ($idxIva !== false) {
            $colIva = Coordinate::stringFromColumnIndex($idxIva + 1);
            $sheet->setCellValue($colIva . $row, $totalIva);
        }

        if ($idxTotal !== false) {
            $colTotal = Coordinate::stringFromColumnIndex($idxTotal + 1);
            $sheet->setCellValue($colTotal . $row, $totalTotal);
        }

        // Poner en negrita la fila de totales en las columnas numéricas si existen
        $firstNumIdx = $idxCantidad !== false ? $idxCantidad + 1 : null;
        $lastNumIdx = $idxTotal !== false ? $idxTotal + 1 : null;
        if ($firstNumIdx && $lastNumIdx) {
            $sheet->getStyle(Coordinate::stringFromColumnIndex($firstNumIdx) . $row . ':' . Coordinate::stringFromColumnIndex($lastNumIdx) . $row)->getFont()->setBold(true);
        }

        // Formato de moneda: aplicar a Precio Unitario, Subtotal, IVA y Total si existen
        $currencyFormat = '"$"#,##0';
        $priceIdx = array_search('Precio Unit.', $headers);
        if ($priceIdx !== false && $idxSubtotal !== false) {
            $colPrice = Coordinate::stringFromColumnIndex($priceIdx + 1);
            $colSubtotal = Coordinate::stringFromColumnIndex($idxSubtotal + 1);
            $sheet->getStyle($colPrice . '2:' . $colSubtotal . $row)->getNumberFormat()->setFormatCode($currencyFormat);
        }
        if ($idxIva !== false && $idxTotal !== false) {
            $colIva = Coordinate::stringFromColumnIndex($idxIva + 1);
            $colTotal = Coordinate::stringFromColumnIndex($idxTotal + 1);
            $sheet->getStyle($colIva . '2:' . $colTotal . $row)->getNumberFormat()->setFormatCode($currencyFormat);
        } elseif ($idxTotal !== false) {
            $colTotal = Coordinate::stringFromColumnIndex($idxTotal + 1);
            $sheet->getStyle($colTotal . '2:' . $colTotal . $row)->getNumberFormat()->setFormatCode($currencyFormat);
        }

        // Auto size
        $lastCol = Coordinate::stringFromColumnIndex(count($headers));
        foreach (range('A', $lastCol) as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }
    }

    /**
     * Exporta el reporte de movimientos de inventario a Excel
     */
    private function exportInventarioMovimientos($sheet, $filtros)
    {
        $sheet->setTitle('Movimientos Inventario');
        
        // Headers
        $headers = ['ID', 'Fecha', 'Producto', 'Tipo', 'Cantidad', 'Origen', 'Referencia', 'Descripción'];
        $col = 'A';
        foreach ($headers as $h) {
            $sheet->setCellValue($col . '1', $h);
            $sheet->getStyle($col . '1')->getFont()->setBold(true);
            $sheet->getStyle($col . '1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $col++;
        }

        // Obtener datos usando el modelo Reporte
        $movimientos = Reporte::inventarioMovimientos($filtros)->get();

        $row = 2;
        $totalEntradas = 0;
        $totalSalidas = 0;

        foreach ($movimientos as $m) {
            // Origen del registro (solo para UI / export)
            $m->origen_reporte = 'inventario';
            // Usar formato consistente con BD (created_at siempre es datetime)
            if ($m->created_at instanceof \Carbon\Carbon) {
                $fecha = $m->created_at->format('Y-m-d H:i');
            } elseif (is_string($m->created_at)) {
                $fecha = \Carbon\Carbon::parse($m->created_at)->format('Y-m-d H:i');
            } else {
                $fecha = '-';
            }
            $productoNombre = $m->producto_nombre ?? '#' . $m->producto_id;
            
            $sheet->setCellValue('A' . $row, $m->id);
            $sheet->setCellValue('B' . $row, $fecha);
            $sheet->setCellValue('C' . $row, $productoNombre);
            $sheet->setCellValue('D' . $row, ucfirst($m->tipo));
            $sheet->setCellValue('E' . $row, (int)$m->cantidad);
            $sheet->setCellValue('F' . $row, $m->origen ?? '-');
            $sheet->setCellValue('G' . $row, $m->referencia_id ?? '-');
            $sheet->setCellValue('H' . $row, $m->descripcion ?? '-');

            if ($m->tipo === 'entrada') {
                $totalEntradas += (int)$m->cantidad;
            } else {
                $totalSalidas += (int)$m->cantidad;
            }

            $row++;
        }

        // Resumen
        $row++;
        $sheet->setCellValue('C' . $row, 'Total Entradas:');
        $sheet->setCellValue('E' . $row, $totalEntradas);
        $sheet->getStyle('C' . $row . ':E' . $row)->getFont()->setBold(true);
        
        $row++;
        $sheet->setCellValue('C' . $row, 'Total Salidas:');
        $sheet->setCellValue('E' . $row, $totalSalidas);
        $sheet->getStyle('C' . $row . ':E' . $row)->getFont()->setBold(true);

        $row++;
        $sheet->setCellValue('C' . $row, 'Diferencia:');
        $sheet->setCellValue('E' . $row, $totalEntradas - $totalSalidas);
        $sheet->getStyle('C' . $row . ':E' . $row)->getFont()->setBold(true);

        // Auto size
        foreach (range('A', 'H') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }
    }

    /**
     * Exporta el reporte de cajas a Excel
     */
  private function exportCajas($sheet, $filtros)
{
    $sheet->setTitle('Cajas');

    // Headers actualizados con usuario apertura y usuario cierre
    $headers = [
        'Fecha apertura',
        'Fecha cierre',
        'Usuario apertura',
        'Usuario cierre',
        'Estado',
        'Monto apertura',
        'Total ventas',
        'Total efectivo',
        'Monto cierre calculado',
        'Monto cierre real',
        'Diferencia',
        'Nota apertura',
        'Nota cierre',
    ];

    // Escribir headers
    $col = 'A';
    $headerCells = [];
    foreach ($headers as $h) {
        $sheet->setCellValue($col . '1', $h);
        $headerCells[] = $col . '1';
        $col++;
    }

    // Aplicar estilos al header
    $this->aplicarEstilosHeader($sheet, $headerCells);

    // Obtener datos con JOIN a users dos veces (para apertura y cierre)
    $rows = \DB::table('cajas as c')
        ->leftJoin('users as ua', 'ua.id', '=', 'c.user_id')
        ->leftJoin('users as uc', 'uc.id', '=', 'c.user_cierre_id')
        ->whereBetween('c.fecha_apertura', [$filtros['fecha_inicio'] . ' 00:00:00', $filtros['fecha_fin'] . ' 23:59:59'])
        ->orderBy('c.fecha_apertura', 'desc')
        ->select(
            'c.fecha_apertura',
            'c.fecha_cierre',
            \DB::raw('ua.name as usuario_apertura'),
            \DB::raw('uc.name as usuario_cierre'),
            'c.estado',
            'c.monto_apertura',
            'c.total_ventas',
            'c.total_efectivo',
            'c.monto_cierre_calculado',
            'c.monto_cierre_real',
            'c.diferencia',
            'c.nota_apertura',
            'c.nota_cierre'
        )
        ->get();

    $row = 2;
    foreach ($rows as $r) {
        // Formatear fechas
        $fechaA = $r->fecha_apertura ? (\Carbon\Carbon::parse($r->fecha_apertura)->format('Y-m-d H:i')) : '';
        $fechaC = $r->fecha_cierre ? (\Carbon\Carbon::parse($r->fecha_cierre)->format('Y-m-d H:i')) : 'Abierta';

        $sheet->setCellValue('A' . $row, $fechaA);
        $sheet->setCellValue('B' . $row, $fechaC);
        $sheet->setCellValue('C' . $row, $r->usuario_apertura ?? '-');
        $sheet->setCellValue('D' . $row, $r->usuario_cierre ?? '-');
        $sheet->setCellValue('E' . $row, $r->estado ?? '-');

        // Montos como números
        $sheet->setCellValue('F' . $row, $r->monto_apertura !== null ? (float)$r->monto_apertura : null);
        $sheet->setCellValue('G' . $row, $r->total_ventas !== null ? (float)$r->total_ventas : null);
        $sheet->setCellValue('H' . $row, $r->total_efectivo !== null ? (float)$r->total_efectivo : null);
        $sheet->setCellValue('I' . $row, $r->monto_cierre_calculado !== null ? (float)$r->monto_cierre_calculado : null);
        $sheet->setCellValue('J' . $row, $r->monto_cierre_real !== null ? (float)$r->monto_cierre_real : null);
        $sheet->setCellValue('K' . $row, $r->diferencia !== null ? (float)$r->diferencia : null);

        $sheet->setCellValue('L' . $row, $r->nota_apertura ?? '-');
        $sheet->setCellValue('M' . $row, $r->nota_cierre ?? '-');

        $row++;
    }

    // Aplicar formato numérico a columnas F-K (montos)
    $currencyRange = 'F2:K' . max(2, $row - 1);
    $sheet->getStyle($currencyRange)->getNumberFormat()->setFormatCode('#,##0');

    // Auto size
    foreach (range('A', 'M') as $columnID) {
        $sheet->getColumnDimension($columnID)->setAutoSize(true);
    }
}

    /**
     * Sanitiza una fecha recibida por input.
     * Si la fecha no es válida devuelve null (ignorar silenciosamente).
     * Acepta formatos legibles por Carbon::parse().
     */
    private function sanitizeDate($date)
    {
        if (empty($date)) {
            return null;
        }

        try {
            $d = \Carbon\Carbon::parse($date);
            return $d->toDateString();
        } catch (\Exception $e) {
            // Registrar error en logs para auditoría
            Log::warning('ReporteController::sanitizeDate - Fecha inválida: ' . $date . ' | Error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Normaliza order a 'asc' o 'desc'.
     */
    private function sanitizeOrder($order)
    {
        $o = strtolower((string)$order);
        return $o === 'asc' ? 'asc' : 'desc';
    }

    /**
     * Sanitiza el filtro estado: acepta SOLO 'completada' o 'anulada'.
     * Si no es uno de estos valores, devuelve null para que no se aplique filtro.
     */
    private function sanitizeEstado($estado)
    {
        if (empty($estado)) return null;
        $s = strtolower((string)$estado);
        // Whitelist: solo valores permitidos
        if (in_array($s, ['completada', 'anulada'], true)) {
            return $s;
        }
        return null;
    }

    /**
     * API: Obtiene datos de reportes en formato JSON para AJAX
     */
    public function apiData(Request $request)
    {
        $tipo = $request->input('tipo', 'ventas');
        $search = $request->input('search', '');  // ← Búsqueda server-side
        $fecha_inicio = $this->sanitizeDate($request->input('fecha_inicio'));
        $fecha_fin = $this->sanitizeDate($request->input('fecha_fin'));
        $page = max(1, (int)$request->input('page', 1));

        // Si no hay fechas, usar últimos 30 días
        if (!$fecha_inicio || !$fecha_fin) {
            $fecha_fin = date('Y-m-d');
            $fecha_inicio = date('Y-m-d', strtotime('-30 days'));
        }

        // Preparar filtros
        $filtros = [
            'fecha_inicio' => $fecha_inicio,
            'fecha_fin' => $fecha_fin,
            'estado' => null,
            'order' => 'desc',
            'search' => $search,  // ← Pasar búsqueda a filtros
        ];

        // Obtener datos según tipo
        if ($tipo === 'cajas') {
    // Cajas: LEFT JOIN con users dos veces para apertura y cierre
    $query = \DB::table('cajas as c')
        ->leftJoin('users as ua', 'ua.id', '=', 'c.user_id')
        ->leftJoin('users as uc', 'uc.id', '=', 'c.user_cierre_id')
        ->whereBetween('c.created_at', [$fecha_inicio . ' 00:00:00', $fecha_fin . ' 23:59:59'])
        ->when($search, function ($q) use ($search) {
            $term = '%' . strtolower($search) . '%';
            return $q->where(function($subQ) use ($term) {
                $subQ->whereRaw('LOWER(ua.name) LIKE ?', [$term])
                     ->orWhereRaw('LOWER(uc.name) LIKE ?', [$term])
                     ->orWhereRaw('LOWER(c.estado) LIKE ?', [$term]);
            });
        })
        ->select(
            'c.fecha_apertura',
            'c.fecha_cierre',
            \DB::raw('ua.name as user_apertura_name'),
            \DB::raw('uc.name as user_cierre_name'),
            'c.total_ventas',
            'c.total_efectivo',
            'c.monto_cierre_calculado',
            'c.monto_cierre_real',
            'c.diferencia',
            'c.estado'
        )
        ->orderBy('c.fecha_apertura', 'desc');

    $data = $query->paginate(15)->appends($request->query());
} elseif ($tipo === 'movimientos') {
            // Eager loading de producto + búsqueda server-side
            $query = \App\Models\InventarioMovimiento::with('producto')
                ->whereBetween('created_at', [$fecha_inicio . ' 00:00:00', $fecha_fin . ' 23:59:59'])
                ->when($search, function ($q) use ($search) {
                    // Buscar en nombre del producto o cantidad
                    return $q->whereHas('producto', function ($sq) use ($search) {
                        $sq->whereRaw('LOWER(nombre) LIKE ?', ['%' . strtolower($search) . '%']);
                    })->orWhereRaw('CAST(cantidad AS CHAR) LIKE ?', ['%' . $search . '%']);
                })
                ->orderBy('created_at', 'desc');
            $data = $query->paginate(15)->appends($request->query());  // ← Conservar parámetros
        } else {
            // Ventas con eager loading + búsqueda server-side
            $query = \App\Models\Venta::with('factura')
                ->whereBetween('created_at', [$fecha_inicio . ' 00:00:00', $fecha_fin . ' 23:59:59'])
                ->when($search, function ($q) use ($search) {
                    // Buscar en número de factura o cliente
                    return $q->whereHas('factura', function ($sq) use ($search) {
                        $sq->whereRaw('LOWER(numero) LIKE ?', ['%' . strtolower($search) . '%'])
                           ->orWhereRaw('LOWER(cliente_nombre) LIKE ?', ['%' . strtolower($search) . '%']);
                    });
                })
                ->orderBy('created_at', 'desc');
            $data = $query->paginate(15)->appends($request->query());  // ← Conservar parámetros
        }

        // Calcular estadísticas
        $stats = $this->calcularEstadisticas($tipo, $fecha_inicio, $fecha_fin);

        // Formatear datos para JSON
        $formattedData = $data->map(function($item) use ($tipo) {
            if ($tipo === 'movimientos') {
                return [
                    'id' => $item->id,
                    'created_at' => $item->created_at,
                    'producto_id' => $item->producto_id,
                    'producto_nombre' => optional($item->producto)->nombre ?? 'Producto #' . $item->producto_id,
                    'cantidad' => $item->cantidad,
                    'tipo' => $item->tipo,
                    'origen' => $item->origen,
                ];
            } elseif ($tipo === 'cajas') {
    // El query de cajas devuelve columnas con user_apertura_name y user_cierre_name
    return [
        'fecha_apertura' => $item->fecha_apertura,
        'fecha_cierre' => $item->fecha_cierre,
        'user_apertura_name' => $item->user_apertura_name,
        'user_cierre_name' => $item->user_cierre_name,
        'total_ventas' => isset($item->total_ventas) ? (float)$item->total_ventas : null,
        'total_efectivo' => isset($item->total_efectivo) ? (float)$item->total_efectivo : null,
        'monto_cierre_calculado' => isset($item->monto_cierre_calculado) ? (float)$item->monto_cierre_calculado : null,
        'monto_cierre_real' => isset($item->monto_cierre_real) ? (float)$item->monto_cierre_real : null,
        'diferencia' => isset($item->diferencia) ? (float)$item->diferencia : null,
        'estado' => $item->estado,
    ];
} else {
                return [
                    'id' => $item->id,
                    'fecha' => $item->created_at,
                    'factura_numero' => optional($item->factura)->numero,
                    'cliente_nombre' => optional($item->factura)->cliente_nombre,
                    'total' => $item->total,
                    'estado' => $item->estado,
                ];
            }
        });

        return response()->json([
            'success' => true,
            'data' => $formattedData,
            'stats' => $stats,
            'pagination' => [
                'current_page' => $data->currentPage(),
                'last_page' => $data->lastPage(),
                'total' => $data->total(),
                'per_page' => $data->perPage(),
            ]
        ]);
    }

    /**
     * API: Obtiene estadísticas independientes de forma global
     * Las estadísticas se calculan SIEMPRE considerando TODOS los tipos
     * Sin depender del filtro de tipo en la vista
     * Optimizado con query agregada (menos queries a BD)
     */
    public function apiStats(Request $request)
    {
        try {
            $fecha_inicio = $this->sanitizeDate($request->input('fecha_inicio'));
            $fecha_fin = $this->sanitizeDate($request->input('fecha_fin'));

            // Si no hay fechas, usar últimos 30 días
            if (!$fecha_inicio || !$fecha_fin) {
                $fecha_fin = date('Y-m-d');
                $fecha_inicio = date('Y-m-d', strtotime('-30 days'));
            }

            // Optimización: query única con agregación (1 query en lugar de 4)
            $inventarioStats = \DB::table('inventario_movimientos')
                ->whereBetween('created_at', [$fecha_inicio . ' 00:00:00', $fecha_fin . ' 23:59:59'])
                ->selectRaw('COUNT(*) as total, SUM(CASE WHEN tipo = ? THEN 1 ELSE 0 END) as entradas, SUM(CASE WHEN tipo = ? THEN 1 ELSE 0 END) as salidas', ['entrada', 'salida'])
                ->first();

            $ingresos = \App\Models\Venta::whereBetween('created_at', [$fecha_inicio . ' 00:00:00', $fecha_fin . ' 23:59:59'])->sum('total');

            return response()->json([
                'success' => true,
                'stats' => [
                    'movimientos' => (int)($inventarioStats->total ?? 0),
                    'entradas' => (int)($inventarioStats->entradas ?? 0),
                    'salidas' => (int)($inventarioStats->salidas ?? 0),
                    'ingresos' => (float)($ingresos ?? 0),
                ]
            ]);
        } catch (\Exception $e) {
            // En caso de error, retornar ceros sin exponer el error
            return response()->json([
                'success' => false,
                'stats' => [
                    'movimientos' => 0,
                    'entradas' => 0,
                    'salidas' => 0,
                    'ingresos' => 0,
                ]
            ], 200);
        }
    }

    /**
     * Calcula estadísticas para las tarjetas
     */
    private function calcularEstadisticas($tipo, $fecha_inicio, $fecha_fin)
    {
        if ($tipo === 'movimientos') {
            $movimientos = \App\Models\InventarioMovimiento::whereBetween('created_at', [$fecha_inicio . ' 00:00:00', $fecha_fin . ' 23:59:59'])->count();
            $entradas = \App\Models\InventarioMovimiento::whereBetween('created_at', [$fecha_inicio . ' 00:00:00', $fecha_fin . ' 23:59:59'])->where('tipo', 'entrada')->count();
            $salidas = \App\Models\InventarioMovimiento::whereBetween('created_at', [$fecha_inicio . ' 00:00:00', $fecha_fin . ' 23:59:59'])->where('tipo', 'salida')->count();
            $ingresos = 0;
        } else {
            $ventas = \App\Models\Venta::whereBetween('created_at', [$fecha_inicio . ' 00:00:00', $fecha_fin . ' 23:59:59']);
            $movimientos = $ventas->count();
            $entradas = 0;
            $salidas = 0;
            $ingresos = $ventas->sum('total');
        }

        return [
            'movimientos' => $movimientos,
            'entradas' => $entradas,
            'salidas' => $salidas,
            'ingresos' => $ingresos,
        ];
    }

    /**
     * API: Exporta datos en Excel
     */
    public function apiExport(Request $request)
    {
        $tipo = $request->input('tipo', 'ventas');
        $fecha_inicio = $this->sanitizeDate($request->input('fecha_inicio'));
        $fecha_fin = $this->sanitizeDate($request->input('fecha_fin'));

        // Si no hay fechas, usar últimos 30 días
        if (!$fecha_inicio || !$fecha_fin) {
            $fecha_fin = date('Y-m-d');
            $fecha_inicio = date('Y-m-d', strtotime('-30 days'));
        }

        // Obtener datos sin paginación (con límite de seguridad)
        if ($tipo === 'movimientos') {
            $movimientosQuery = \App\Models\InventarioMovimiento::whereBetween('created_at', [$fecha_inicio . ' 00:00:00', $fecha_fin . ' 23:59:59']);
            $count = (clone $movimientosQuery)->count();
            if ($count > self::MAX_EXPORT_ROWS) {
                return response()->json([
                    'success' => false,
                    'code' => 'EXPORT_LIMIT_EXCEEDED',
                    'message' => 'El reporte es demasiado grande para exportar. Reduzca el rango de fechas o contacte a soporte.'
                ], 413);
            }

            $data = $movimientosQuery
                ->with('producto')
                ->orderBy('created_at', 'desc')
                ->get();
        } elseif ($tipo === 'cajas') {
            $cajasQuery = \DB::table('cajas as c')
                ->leftJoin('users as ua', 'ua.id', '=', 'c.user_id')
                ->leftJoin('users as uc', 'uc.id', '=', 'c.user_cierre_id')
                ->whereBetween('c.fecha_apertura', [$fecha_inicio . ' 00:00:00', $fecha_fin . ' 23:59:59']);

            $count = (clone $cajasQuery)->count();
            if ($count > self::MAX_EXPORT_ROWS) {
                return response()->json([
                    'success' => false,
                    'code' => 'EXPORT_LIMIT_EXCEEDED',
                    'message' => 'El reporte es demasiado grande para exportar. Reduzca el rango de fechas o contacte a soporte.'
                ], 413);
            }

            $data = $cajasQuery
                ->select(
                    'c.fecha_apertura',
                    'c.fecha_cierre',
                    \DB::raw('ua.name as usuario_apertura'),
                    \DB::raw('uc.name as usuario_cierre'),
                    'c.estado',
                    'c.monto_apertura',
                    'c.total_ventas',
                    'c.total_efectivo',
                    'c.monto_cierre_calculado',
                    'c.monto_cierre_real',
                    'c.diferencia',
                    'c.nota_apertura',
                    'c.nota_cierre'
                )
                ->orderBy('c.fecha_apertura', 'desc')
                ->get();
        } else {
            $ventasQuery = \App\Models\Venta::whereBetween('created_at', [$fecha_inicio . ' 00:00:00', $fecha_fin . ' 23:59:59']);
            $detalleCount = \App\Models\VentaDetalle::whereHas('venta', function ($q) use ($fecha_inicio, $fecha_fin) {
                $q->whereBetween('created_at', [$fecha_inicio . ' 00:00:00', $fecha_fin . ' 23:59:59']);
            })->count();
            $ventasSinDetalles = (clone $ventasQuery)->doesntHave('detalles')->count();
            $count = $detalleCount + $ventasSinDetalles;
            if ($count > self::MAX_EXPORT_ROWS) {
                return response()->json([
                    'success' => false,
                    'code' => 'EXPORT_LIMIT_EXCEEDED',
                    'message' => 'El reporte es demasiado grande para exportar. Reduzca el rango de fechas o contacte a soporte.'
                ], 413);
            }

            $data = $ventasQuery
                ->with('detalles.producto', 'factura')
                ->orderBy('created_at', 'desc')
                ->get();
        }

        // Crear spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        if ($tipo === 'movimientos') {
            // Headers para movimientos
            $sheet->setCellValue('A1', 'ID');
            $sheet->setCellValue('B1', 'Producto');
            $sheet->setCellValue('C1', 'Tipo');
            $sheet->setCellValue('D1', 'Cantidad');
            $sheet->setCellValue('E1', 'Origen');
            $sheet->setCellValue('F1', 'Descripción');
            $sheet->setCellValue('G1', 'Fecha');

            // Aplicar estilos al header
            $this->aplicarEstilosHeader($sheet, ['A1', 'B1', 'C1', 'D1', 'E1', 'F1', 'G1']);

            $row = 2;
            foreach ($data as $item) {
                $sheet->setCellValue('A' . $row, $item->id);
                $sheet->setCellValue('B' . $row, optional($item->producto)->nombre);
                $sheet->setCellValue('C' . $row, $item->tipo);
                $sheet->setCellValue('D' . $row, $item->cantidad);
                $sheet->setCellValue('E' . $row, $item->origen);
                $sheet->setCellValue('F' . $row, $item->descripcion);
                $sheet->setCellValue('G' . $row, $item->created_at ? $item->created_at->format('d/m/Y H:i') : '');
                $row++;
            }
        } elseif ($tipo === 'cajas') {
            // Headers para cajas - ACTUALIZADO
            $headers = [
                'Fecha apertura',
                'Fecha cierre',
                'Usuario apertura',
                'Usuario cierre',
                'Estado',
                'Monto apertura',
                'Total ventas',
                'Total efectivo',
                'Monto cierre calculado',
                'Monto cierre real',
                'Diferencia',
                'Nota apertura',
                'Nota cierre'
            ];

            $col = 'A';
            $headerCells = [];
            foreach ($headers as $h) {
                $sheet->setCellValue($col . '1', $h);
                $headerCells[] = $col . '1';
                $col++;
            }
            $this->aplicarEstilosHeader($sheet, $headerCells);

            $row = 2;
            foreach ($data as $r) {
                $fechaA = $r->fecha_apertura ? (\Carbon\Carbon::parse($r->fecha_apertura)->format('d/m/Y H:i')) : '';
                $fechaC = $r->fecha_cierre ? (\Carbon\Carbon::parse($r->fecha_cierre)->format('d/m/Y H:i')) : 'Abierta';

                $sheet->setCellValue('A' . $row, $fechaA);
                $sheet->setCellValue('B' . $row, $fechaC);
                $sheet->setCellValue('C' . $row, $r->usuario_apertura ?? '-');
                $sheet->setCellValue('D' . $row, $r->usuario_cierre ?? '-');
                $sheet->setCellValue('E' . $row, $r->estado ?? '-');

                $sheet->setCellValue('F' . $row, $r->monto_apertura !== null ? (float)$r->monto_apertura : null);
                $sheet->setCellValue('G' . $row, $r->total_ventas !== null ? (float)$r->total_ventas : null);
                $sheet->setCellValue('H' . $row, $r->total_efectivo !== null ? (float)$r->total_efectivo : null);
                $sheet->setCellValue('I' . $row, $r->monto_cierre_calculado !== null ? (float)$r->monto_cierre_calculado : null);
                $sheet->setCellValue('J' . $row, $r->monto_cierre_real !== null ? (float)$r->monto_cierre_real : null);
                $sheet->setCellValue('K' . $row, $r->diferencia !== null ? (float)$r->diferencia : null);

                $sheet->setCellValue('L' . $row, $r->nota_apertura ?? '-');
                $sheet->setCellValue('M' . $row, $r->nota_cierre ?? '-');

                $row++;
            }

            // ACTUALIZADO: rango de formato ahora es F-K (porque agregamos columna)
            $sheet->getStyle('F2:K' . max(2, $row - 1))->getNumberFormat()->setFormatCode('#,##0');

        } else {
            // Headers para ventas con detalles (añadimos Vendedor y Rol)
            $headers = ['Venta ID', 'Fecha', 'N° Factura', 'Cliente', 'Vendedor', 'Rol', 'Producto', 'Cantidad', 'Precio Unitario', 'Subtotal', 'IVA', 'Total', 'Estado', 'Medio de pago', 'Motivo de anulación'];

            $col = 'A';
            $headerCells = [];
            foreach ($headers as $h) {
                $sheet->setCellValue($col . '1', $h);
                $headerCells[] = $col . '1';
                $col++;
            }

            // Aplicar estilos al header
            $this->aplicarEstilosHeader($sheet, $headerCells);

           $row = 2;
$totalVentas = 0;
$totalIva = 0;
$ventasCompletadas = 0;
// Precargar usuarios para evitar N+1
$userIds = $data->map(function($v) { return $v->user_id; })->filter()->unique()->values()->all();
$users = \App\Models\User::whereIn('id', $userIds)->get()->keyBy('id');

// Variables para coloreo alternado por factura
$facturaAnterior = null;
$colorActual = 0; // 0 = blanco, 1 = verde claro
$colores = [
    'FFFFFF', // Blanco
    'E8F5E9'  // Verde claro suave
];

foreach ($data as $venta) {
    $detalles = $venta->detalles ?? collect();
    $medioPago = optional($venta->factura)->forma_pago ?? '-';
    $facturaNumero = optional($venta->factura)->numero ?? '-';
    
    // Detectar cambio de factura para alternar color
    if ($facturaAnterior !== null && $facturaAnterior !== $facturaNumero) {
        $colorActual = ($colorActual + 1) % 2; // Alterna entre 0 y 1
    }
    $facturaAnterior = $facturaNumero;
                $motivoAnulacion = '-';
                if ($venta->estado === 'anulada') {
                    $motivoAnulacion = optional($detalles->first())->motivo_anulacion ?? '-';
                }
                if ($detalles->isEmpty()) {
                    // Si no hay detalles, mostrar solo la venta (llenar columnas de detalle con valores por defecto)
                    $ventaUser = $users->get($venta->user_id);
                    $vendedorNombre = optional($ventaUser)->name ?? '-';
                    $vendedorRol = optional($ventaUser)->role ?? '-';

                    $values = [];
                    $values[] = $venta->id;
                    $values[] = $venta->created_at ? $venta->created_at->format('Y-m-d H:i') : '';
                    $values[] = optional($venta->factura)->numero;
                    $values[] = optional($venta->factura)->cliente_nombre;
                    $values[] = $vendedorNombre;
                    $values[] = $vendedorRol;
                    $values[] = '-'; // Producto
                    $values[] = 0;   // Cantidad
                    $values[] = '';  // Precio Unitario
                    $values[] = '';  // Subtotal
                    $values[] = 0;   // IVA
                    $values[] = $venta->total;
                    $values[] = $venta->estado;
                    $values[] = $medioPago;
                    $values[] = $motivoAnulacion;

                    $col = 'A';
foreach ($values as $val) {
    $sheet->setCellValue($col . $row, $val);
    $col++;
}

// Aplicar color de fondo a toda la fila
$sheet->getStyle('A' . $row . ':O' . $row)
      ->getFill()
      ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
      ->getStartColor()
      ->setRGB($colores[$colorActual]);

$row++;

                    if ($venta->estado === 'completada') {
                        $totalVentas += $venta->total;
                        $ventasCompletadas++;
                    }
                } else {
                    // Mostrar cada detalle en una fila
                    $ivaVenta = 0;
                    $ventaUser = $users->get($venta->user_id);
                    $vendedorNombre = optional($ventaUser)->name ?? '-';
                    $vendedorRol = optional($ventaUser)->role ?? '-';
                    foreach ($detalles as $detalle) {
                        $values = [];
                        $values[] = $venta->id;
                        $values[] = $venta->created_at ? $venta->created_at->format('Y-m-d H:i') : '';
                        $values[] = optional($venta->factura)->numero;
                        $values[] = optional($venta->factura)->cliente_nombre;
                        $values[] = $vendedorNombre;
                        $values[] = $vendedorRol;
                        $values[] = optional($detalle->producto)->nombre ?? 'Producto #' . $detalle->producto_id;
                        $values[] = $detalle->cantidad;
                        $values[] = $detalle->precio_unitario;
                        $values[] = $detalle->subtotal;
                        $values[] = $detalle->iva ?? 0;
                        $values[] = $venta->total;
                        $values[] = $venta->estado;
                        $values[] = $medioPago;
                        $values[] = $motivoAnulacion;

                        $col = 'A';
                        foreach ($values as $val) {
                            $sheet->setCellValue($col . $row, $val);
                            $col++;
                        }

                        $ivaVenta += ($detalle->iva ?? 0);
                        $row++;
                    }

                    if ($venta->estado === 'completada') {
                        $totalVentas += $venta->total;
                        $totalIva += $ivaVenta;
                        $ventasCompletadas++;
                    }
                }
            }

            // Agregar fila de resumen
            $row += 1;
            $sheet->setCellValue('A' . $row, 'RESUMEN');
            $sheet->getStyle('A' . $row)->getFont()->setBold(true);
            $sheet->getStyle('A' . $row)->getFill()->setFillType('solid')->getStartColor()->setRGB('E8E8E8');

            $row += 1;
            $sheet->setCellValue('A' . $row, 'Total Ventas Completadas:');
            $sheet->setCellValue('B' . $row, $ventasCompletadas);
            $sheet->getStyle('A' . $row)->getFont()->setBold(true);

            $row += 1;
            $sheet->setCellValue('A' . $row, 'Total Ingresos:');
            $sheet->setCellValue('B' . $row, $totalVentas);
            $sheet->getStyle('A' . $row)->getFont()->setBold(true);
            $sheet->getStyle('B' . $row)->getNumberFormat()->setFormatCode('#,##0.00');

            $row += 1;
            $sheet->setCellValue('A' . $row, 'Total IVA Recolectado:');
            $sheet->setCellValue('B' . $row, $totalIva);
            $sheet->getStyle('A' . $row)->getFont()->setBold(true);
            $sheet->getStyle('B' . $row)->getNumberFormat()->setFormatCode('#,##0.00');

            $row += 1;
            $baseImponible = $totalVentas - $totalIva;
            $sheet->setCellValue('A' . $row, 'Base Imponible:');
            $sheet->setCellValue('B' . $row, $baseImponible);
            $sheet->getStyle('A' . $row)->getFont()->setBold(true);
            $sheet->getStyle('B' . $row)->getNumberFormat()->setFormatCode('#,##0.00');

        }

        // Auto-size columns
        foreach ($sheet->getColumnDimensions() as $col) {
            $col->setAutoSize(true);
        }

        // Crear archivo temporal
        $writer = new Xlsx($spreadsheet);
        $nombreTipo = ($tipo === 'movimientos') ? 'inventario' : (($tipo === 'cajas') ? 'cajas' : 'venta');
        $fileName = 'reporte_' . $nombreTipo . '.xlsx';

        return response()->stream(function() use ($writer) {
            $writer->save('php://output');
        }, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"'
        ]);
    }

    /**
     * Helper: Aplica estilos a headers
     */
    private function aplicarEstilosHeader($sheet, $cells)
    {
        foreach ($cells as $cell) {
            $sheet->getStyle($cell)->getFont()->setBold(true);
            $sheet->getStyle($cell)->getFill()->setFillType('solid')->getStartColor()->setRGB('4472C4');
            $sheet->getStyle($cell)->getFont()->getColor()->setRGB('FFFFFF');
            $sheet->getStyle($cell)->getAlignment()->setHorizontal('center');
        }
    }
}