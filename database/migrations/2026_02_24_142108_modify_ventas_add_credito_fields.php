<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->foreignId('cliente_id')->nullable()->after('cliente')->constrained('clientes')->nullOnDelete();
            $table->integer('saldo_pendiente')->default(0)->after('total');
            $table->enum('estado', ['completada', 'anulada', 'credito', 'parcial'])
                  ->default('completada')
                  ->change();
        });
    }

    public function down(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->dropForeign(['cliente_id']);
            $table->dropColumn(['cliente_id', 'saldo_pendiente']);
            $table->enum('estado', ['completada', 'anulada'])
                  ->default('completada')
                  ->change();
        });
    }
};