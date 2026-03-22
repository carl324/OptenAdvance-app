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
    DB::statement("ALTER TABLE clientes MODIFY cupo_credito INT NULL DEFAULT NULL");
}

public function down(): void
{
    DB::statement("ALTER TABLE clientes MODIFY cupo_credito INT NOT NULL DEFAULT 0");
}
};
