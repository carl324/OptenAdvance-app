<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('facturas', function (Blueprint $table) {
            $table->integer('total')->change();
            $table->integer('impuestos')->change();
        });
    }

    public function down()
    {
        Schema::table('facturas', function (Blueprint $table) {
            $table->decimal('total', 12, 2)->change();
            $table->decimal('impuestos', 12, 2)->change();
        });
    }
};
