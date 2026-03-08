<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up(): void
{
    Schema::create('inventario_movimientos_archivo', function (Blueprint $table) {
        $table->bigIncrements('id');
        $table->unsignedBigInteger('id_original');
        $table->unsignedBigInteger('producto_id');
        $table->enum('tipo', ['entrada', 'salida']);
        $table->decimal('cantidad', 10, 2);
        $table->enum('origen', ['registro_producto', 'venta', 'venta_anulada', 'ajuste']);
        $table->unsignedBigInteger('referencia_id')->nullable();
        $table->string('descripcion')->nullable();
        $table->unsignedBigInteger('user_id')->nullable();
        $table->timestamp('created_at')->nullable();
        $table->timestamp('updated_at')->nullable();
        $table->timestamp('archivado_at')->useCurrent();
        $table->index('producto_id');
        $table->index('created_at');
    });
}

public function down(): void
{
    Schema::dropIfExists('inventario_movimientos_archivo');
}
};
