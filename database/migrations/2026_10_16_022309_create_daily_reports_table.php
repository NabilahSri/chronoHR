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
        Schema::create('daily_reports', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users')->onDelete('cascade');
            $table->date('date');
            $table->time('morning_submitted_at')->nullable();
            $table->text('morning_planned_tasks');
            $table->time('evening_submitted_at')->nullable();
            $table->text('evening_completed_tasks')->nullable();
            $table->text('evening_pending_tasks')->nullable();
            $table->text('evening_additional_tasks')->nullable();
            $table->enum('status', ['MORNING_ONLY', 'COMPLETED'])->default('MORNING_ONLY');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_reports');
    }
};
