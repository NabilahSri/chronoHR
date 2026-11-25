<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sanction_rules', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code', 50)->unique();
            $table->string('name');
            $table->enum('category', ['DISIPLIN_WAKTU', 'KEHADIRAN', 'KINERJA_TUGAS', 'ETIKA_SOP'])->default('DISIPLIN_WAKTU');
            $table->enum('trigger_type', ['AUTO_LATE', 'AUTO_ALPHA', 'MANUAL'])->default('MANUAL');
            $table->integer('threshold_count')->nullable()->comment('Batas jumlah pelanggaran bulanan untuk trigger otomatis (misal > 3 kali terlambat)');
            $table->enum('default_level', ['TEGURAN_LISAN', 'SP1', 'SP2', 'SP3'])->default('TEGURAN_LISAN');
            $table->decimal('default_deduction_amount', 12, 2)->default(0);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('employee_sanctions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignUuid('sanction_rule_id')->nullable()->constrained('sanction_rules')->onDelete('set null');
            $table->foreignUuid('issued_by')->nullable()->constrained('users')->onDelete('set null');
            $table->integer('month');
            $table->integer('year');
            $table->date('incident_date');
            $table->string('violation_name');
            $table->enum('level', ['TEGURAN_LISAN', 'SP1', 'SP2', 'SP3'])->default('TEGURAN_LISAN');
            $table->enum('source', ['AUTO_DETECTED', 'MANUAL'])->default('MANUAL');
            $table->integer('occurrence_count')->default(1);
            $table->decimal('deduction_amount', 12, 2)->default(0);
            $table->text('reason');
            $table->enum('status', ['ACTIVE', 'RESOLVED', 'REVOKED'])->default('ACTIVE');
            $table->timestamps();
        });

        Schema::table('payrolls', function (Blueprint $table) {
            $table->integer('total_sanctions_count')->default(0)->after('total_overtime_hours');
            $table->decimal('sanction_deduction_amount', 12, 2)->default(0)->after('leave_deduction_amount');
        });
    }

    public function down(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            $table->dropColumn(['total_sanctions_count', 'sanction_deduction_amount']);
        });
        Schema::dropIfExists('employee_sanctions');
        Schema::dropIfExists('sanction_rules');
    }
};
