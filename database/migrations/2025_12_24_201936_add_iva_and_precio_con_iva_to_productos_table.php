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
        Schema::table('productos', function (Blueprint $table) {
            $table->decimal('iva', 5, 2)->default(0)->after('precio'); // Porcentaje de IVA
            $table->decimal('precio_con_iva', 10, 2)->default(0)->after('iva'); // Precio final con IVA incluido
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->dropColumn(['iva', 'precio_con_iva']);
        });
    }
};
