<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();

            // tipo de notificación (license_expiry, license_expired_action, etc)
            $table->string('type');

            // días antes del vencimiento (30, 15, 7, 3, 0)
            $table->unsignedInteger('days_before')->nullable();

            // fecha en la que ya se mostró
            $table->timestamp('shown_at')->nullable();

            // clave diaria para evitar repetir el mismo día
            $table->date('date_key')->nullable();

            $table->timestamps();

            $table->unique(['type', 'days_before', 'date_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
