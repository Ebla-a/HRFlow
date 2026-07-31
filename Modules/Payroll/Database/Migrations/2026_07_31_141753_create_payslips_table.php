<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payslips', function (Blueprint $table) {

            $table->id();

            $table->foreignId('payroll_run_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('employee_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->decimal('basic_salary',12,2);

            $table->decimal('allowances',12,2)
                ->default(0);

            $table->decimal('deductions',12,2)
                ->default(0);

            $table->decimal('net_salary',12,2);

            $table->timestamps();

            $table->unique([
                'payroll_run_id',
                'employee_id'
            ]);

            $table->index('employee_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payslips');
    }
};