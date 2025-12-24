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
        Schema::create('ventas', function (Blueprint $table) {
            $table->id();
            $table->string('cliente')->nullable();
            $table->decimal('total', 10, 2);
            $table->enum('estado', ['completada','anulada'])->default('completada');
            $table->timestamp('fecha')->useCurrent();
            $table->foreignId('caja_id')->constrained('caja');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ventas');
    }
};
