<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('license_state', function (Blueprint $table) {
            $table->id();

            $table->string('status', 20); // active | expired | blocked
            $table->string('machine_hash', 64);

            $table->timestamp('last_valid_check_at')->nullable();

            $table->timestamp('hardware_trial_started_at')->nullable();
            $table->timestamp('expired_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('license_state');
    }
};
