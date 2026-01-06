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
       Schema::create('empresa', function (Blueprint $table) {
    $table->id();

    $table->string('nombre', 150)->unique();  // Bug #24: Agregar unique constraint
    $table->string('nit', 50)->unique();       // Bug #24: Agregar unique constraint
    $table->string('direccion')->nullable();
    $table->string('telefono', 30)->nullable();
    $table->string('email')->nullable();

    $table->string('moneda', 10)->default('COP');
    $table->boolean('cobra_iva')->default(true);

    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('empresa');
    }
};
