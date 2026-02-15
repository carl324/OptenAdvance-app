<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // ========== TABLA PRODUCTOS ==========
        
        Schema::table('productos', function (Blueprint $table) {
            // Agregar nuevas columnas (temporalmente nullable para migración)
            $table->integer('precio_compra')->nullable()->after('nombre');
            $table->integer('precio_venta')->nullable()->after('precio_compra');
        });
        
        // Migrar datos: precio actual → precio_venta
        DB::statement('UPDATE productos SET precio_venta = precio WHERE precio_venta IS NULL');
        
        // Establecer precio_compra estimado (60% del precio_venta como default)
        // O dejarlo en 0 si prefieres que lo llenen manual
        DB::statement('UPDATE productos SET precio_compra = FLOOR(precio_venta * 0.6) WHERE precio_compra IS NULL');
        
        Schema::table('productos', function (Blueprint $table) {
            // Hacer NOT NULL después de llenar datos
            $table->integer('precio_compra')->nullable(false)->change();
            $table->integer('precio_venta')->nullable(false)->change();
            
            // Eliminar columna vieja
            $table->dropColumn('precio');
        });
        
        
        // ========== TABLA VENTAS_DETALLE ==========
        
        Schema::table('ventas_detalle', function (Blueprint $table) {
            // Agregar precio_compra histórico (NULL para ventas antiguas)
            $table->integer('precio_compra')->nullable()->after('precio_unitario');
        });
    }

    public function down()
    {
        // Rollback: restaurar estructura original
        
        Schema::table('productos', function (Blueprint $table) {
            $table->integer('precio')->after('nombre');
        });
        
        DB::statement('UPDATE productos SET precio = precio_venta WHERE precio IS NULL');
        
        Schema::table('productos', function (Blueprint $table) {
            $table->dropColumn(['precio_compra', 'precio_venta']);
        });
        
        Schema::table('ventas_detalle', function (Blueprint $table) {
            $table->dropColumn('precio_compra');
        });
    }
};