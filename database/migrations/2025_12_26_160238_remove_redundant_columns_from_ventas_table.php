<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->dropColumn([
                'cliente_nombre',
                'cliente_nit',
                'numero_factura',
                'fecha_emision',
                'forma_pago',
            ]);
        });
    }

    public function down()
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->string('cliente_nombre')->nullable()->after('cliente');
            $table->string('cliente_nit')->nullable()->after('cliente_nombre');
            $table->string('numero_factura')->nullable()->after('total');
            $table->date('fecha_emision')->nullable()->after('numero_factura');
            $table->string('forma_pago')->nullable()->after('fecha_emision');
        });
    }
};