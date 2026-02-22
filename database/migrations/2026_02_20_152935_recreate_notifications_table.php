<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Eliminar tabla actual y recrear desde cero
        Schema::dropIfExists('notifications');

        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->string('modulo', 50);               // 'backup', 'licencia', 'caja', etc
            $table->enum('tipo', ['info', 'warning', 'error']);
            $table->string('titulo', 255);              // "Backup fallido"
            $table->text('mensaje');                    // Descripción detallada
            $table->boolean('leida')->default(false);
            $table->json('data')->nullable();           // Metadata extra por módulo
            $table->timestamps();

            // Índices para consultas frecuentes
            $table->index('modulo');
            $table->index('leida');
            $table->index('tipo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};