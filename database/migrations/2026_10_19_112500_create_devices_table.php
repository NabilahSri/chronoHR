<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('devices', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('device_uid')->unique();
            $table->string('ip_address')->nullable();
            $table->string('location')->default('Lobby Utama');
            $table->enum('status', ['ONLINE', 'OFFLINE', 'MAINTENANCE'])->default('ONLINE');
            $table->enum('mode', ['ATTENDANCE', 'REGISTRATION', 'MAINTENANCE'])->default('ATTENDANCE');
            $table->string('firmware_version')->default('1.0.0');
            $table->timestamp('last_ping_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('devices');
    }
};
