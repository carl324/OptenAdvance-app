<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // 1. Eliminar índice UNIQUE
            $table->dropUnique('users_username_unique');

            // 2. Eliminar columna
            $table->dropColumn('username');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Restaurar columna
            $table->string('username')->nullable();

            // Restaurar índice UNIQUE
            $table->unique('username', 'users_username_unique');
        });
    }
};
