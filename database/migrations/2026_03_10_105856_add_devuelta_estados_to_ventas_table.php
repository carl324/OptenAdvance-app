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
    DB::statement("ALTER TABLE ventas MODIFY estado ENUM('completada','anulada','credito','parcial','devuelta','dev_parcial') NOT NULL DEFAULT 'completada'");
}

public function down(): void
{
    DB::statement("ALTER TABLE ventas MODIFY estado ENUM('completada','anulada','credito','parcial') NOT NULL DEFAULT 'completada'");
}
};
