<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up(): void
{
    // Agregar unidad a productos y cambiar stock a decimal
    Schema::table('productos', function (Blueprint $table) {
        $table->string('unidad', 30)->default('Unidad')->after('stock');
        $table->decimal('stock', 10, 2)->default(0)->change();
    });

    // Cambiar cantidad a decimal en ventas_detalle
    Schema::table('ventas_detalle', function (Blueprint $table) {
        $table->decimal('cantidad', 10, 2)->change();
    });

    // Cambiar cantidad a decimal en inventario_movimientos
    Schema::table('inventario_movimientos', function (Blueprint $table) {
        $table->decimal('cantidad', 10, 2)->change();
    });
}

public function down(): void
{
    Schema::table('productos', function (Blueprint $table) {
        $table->dropColumn('unidad');
        $table->integer('stock')->default(0)->change();
    });

    Schema::table('ventas_detalle', function (Blueprint $table) {
        $table->integer('cantidad')->change();
    });

    Schema::table('inventario_movimientos', function (Blueprint $table) {
        $table->integer('cantidad')->change();
    });
}

 
};
