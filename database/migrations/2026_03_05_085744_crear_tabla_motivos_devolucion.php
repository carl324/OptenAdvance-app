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
    Schema::create('motivos_devolucion', function (Blueprint $table) {
        $table->id();
        $table->string('nombre', 100);
        $table->boolean('activo')->default(true);
        $table->timestamps();
    });

    // Motivos por defecto
    DB::table('motivos_devolucion')->insert([
        ['nombre' => 'Producto defectuoso', 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
        ['nombre' => 'Producto equivocado', 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
        ['nombre' => 'Cliente cambió de opinión', 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
        ['nombre' => 'Producto vencido', 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
        ['nombre' => 'Otro', 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
    ]);
}

public function down(): void
{
    Schema::dropIfExists('motivos_devolucion');
}
};
