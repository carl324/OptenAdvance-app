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
        Schema::create('facturas', function (Blueprint $table) {
            $table->id();
            $table->string('numero')->unique();
            $table->foreignId('venta_id')->constrained('ventas')->onDelete('cascade');
            $table->dateTime('fecha_emision');
            $table->string('cliente_nombre')->nullable();
            $table->string('cliente_nit')->nullable();
            $table->decimal('total', 12, 2);
            $table->decimal('impuestos', 12, 2);
            $table->string('forma_pago');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('facturas');
    }
};
