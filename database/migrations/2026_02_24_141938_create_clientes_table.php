<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100);
            $table->string('telefono', 30)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('nit', 50)->nullable();
            $table->string('direccion', 255)->nullable();
            $table->integer('cupo_credito')->default(0);
            $table->integer('saldo_pendiente')->default(0);
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};