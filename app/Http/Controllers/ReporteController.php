<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reporte;
use App\Models\Empresa;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

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
                    return response('La exportación contiene ' . $count . " filas, excede el límite de exportación (" . self::MAX_EXPORT_ROWS . "). Reduce el rango de fechas o aplica filtros más específicos.", 413);
                }

                $this->exportVentasCompletas($sheet, $filtros, $empresa);
                $filename = 'reporte_ventas_completo';
                break;
            
            case 'inventario_movimientos':
                $count = Reporte::inventarioMovimientos($filtros)->count();
                if ($count > self::MAX_EXPORT_ROWS) {
                    return response('La exportación contiene ' . $count . " filas, excede el límite de exportación (" . self::MAX_EXPORT_ROWS . "). Reduce el rango de fechas o aplica filtros más específicos.", 413);
                }

                $this->exportInventarioMovimientos($sheet, $filtros);
                $filename = 'reporte_inventario_movimientos';
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
        
        // Headers
        $headers = ['Fecha', 'Venta ID', 'N° Factura', 'Cliente', 'Producto', 'Cantidad', 'Precio Unit.', 'Subtotal'];

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

        foreach ($detalles as $d) {
            // Origen del registro (solo para UI / export)
            $d->origen_reporte = 'venta_detalle';
            $fecha = optional($d->venta->fecha)->format('Y-m-d H:i');
            $facturaNumero = optional($d->venta->factura)->numero ?? '-';
            $cliente = (optional($d->venta->factura)->cliente_nombre ?? $d->venta->cliente) ?? '-';
            $producto = optional($d->producto)->nombre ?? '#' . $d->producto_id;
            // Usar subtotal directamente (ya incluye IVA desde BD)
            $total = $d->subtotal;

            $sheet->setCellValue('A' . $row, $fecha);
            $sheet->setCellValue('B' . $row, $d->venta_id);
            $sheet->setCellValue('C' . $row, $facturaNumero);
            $sheet->setCellValue('D' . $row, $cliente);
            $sheet->setCellValue('E' . $row, $producto);
            $sheet->setCellValue('F' . $row, (float)$d->cantidad);
            $sheet->setCellValue('G' . $row, (float)$d->precio_unitario);
            $sheet->setCellValue('H' . $row, (float)$d->subtotal);

            $currentCol = 'I';
            if ($mostrarIva) {
                $sheet->setCellValue($currentCol . $row, (float)$d->iva);
                $currentCol++;
            }

            $sheet->setCellValue($currentCol . $row, (float)$total);
            $currentCol++;
            $sheet->setCellValue($currentCol . $row, $d->venta->estado ?? '-');

            $totalCantidad += (float)$d->cantidad;
            $totalSubtotal += (float)$d->subtotal;
            $totalIva += (float)($d->iva ?? 0);
            $totalTotal += (float)$total;

            $row++;
        }

        // Totales
        $sheet->setCellValue('E' . $row, 'TOTALES');
        $sheet->getStyle('E' . $row)->getFont()->setBold(true);
        $sheet->setCellValue('F' . $row, $totalCantidad);
        $sheet->setCellValue('H' . $row, $totalSubtotal);

        $currentCol = 'I';
        if ($mostrarIva) {
            $sheet->setCellValue($currentCol . $row, $totalIva);
            $currentCol++;
        }
        $sheet->setCellValue($currentCol . $row, $totalTotal);
        
        $sheet->getStyle('F' . $row . ':' . $currentCol . $row)->getFont()->setBold(true);

        // Formato de moneda
        $currencyFormat = '"$"#,##0';
        $sheet->getStyle('G2:H' . $row)->getNumberFormat()->setFormatCode($currencyFormat);
        
        if ($mostrarIva) {
            $sheet->getStyle('I2:J' . $row)->getNumberFormat()->setFormatCode($currencyFormat);
        } else {
            $sheet->getStyle('I2:I' . $row)->getNumberFormat()->setFormatCode($currencyFormat);
        }

        // Auto size
        $lastCol = $mostrarIva ? 'K' : 'J';
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
        ];

        // Obtener datos según tipo
        if ($tipo === 'movimientos') {
            $query = \App\Models\InventarioMovimiento::whereBetween('created_at', [$fecha_inicio . ' 00:00:00', $fecha_fin . ' 23:59:59'])
                ->orderBy('created_at', 'desc');
            $data = $query->paginate(15);
        } else {
            // ventas con eager loading para evitar N+1
            $query = \App\Models\Venta::with('factura')
                ->whereBetween('created_at', [$fecha_inicio . ' 00:00:00', $fecha_fin . ' 23:59:59'])
                ->orderBy('created_at', 'desc');
            $data = $query->paginate(15);
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
                    'producto_nombre' => $item->producto_nombre,
                    'cantidad' => $item->cantidad,
                    'tipo' => $item->tipo,
                    'origen' => $item->origen,
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
            $data = \App\Models\InventarioMovimiento::whereBetween('created_at', [$fecha_inicio . ' 00:00:00', $fecha_fin . ' 23:59:59'])
                ->orderBy('created_at', 'desc')
                ->limit(self::MAX_EXPORT_ROWS)
                ->get();
        } else {
            $data = \App\Models\Venta::with('detalles.producto', 'factura')
                ->whereBetween('created_at', [$fecha_inicio . ' 00:00:00', $fecha_fin . ' 23:59:59'])
                ->orderBy('created_at', 'desc')
                ->limit(self::MAX_EXPORT_ROWS)
                ->get();
        }

        // Crear spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        if ($tipo === 'movimientos') {
            // Headers para movimientos
            $sheet->setCellValue('A1', 'ID');
            $sheet->setCellValue('B1', 'Fecha');
            $sheet->setCellValue('C1', 'Producto');
            $sheet->setCellValue('D1', 'Cantidad');
            $sheet->setCellValue('E1', 'Tipo');
            $sheet->setCellValue('F1', 'Origen');

            // Aplicar estilos al header
            $this->aplicarEstilosHeader($sheet, ['A1', 'B1', 'C1', 'D1', 'E1', 'F1']);

            $row = 2;
            foreach ($data as $item) {
                $sheet->setCellValue('A' . $row, $item->id);
                $sheet->setCellValue('B' . $row, $item->created_at ? $item->created_at->format('Y-m-d H:i') : '');
                $sheet->setCellValue('C' . $row, $item->producto_nombre);
                $sheet->setCellValue('D' . $row, $item->cantidad);
                $sheet->setCellValue('E' . $row, $item->tipo);
                $sheet->setCellValue('F' . $row, $item->origen);
                $row++;
            }
        } else {
            // Headers para ventas con detalles
            $sheet->setCellValue('A1', 'Venta ID');
            $sheet->setCellValue('B1', 'Fecha');
            $sheet->setCellValue('C1', 'N° Factura');
            $sheet->setCellValue('D1', 'Cliente');
            $sheet->setCellValue('E1', 'Producto');
            $sheet->setCellValue('F1', 'Cantidad');
            $sheet->setCellValue('G1', 'Precio Unitario');
            $sheet->setCellValue('H1', 'Subtotal');
            $sheet->setCellValue('I1', 'IVA');
            $sheet->setCellValue('J1', 'Total');
            $sheet->setCellValue('K1', 'Estado');

            // Aplicar estilos al header
            $this->aplicarEstilosHeader($sheet, ['A1', 'B1', 'C1', 'D1', 'E1', 'F1', 'G1', 'H1', 'I1', 'J1', 'K1']);

            $row = 2;
            $totalVentas = 0;
            $totalIva = 0;
            $ventasCompletadas = 0;
            
            foreach ($data as $venta) {
                $detalles = $venta->detalles ?? collect();
                
                if ($detalles->isEmpty()) {
                    // Si no hay detalles, mostrar solo la venta
                    $sheet->setCellValue('A' . $row, $venta->id);
                    $sheet->setCellValue('B' . $row, $venta->created_at ? $venta->created_at->format('Y-m-d H:i') : '');
                    $sheet->setCellValue('C' . $row, optional($venta->factura)->numero);
                    $sheet->setCellValue('D' . $row, optional($venta->factura)->cliente_nombre);
                    $sheet->setCellValue('J' . $row, $venta->total);
                    $sheet->setCellValue('K' . $row, $venta->estado);
                    $row++;
                    
                    if ($venta->estado === 'completada') {
                        $totalVentas += $venta->total;
                        $ventasCompletadas++;
                    }
                } else {
                    // Mostrar cada detalle en una fila
                    $ivaVenta = 0;
                    foreach ($detalles as $detalle) {
                        $sheet->setCellValue('A' . $row, $venta->id);
                        $sheet->setCellValue('B' . $row, $venta->created_at ? $venta->created_at->format('Y-m-d H:i') : '');
                        $sheet->setCellValue('C' . $row, optional($venta->factura)->numero);
                        $sheet->setCellValue('D' . $row, optional($venta->factura)->cliente_nombre);
                        $sheet->setCellValue('E' . $row, optional($detalle->producto)->nombre ?? 'Producto #' . $detalle->producto_id);
                        $sheet->setCellValue('F' . $row, $detalle->cantidad);
                        $sheet->setCellValue('G' . $row, $detalle->precio_unitario);
                        $sheet->setCellValue('H' . $row, $detalle->subtotal);
                        $sheet->setCellValue('I' . $row, $detalle->iva ?? 0);
                        $sheet->setCellValue('J' . $row, $venta->total);
                        $sheet->setCellValue('K' . $row, $venta->estado);
                        
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
        $nombreTipo = ($tipo === 'movimientos') ? 'inventario' : 'venta';
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
