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
        Schema::table('payrolls', function (Blueprint $table) {
            $table->integer('total_sick_days')->default(0)->after('total_wfh_days');
            $table->integer('total_permit_days')->default(0)->after('total_sick_days');
            $table->decimal('leave_deduction_amount', 12, 2)->default(0)->after('wfh_deduction_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            $table->dropColumn(['total_sick_days', 'total_permit_days', 'leave_deduction_amount']);
        });
    }
};
