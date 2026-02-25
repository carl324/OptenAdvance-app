// database/migrations/xxxx_create_auditoria_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('auditoria', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('tipo_accion', 50);        // anulacion_venta, apertura_caja, etc.
            $table->string('entidad', 50);             // venta, caja, producto
            $table->unsignedBigInteger('entidad_id')->nullable();
            $table->json('antes')->nullable();
            $table->json('despues')->nullable();
            $table->string('ip', 45)->nullable();
            $table->text('descripcion')->nullable();
            $table->timestamp('created_at')->useCurrent();

            // Índices para los 3 filtros de la vista
            $table->index('user_id');
            $table->index('tipo_accion');
            $table->index('created_at');
            $table->index(['created_at', 'tipo_accion']); // compuesto para filtros combinados

            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('auditoria');
    }
};