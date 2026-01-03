<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('empresa', function (Blueprint $table) {
            $table->dropColumn([
                'precios_incluyen_iva',
                'porcentaje_iva',
                'tipo_regimen'
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('empresa', function (Blueprint $table) {
            $table->boolean('precios_incluyen_iva')->default(false);
            $table->decimal('porcentaje_iva', 5, 2)->nullable();
            $table->string('tipo_regimen')->nullable();
        });
    }
};
