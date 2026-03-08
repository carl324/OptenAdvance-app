<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Reporte extends Model
{
    // Este modelo no tiene tabla propia, solo maneja consultas
    // Nota: los datos consultados por este modelo representan registros históricos
    // es_dato_historico = true (flag informativo, no afecta consultas)
    protected $table = null;

    /**
     * Obtiene el reporte de ventas con factura
     * 
     * @param array $filtros ['fecha_inicio', 'fecha_fin', 'estado', 'order']
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function ventas(array $filtros = [])
    {
        $query = Venta::with('factura')->select('ventas.*');

        // Aplicar filtros de fecha
        if (!empty($filtros['fecha_inicio'])) {
            $start = Carbon::parse($filtros['fecha_inicio'])->startOfDay();
            $query->where('fecha', '>=', $start);
        }

        if (!empty($filtros['fecha_fin'])) {
            $end = Carbon::parse($filtros['fecha_fin'])->endOfDay();
            $query->where('fecha', '<=', $end);
        }

        // Filtro por estado
        if (!empty($filtros['estado'])) {
            $query->where('estado', $filtros['estado']);
        }

        // Orden
        $order = strtolower($filtros['order'] ?? 'desc') === 'asc' ? 'asc' : 'desc';
        $query->orderBy('fecha', $order);

        return $query;
    }

    /**
     * Obtiene el reporte detallado de ventas (ventas + ventas_detalle)
     * Combina información de ambas tablas en una sola vista
     * 
     * @param array $filtros ['fecha_inicio', 'fecha_fin', 'estado', 'order']
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function ventasDetalle(array $filtros = [])
    {
        $query = VentaDetalle::with(['producto', 'venta.factura'])
            ->select('ventas_detalle.*')
            ->join('ventas', 'ventas.id', '=', 'ventas_detalle.venta_id');

        // Aplicar filtros de fecha
        if (!empty($filtros['fecha_inicio'])) {
            $start = Carbon::parse($filtros['fecha_inicio'])->startOfDay();
            $query->where('ventas.fecha', '>=', $start);
        }

        if (!empty($filtros['fecha_fin'])) {
            $end = Carbon::parse($filtros['fecha_fin'])->endOfDay();
            $query->where('ventas.fecha', '<=', $end);
        }

        // Filtro por estado
        if (!empty($filtros['estado'])) {
            $query->where('ventas.estado', $filtros['estado']);
        }

        // Orden
        $order = strtolower($filtros['order'] ?? 'desc') === 'asc' ? 'asc' : 'desc';
        $query->orderBy('ventas.fecha', $order);

        return $query;
    }

    /**
     * Obtiene el reporte consolidado que une ventas y ventas_detalle
     * como una sola tabla virtual con toda la información
     * 
     * @param array $filtros ['fecha_inicio', 'fecha_fin', 'estado', 'order']
     * @return \Illuminate\Support\Collection
     */
    public static function ventasConsolidado(array $filtros = [])
    {
        $query = DB::table('ventas_detalle')
            ->join('ventas', 'ventas.id', '=', 'ventas_detalle.venta_id')
            ->leftJoin('productos', 'productos.id', '=', 'ventas_detalle.producto_id')
            ->leftJoin('facturas', 'facturas.venta_id', '=', 'ventas.id')
            ->select([
                'ventas.id as venta_id',
                'ventas.fecha',
                'ventas.estado',
                'ventas.total as venta_total',
                'ventas.cliente',
                'ventas.forma_pago',
                'facturas.numero as factura_numero',
                'facturas.cliente_nombre as factura_cliente',
                'facturas.impuestos as factura_iva',
                'facturas.total as factura_total',
                'ventas_detalle.id as detalle_id',
                'ventas_detalle.producto_id',
                'productos.nombre as producto_nombre',
                'ventas_detalle.cantidad',
                'ventas_detalle.precio_unitario',
                'ventas_detalle.subtotal',
                'ventas_detalle.iva',
                'ventas_detalle.motivo_anulacion',
            ]);

        // Aplicar filtros de fecha
        if (!empty($filtros['fecha_inicio'])) {
            $start = Carbon::parse($filtros['fecha_inicio'])->startOfDay();
            $query->where('ventas.fecha', '>=', $start);
        }

        if (!empty($filtros['fecha_fin'])) {
            $end = Carbon::parse($filtros['fecha_fin'])->endOfDay();
            $query->where('ventas.fecha', '<=', $end);
        }

        // Filtro por estado
        if (!empty($filtros['estado'])) {
            $query->where('ventas.estado', $filtros['estado']);
        }

        // Orden
        $order = strtolower($filtros['order'] ?? 'desc') === 'asc' ? 'asc' : 'desc';
        $query->orderBy('ventas.fecha', $order);

        return $query;
    }

    /**
     * Obtiene el reporte de inventario actual
     * 
     * @param array $filtros ['order', 'stock_minimo']
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function inventario(array $filtros = [])
    {
        $query = Producto::query();

        // Filtro por stock mínimo
        if (!empty($filtros['stock_minimo'])) {
            $query->where('stock', '<=', $filtros['stock_minimo']);
        }

        // Orden
        $order = strtolower($filtros['order'] ?? 'asc') === 'asc' ? 'asc' : 'desc';
        $query->orderBy('nombre', $order);

        return $query;
    }

    /**
     * Obtiene el reporte de movimientos de inventario
     * 
     * @param array $filtros ['fecha_inicio', 'fecha_fin', 'tipo', 'producto_id', 'order']
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function inventarioMovimientos(array $filtros = [])
    {
        $query = DB::table('inventario_movimientos')
            ->leftJoin('productos', 'productos.id', '=', 'inventario_movimientos.producto_id')
            ->select([
                'inventario_movimientos.id',
                'inventario_movimientos.producto_id',
                'productos.nombre as producto_nombre',
                'inventario_movimientos.tipo',
                'inventario_movimientos.cantidad',
                'inventario_movimientos.origen',
                'inventario_movimientos.referencia_id',
                'inventario_movimientos.descripcion',
                'inventario_movimientos.created_at',
            ]);

        // Aplicar filtros de fecha
        if (!empty($filtros['fecha_inicio'])) {
            $start = Carbon::parse($filtros['fecha_inicio'])->startOfDay();
            $query->where('inventario_movimientos.created_at', '>=', $start);
        }

        if (!empty($filtros['fecha_fin'])) {
            $end = Carbon::parse($filtros['fecha_fin'])->endOfDay();
            $query->where('inventario_movimientos.created_at', '<=', $end);
        }

        // Filtro por tipo (entrada/salida)
        if (!empty($filtros['tipo'])) {
            $query->where('inventario_movimientos.tipo', $filtros['tipo']);
        }

        // Filtro por producto
        if (!empty($filtros['producto_id'])) {
            $query->where('inventario_movimientos.producto_id', $filtros['producto_id']);
        }

        // Orden
        $order = strtolower($filtros['order'] ?? 'desc') === 'asc' ? 'asc' : 'desc';
        $query->orderBy('inventario_movimientos.created_at', $order);

        return $query;
    }

    /**
     * Obtiene estadísticas resumidas de ventas
     * 
     * @param array $filtros ['fecha_inicio', 'fecha_fin']
     * @return array
     */
    public static function estadisticasVentas(array $filtros = [])
    {
        $query = Venta::query();

        if (!empty($filtros['fecha_inicio'])) {
            $start = Carbon::parse($filtros['fecha_inicio'])->startOfDay();
            $query->where('fecha', '>=', $start);
        }

        if (!empty($filtros['fecha_fin'])) {
            $end = Carbon::parse($filtros['fecha_fin'])->endOfDay();
            $query->where('fecha', '<=', $end);
        }

        return [
    'total_ventas'       => (clone $query)->count(),
    'monto_total'        => (clone $query)->sum('total'),
    'promedio_venta'     => (clone $query)->avg('total'),
    'ventas_completadas' => (clone $query)->where('estado', 'completada')->count(),
    'ventas_anuladas'    => (clone $query)->where('estado', 'anulada')->count(),
];
    }
}