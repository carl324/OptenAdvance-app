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
                $count = Reporte::ventas($filtros)->count();
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

  
    private function exportVentasCompletas($sheet, $filtros, $empresa)
    {
        $sheet->setTitle('Ventas Completas');
        
     
        $headers = ['Fecha', 'Venta ID', 'N° Factura', 'Cliente', 'Vendedor', 'Rol', 'Total', 'Estado', 'Medio de pago'];

        // Verificar si hay datos históricos con IVA > 0
        $ventas = Reporte::ventas($filtros)->get();
        $hayIvaHistorico = false;

        // Mostrar columna IVA si la empresa cobra IVA O si hay datos históricos con IVA
        $mostrarIva = ($empresa && $empresa->cobra_iva) || $hayIvaHistorico;

        if ($mostrarIva) {
            $headers[] = 'IVA';
        }
        
        $col = 'A';
        foreach ($headers as $h) {
            $sheet->setCellValue($col . '1', $h);
            $sheet->getStyle($col . '1')->getFont()->setBold(true);
            $sheet->getStyle($col . '1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $col++;
        }

        $row = 2;
        $totalIva = 0;
        $totalTotal = 0;

// Cargar usuarios relacionados en una sola consulta para evitar N+1
$userIds = $ventas->pluck('user_id')->filter()->unique()->values()->all();
$users = \App\Models\User::whereIn('id', $userIds)->get()->keyBy('id');

foreach ($ventas as $venta) {
    $fecha = optional($venta->created_at)->format('Y-m-d H:i');
    $facturaNumero = optional($venta->factura)->numero ?? '-';
    $cliente = (optional($venta->factura)->cliente_nombre ?? $venta->cliente) ?? '-';
    $medioPago = optional($venta->factura)->forma_pago ?? '-';
    
    $ventaUserId = $venta->user_id;
    $vendedor = $users->get($ventaUserId);
    $vendedorNombre = optional($vendedor)->name ?? '-';
    $vendedorRol = optional($vendedor)->role ?? '-';
    
    $values = [];
    $values[] = $fecha;
    $values[] = $venta->id;
    $values[] = $facturaNumero;
    $values[] = $cliente;
    $values[] = $vendedorNombre;
    $values[] = $vendedorRol;
    $values[] = (float)$venta->total;
    $values[] = $venta->estado ?? '-';
    $values[] = $medioPago;
    
    if ($mostrarIva) {
        $values[] = (float)($venta->iva ?? 0);
    }
    
    $col = 'A';
    foreach ($values as $val) {
        $sheet->setCellValue($col . $row, $val);
        $col++;
    }
    
    $totalTotal += (float)$venta->total;
    $totalIva += (float)($venta->iva ?? 0);
    
    $row++;
}

        // Totales: ubicar columnas dinámicamente según headers
        $idxCliente = array_search('Cliente', $headers);
        $idxIva = array_search('IVA', $headers);
        $idxTotal = array_search('Total', $headers);

        if ($idxCliente !== false) {
            $colCliente = Coordinate::stringFromColumnIndex($idxCliente + 1);
            $sheet->setCellValue($colCliente . $row, 'TOTALES');
            $sheet->getStyle($colCliente . $row)->getFont()->setBold(true);
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
        $firstNumIdx = $idxTotal !== false ? $idxTotal + 1 : null;
        $lastNumIdx = $idxTotal !== false ? $idxTotal + 1 : null;
        if ($firstNumIdx && $lastNumIdx) {
            $sheet->getStyle(Coordinate::stringFromColumnIndex($firstNumIdx) . $row . ':' . Coordinate::stringFromColumnIndex($lastNumIdx) . $row)->getFont()->setBold(true);
        }

        // Formato de moneda: aplicar a Total e IVA si existe
        $currencyFormat = '"$"#,##0';
        $idxTotal = array_search('Total', $headers);
        if ($idxTotal !== false) {
            $colTotal = Coordinate::stringFromColumnIndex($idxTotal + 1);
            $sheet->getStyle($colTotal . '2:' . $colTotal . $row)->getNumberFormat()->setFormatCode($currencyFormat);
        }
        if ($mostrarIva) {
            $lastColIdx = count($headers);
            $colIva = Coordinate::stringFromColumnIndex($lastColIdx);
            $sheet->getStyle($colIva . '2:' . $colIva . $row)->getNumberFormat()->setFormatCode($currencyFormat);
        }

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
            
            $m->origen_reporte = 'inventario';
      
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
        'Total ingresos',
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

  
    private function sanitizeOrder($order)
    {
        $o = strtolower((string)$order);
        return $o === 'asc' ? 'asc' : 'desc';
    }


    private function sanitizeEstado($estado)
    {
        if (empty($estado)) return null;
        $s = strtolower((string)$estado);
        if (in_array($s, ['completada', 'anulada'], true)) {
            return $s;
        }
        return null;
    }

/**
 * API: Retorna KPIs del mes actual vs mes anterior
 */
public function apiKpis(Request $request)
{
    try {
        $hoy = \Carbon\Carbon::now();
        $inicioMesActual = $hoy->copy()->startOfMonth()->toDateString();
        $finMesActual = $hoy->copy()->toDateString();
        $inicioMesAnterior = $hoy->copy()->subMonth()->startOfMonth()->toDateString();
        $finMesAnterior = $hoy->copy()->subMonth()->endOfMonth()->toDateString();

        // Mes actual
        $ventasActual = \App\Models\Venta::whereBetween('created_at', [$inicioMesActual . ' 00:00:00', $finMesActual . ' 23:59:59'])
            ->where('estado', '!=', 'anulada');

        $totalActual   = (clone $ventasActual)->sum('total');
        $countActual   = (clone $ventasActual)->count();
        $ticketActual  = $countActual > 0 ? $totalActual / $countActual : 0;

        // Ganancia actual: JOIN con ventas_detalle y productos
        $gananciaActual = \DB::table('ventas_detalle as vd')
            ->join('ventas as v', 'v.id', '=', 'vd.venta_id')
            ->join('productos as p', 'p.id', '=', 'vd.producto_id')
            ->whereBetween('v.created_at', [$inicioMesActual . ' 00:00:00', $finMesActual . ' 23:59:59'])
            ->where('v.estado', '!=', 'anulada')
            ->selectRaw('SUM((vd.precio_unitario - p.precio_compra) * vd.cantidad) as ganancia')
            ->value('ganancia') ?? 0;

        // Mes anterior (para comparativo)
        $ventasAnterior = \App\Models\Venta::whereBetween('created_at', [$inicioMesAnterior . ' 00:00:00', $finMesAnterior . ' 23:59:59'])
            ->where('estado', '!=', 'anulada');

        $totalAnterior  = (clone $ventasAnterior)->sum('total');
        $countAnterior  = (clone $ventasAnterior)->count();
        $ticketAnterior = $countAnterior > 0 ? $totalAnterior / $countAnterior : 0;

        $gananciaAnterior = \DB::table('ventas_detalle as vd')
            ->join('ventas as v', 'v.id', '=', 'vd.venta_id')
            ->join('productos as p', 'p.id', '=', 'vd.producto_id')
            ->whereBetween('v.created_at', [$inicioMesAnterior . ' 00:00:00', $finMesAnterior . ' 23:59:59'])
            ->where('v.estado', '!=', 'anulada')
            ->selectRaw('SUM((vd.precio_unitario - p.precio_compra) * vd.cantidad) as ganancia')
            ->value('ganancia') ?? 0;

        // Helper para calcular variación %
        $variacion = function($actual, $anterior) {
            if ($anterior == 0) return null;
            return round((($actual - $anterior) / $anterior) * 100, 1);
        };

        return response()->json([
            'success' => true,
            'kpis' => [
                'total_vendido'    => (float) $totalActual,
                'num_ventas'       => (int)   $countActual,
                'ticket_promedio'  => (float) $ticketActual,
                'ganancia'         => (float) $gananciaActual,
            ],
            'variaciones' => [
                'total_vendido'    => $variacion($totalActual,   $totalAnterior),
                'num_ventas'       => $variacion($countActual,   $countAnterior),
                'ticket_promedio'  => $variacion($ticketActual,  $ticketAnterior),
                'ganancia'         => $variacion($gananciaActual,$gananciaAnterior),
            ],
            'periodo' => $hoy->locale('es')->monthName . ' ' . $hoy->year,
        ]);
    } catch (\Exception $e) {
        Log::error('apiKpis: ' . $e->getMessage());
        return response()->json(['success' => false], 500);
    }
}


public function apiTendencia(Request $request)
{
    try {
        $agrupacion = in_array($request->input('agrupacion'), ['diario', 'semanal', 'mensual'])
            ? $request->input('agrupacion')
            : 'mensual';

        $hoy = \Carbon\Carbon::now();

        // Definir rango según agrupación
        switch ($agrupacion) {
            case 'diario':
                $inicio = $hoy->copy()->startOfDay();
                $fin    = $hoy->copy()->endOfDay();
                $format = '%H:00'; // agrupar por hora
                break;
            case 'semanal':
                $inicio = $hoy->copy()->startOfWeek(\Carbon\Carbon::MONDAY);
                $fin    = $hoy->copy()->endOfWeek(\Carbon\Carbon::SUNDAY);
                $format = '%Y-%m-%d'; // agrupar por día
                break;
            default: // mensual
                $inicio = $hoy->copy()->startOfMonth();
                $fin    = $hoy->copy()->endOfMonth();
                $format = '%Y-%m-%d'; // agrupar por día
                break;
        }

        $datos = \DB::table('ventas')
            ->whereBetween('created_at', [$inicio, $fin])
            ->where('estado', '!=', 'anulada')
            ->selectRaw("DATE_FORMAT(created_at, '{$format}') as periodo, SUM(total) as total, COUNT(*) as cantidad")
            ->groupBy('periodo')
            ->orderBy('periodo')
            ->get();

        return response()->json([
            'success'     => true,
            'agrupacion'  => $agrupacion,
            'labels'      => $datos->pluck('periodo'),
            'totales'     => $datos->pluck('total')->map(fn($v) => (float)$v),
            'cantidades'  => $datos->pluck('cantidad')->map(fn($v) => (int)$v),
        ]);
    } catch (\Exception $e) {
        Log::error('apiTendencia: ' . $e->getMessage());
        return response()->json(['success' => false], 500);
    }
}

/**
 * API: Datos para la gráfica de ventas por cajero
 */
public function apiCajeros(Request $request)
{
    try {
        $agrupacion = in_array($request->input('agrupacion'), ['diario', 'semanal', 'mensual'])
            ? $request->input('agrupacion')
            : 'mensual';

        $hoy = \Carbon\Carbon::now();

        switch ($agrupacion) {
            case 'diario':
                $inicio = $hoy->copy()->startOfDay();
                $fin    = $hoy->copy()->endOfDay();
                break;
            case 'semanal':
                $inicio = $hoy->copy()->startOfWeek(\Carbon\Carbon::MONDAY);
                $fin    = $hoy->copy()->endOfWeek(\Carbon\Carbon::SUNDAY);
                break;
            default:
                $inicio = $hoy->copy()->startOfMonth();
                $fin    = $hoy->copy()->endOfMonth();
                break;
        }

       $cajeros = \DB::table('ventas as v')
    ->join('users as u', 'u.id', '=', 'v.user_id')
    ->whereBetween('v.created_at', [$inicio, $fin])
    ->selectRaw("
        u.name as cajero,
        SUM(CASE WHEN v.estado != 'anulada' THEN v.total ELSE 0 END) as total_ventas,
        COUNT(CASE WHEN v.estado != 'anulada' THEN 1 END) as num_ventas
    ")
    ->groupBy('u.id', 'u.name')
    ->orderByDesc('total_ventas')
    ->get();

        $total = count($cajeros);

        return response()->json([
            'success'  => true,
            'total'    => $total,
            'cajeros'  => $cajeros->take(5)->map(fn($c) => [
                'cajero'       => $c->cajero,
                'total_ventas' => (float) $c->total_ventas,
                'num_ventas'   => (int)   $c->num_ventas,
            ]),
            'hay_mas'  => $total > 5,
        ]);
    } catch (\Exception $e) {
        Log::error('apiCajeros: ' . $e->getMessage());
        return response()->json(['success' => false], 500);
    }
}

/**
 * API: Top 7 productos más vendidos
 */
public function apiProductos(Request $request)
{
    try {
        $agrupacion = in_array($request->input('agrupacion'), ['diario', 'semanal', 'mensual'])
            ? $request->input('agrupacion')
            : 'mensual';

        $hoy = \Carbon\Carbon::now();

        switch ($agrupacion) {
            case 'diario':
                $inicio = $hoy->copy()->startOfDay();
                $fin    = $hoy->copy()->endOfDay();
                break;
            case 'semanal':
                $inicio = $hoy->copy()->startOfWeek(\Carbon\Carbon::MONDAY);
                $fin    = $hoy->copy()->endOfWeek(\Carbon\Carbon::SUNDAY);
                break;
            default:
                $inicio = $hoy->copy()->startOfMonth();
                $fin    = $hoy->copy()->endOfMonth();
                break;
        }

        $productos = \DB::table('ventas_detalle as vd')
            ->join('ventas as v',    'v.id', '=', 'vd.venta_id')
            ->join('productos as p', 'p.id', '=', 'vd.producto_id')
            ->whereBetween('v.created_at', [$inicio, $fin])
            ->where('v.estado', '!=', 'anulada')
            ->selectRaw('
                p.nombre,
                p.precio_venta,
                SUM(vd.cantidad) as unidades,
                SUM(vd.precio_unitario * vd.cantidad) as total_vendido,
                SUM((vd.precio_unitario - p.precio_compra) * vd.cantidad) as ganancia
            ')
            ->groupBy('p.id', 'p.nombre', 'p.precio_venta')
            ->orderByDesc('unidades')
            ->limit(7)
            ->get();

return response()->json([
    'success'   => true,
    'productos' => $productos->map(fn($p) => [
        'nombre'        => $p->nombre,
        'precio_venta'  => (float) $p->precio_venta,
        'unidades'      => (int)   $p->unidades,
        'total_vendido' => (float) $p->total_vendido,
        'ganancia'      => (float) $p->ganancia,
    ]),
    'productos_activos' => \App\Models\Producto::activos()->count(), // 👈
]);
    } catch (\Exception $e) {
        Log::error('apiProductos: ' . $e->getMessage());
        return response()->json(['success' => false], 500);
    }
}
public function index(Request $request)
{
    return view('reportes.index');
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
            $count = (clone $ventasQuery)->count();
            if ($count > self::MAX_EXPORT_ROWS) {
                return response()->json([
                    'success' => false,
                    'code' => 'EXPORT_LIMIT_EXCEEDED',
                    'message' => 'El reporte es demasiado grande para exportar. Reduzca el rango de fechas o contacte a soporte.'
                ], 413);
            }

           $data = $ventasQuery
    ->with(['factura', 'detalles'])
    ->orderBy('fecha', 'desc')
    ->get();

// Cargar montos devueltos para dev_parcial en una sola query
$devParcialIds = $data->where('estado', 'dev_parcial')->pluck('id');
$montosDevueltos = \DB::table('devoluciones')
    ->whereIn('venta_id', $devParcialIds)
    ->selectRaw('venta_id, SUM(monto_real) as total_devuelto')
    ->groupBy('venta_id')
    ->pluck('total_devuelto', 'venta_id');
        }

     
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
                'Total ingresos',
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
            // Headers para ventas sin detalles expandidos
           $headers = ['Venta ID', 'Fecha', 'N° Factura', 'Cliente', 'Vendedor', 'Rol', 'Total', 'Estado', 'Medio de pago'];

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
$totalIngresos = 0;
$totalVentasContadas = 0;

$userIds = $data->map(fn($v) => $v->user_id)->filter()->unique()->values()->all();
$users = \App\Models\User::whereIn('id', $userIds)->get()->keyBy('id');

foreach ($data as $venta) {
    $medioPago     = optional($venta->factura)->forma_pago ?? '-';
    $facturaNumero = optional($venta->factura)->numero ?? '-';
    $ventaUser     = $users->get($venta->user_id);

    switch ($venta->estado) {
        case 'completada':
            $ingresoReal = $venta->total;
            break;
        case 'credito':
        case 'parcial':
            $ingresoReal = $venta->total - $venta->saldo_pendiente;
            break;
        case 'dev_parcial':
            $ingresoReal = $venta->total - ($montosDevueltos[$venta->id] ?? 0);
            break;
        default:
            $ingresoReal = 0;
    }

    $values = [
        $venta->id,
        $venta->fecha ? $venta->fecha->format('d/m/Y H:i') : '',
        $facturaNumero,
        optional($venta->factura)->cliente_nombre ?? '-',
        optional($ventaUser)->name ?? '-',
        optional($ventaUser)->role ?? '-',
        $venta->total,
        $venta->estado,
        $medioPago,
    ];

    $col = 'A';
    foreach ($values as $val) {
        $sheet->setCellValue($col . $row, $val);
        $col++;
    }

    if (!in_array($venta->estado, ['anulada', 'devuelta'])) {
        $totalIngresos += $ingresoReal;
        $totalVentasContadas++;
    }

    $row++;

}

            // Agregar fila de resumen
           $row += 1;
$sheet->setCellValue('A' . $row, 'RESUMEN');
$sheet->getStyle('A' . $row)->getFont()->setBold(true);
$sheet->getStyle('A' . $row)->getFill()->setFillType('solid')->getStartColor()->setRGB('E8E8E8');

$row += 1;
$sheet->setCellValue('A' . $row, 'Total Ventas:');
$sheet->setCellValue('B' . $row, $totalVentasContadas);
$sheet->getStyle('A' . $row)->getFont()->setBold(true);

$row += 1;
$sheet->setCellValue('A' . $row, 'Total Ingresos:');
$sheet->setCellValue('B' . $row, $totalIngresos);
$sheet->getStyle('A' . $row)->getFont()->setBold(true);
$sheet->getStyle('B' . $row)->getNumberFormat()->setFormatCode('#,##0.00');

        }


        foreach ($sheet->getColumnDimensions() as $col) {
            $col->setAutoSize(true);
        }

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