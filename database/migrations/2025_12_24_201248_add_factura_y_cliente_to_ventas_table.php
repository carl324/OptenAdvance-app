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
        Schema::table('ventas', function (Blueprint $table) {
            $table->string('cliente_nombre')->nullable()->after('id');
            $table->string('cliente_nit')->nullable()->after('cliente_nombre');
            $table->string('numero_factura')->nullable()->after('cliente_nit');
            $table->date('fecha_emision')->nullable()->after('numero_factura');
            $table->string('forma_pago')->nullable()->after('fecha_emision');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->dropColumn([
                'cliente_nombre',
                'cliente_nit',
                'numero_factura',
                'fecha_emision',
                'forma_pago'
            ]);
        });
    }
};
