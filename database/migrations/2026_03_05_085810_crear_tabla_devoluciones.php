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
    Schema::create('devoluciones', function (Blueprint $table) {
        $table->id();
        $table->foreignId('venta_id')->constrained('ventas')->onDelete('cascade');
        $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
        $table->foreignId('motivo_devolucion_id')->constrained('motivos_devolucion');
        $table->text('observacion')->nullable();
        $table->enum('metodo_reembolso', ['efectivo', 'transferencia', 'nota_credito']);
        $table->integer('monto_calculado');
        $table->integer('monto_real');
        $table->timestamp('fecha');
        $table->timestamps();

        $table->index('venta_id');
        $table->index('fecha');
    });
}

public function down(): void
{
    Schema::dropIfExists('devoluciones');
}
};
