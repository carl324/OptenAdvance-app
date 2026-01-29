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
        Schema::table('ventas_detalle', function (Blueprint $table) {
            $table->string('motivo_anulacion')->nullable()->after('total_pagado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ventas_detalle', function (Blueprint $table) {
            $table->dropColumn('motivo_anulacion');
        });
    }
};
