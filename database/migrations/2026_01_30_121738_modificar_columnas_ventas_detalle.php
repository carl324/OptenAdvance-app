<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('ventas_detalle', function (Blueprint $table) {
            $table->integer('precio_unitario')->change();
            $table->integer('subtotal')->change();
            $table->integer('iva')->change();
            $table->integer('total_pagado')->change();
        });
    }

    public function down()
    {
        Schema::table('ventas_detalle', function (Blueprint $table) {
            $table->decimal('precio_unitario', 10, 2)->change();
            $table->decimal('subtotal', 10, 2)->change();
            $table->decimal('iva', 10, 2)->change();
            $table->decimal('total_pagado', 10, 2)->change();
        });
    }
};
