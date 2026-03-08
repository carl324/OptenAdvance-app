<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ventas
        Schema::table('ventas', function (Blueprint $table) {
            $table->index('estado', 'idx_ventas_estado');
            $table->index('fecha', 'idx_ventas_fecha');
            $table->index('created_at', 'idx_ventas_created_at');
            $table->index(['estado', 'created_at'], 'idx_ventas_estado_created_at');
            $table->index(['estado', 'fecha'], 'idx_ventas_estado_fecha');
        });

        // ventas_detalle — ya tiene FKs individuales, agregar compuesto
        Schema::table('ventas_detalle', function (Blueprint $table) {
            $table->index(['venta_id', 'producto_id'], 'idx_vd_venta_producto');
        });

        // productos
        Schema::table('productos', function (Blueprint $table) {
            $table->index('activo', 'idx_productos_activo');
            $table->index(['activo', 'nombre'], 'idx_productos_activo_nombre');
        });

        // inventario_movimientos
        Schema::table('inventario_movimientos', function (Blueprint $table) {
            $table->index('created_at', 'idx_inv_created_at');
            $table->index('tipo', 'idx_inv_tipo');
            $table->index(['created_at', 'tipo'], 'idx_inv_created_at_tipo');
        });

        // clientes
        Schema::table('clientes', function (Blueprint $table) {
            $table->index('deleted_at', 'idx_clientes_deleted_at');
            $table->index('nombre', 'idx_clientes_nombre');
        });

        // cajas
        Schema::table('cajas', function (Blueprint $table) {
            $table->index('estado', 'idx_cajas_estado');
            $table->index('fecha_apertura', 'idx_cajas_fecha_apertura');
        });
    }

    public function down(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->dropIndex('idx_ventas_estado');
            $table->dropIndex('idx_ventas_fecha');
            $table->dropIndex('idx_ventas_created_at');
            $table->dropIndex('idx_ventas_estado_created_at');
            $table->dropIndex('idx_ventas_estado_fecha');
        });

        Schema::table('ventas_detalle', function (Blueprint $table) {
            $table->dropIndex('idx_vd_venta_producto');
        });

        Schema::table('productos', function (Blueprint $table) {
            $table->dropIndex('idx_productos_activo');
            $table->dropIndex('idx_productos_activo_nombre');
        });

        Schema::table('inventario_movimientos', function (Blueprint $table) {
            $table->dropIndex('idx_inv_created_at');
            $table->dropIndex('idx_inv_tipo');
            $table->dropIndex('idx_inv_created_at_tipo');
        });

        Schema::table('clientes', function (Blueprint $table) {
            $table->dropIndex('idx_clientes_deleted_at');
            $table->dropIndex('idx_clientes_nombre');
        });

        Schema::table('cajas', function (Blueprint $table) {
            $table->dropIndex('idx_cajas_estado');
            $table->dropIndex('idx_cajas_fecha_apertura');
        });
    }
};