<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MakeEmpresaColumnsNullable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('empresa', function (Blueprint $table) {
            $table->string('nombre')->nullable()->change();
            $table->string('nit')->nullable()->change();
            $table->string('direccion')->nullable()->change();
            $table->string('telefono')->nullable()->change();
            $table->string('email')->nullable()->change();
            $table->string('moneda')->nullable()->change();
            $table->boolean('cobra_iva')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('empresa', function (Blueprint $table) {
            $table->string('nombre')->nullable(false)->change();
            $table->string('nit')->nullable(false)->change();
            $table->string('direccion')->nullable(false)->change();
            $table->string('telefono')->nullable(false)->change();
            $table->string('email')->nullable(false)->change();
            $table->string('moneda')->nullable(false)->change();
            $table->boolean('cobra_iva')->nullable(false)->change();
        });
    }
}
