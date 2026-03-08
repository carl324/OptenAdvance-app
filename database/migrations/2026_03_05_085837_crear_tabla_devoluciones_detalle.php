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
    Schema::create('devoluciones_detalle', function (Blueprint $table) {
        $table->id();
        $table->foreignId('devolucion_id')->constrained('devoluciones')->onDelete('cascade');
        $table->foreignId('venta_detalle_id')->constrained('ventas_detalle')->onDelete('cascade');
        $table->foreignId('producto_id')->constrained('productos')->onDelete('cascade');
        $table->decimal('cantidad_devuelta', 10, 2);
        $table->integer('precio_unitario');
        $table->integer('subtotal');
        $table->timestamps();

        $table->index('devolucion_id');
        $table->index('producto_id');
    });
}

public function down(): void
{
    Schema::dropIfExists('devoluciones_detalle');
}
};
