<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cajas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->dateTime('fecha_apertura');
            $table->decimal('monto_apertura', 12, 2);
            $table->string('nota_apertura')->nullable();
            $table->dateTime('fecha_cierre')->nullable();
            $table->decimal('total_ventas', 12, 2)->nullable();
            $table->decimal('total_efectivo', 12, 2)->nullable();
            $table->decimal('monto_cierre_calculado', 12, 2)->nullable();
            $table->decimal('monto_cierre_real', 12, 2)->nullable();
            $table->decimal('diferencia', 12, 2)->nullable();
            $table->string('nota_cierre')->nullable();
            $table->enum('estado', ['abierta', 'cerrada']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cajas');
    }
};
