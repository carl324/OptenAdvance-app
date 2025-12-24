<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Eliminar tabla vieja
        Schema::dropIfExists('ventas');
        
        // Crear tabla nueva SIN caja_id
        Schema::create('ventas', function (Blueprint $table) {
            $table->id();
            $table->string('cliente', 100)->nullable();
            $table->decimal('total', 10, 2);
            $table->string('estado', 20)->default('completada');
            $table->dateTime('fecha');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('ventas');
    }
};