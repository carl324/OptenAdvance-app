<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('empresa', function (Blueprint $table) {

            $table->boolean('cobra_iva')
                  ->default(false)
                  ->after('moneda');

            $table->decimal('porcentaje_iva', 5, 2)
                  ->default(19.00)
                  ->after('cobra_iva');

            $table->boolean('precios_incluyen_iva')
                  ->default(true)
                  ->after('porcentaje_iva');

            $table->enum('tipo_regimen', [
                'no_responsable',
                'responsable_iva'
            ])->default('no_responsable')
              ->after('precios_incluyen_iva');

        });
    }

    public function down(): void
    {
        Schema::table('empresa', function (Blueprint $table) {
            $table->dropColumn([
                'cobra_iva',
                'porcentaje_iva',
                'precios_incluyen_iva',
                'tipo_regimen'
            ]);
        });
    }
};
