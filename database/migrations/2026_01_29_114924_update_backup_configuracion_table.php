<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('backup_configuracion', function (Blueprint $table) {
            $table->id();
            $table->string('carpeta_destino')->nullable();
            $table->string('prefijo_nombre_archivo')->nullable();
            $table->enum('frecuencia', ['diario','semanal','mensual'])->nullable();
            $table->time('hora_backup')->nullable();
            $table->integer('retencion')->nullable();
            $table->timestamp('ultima_fecha_backup')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('backup_configuracion');
    }
};
