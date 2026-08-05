<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Payroll\App\Enums\PayslipStatus;

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

            $table->decimal('housing_allowance', 12, 2)->default(0);

            $table->decimal('transport_allowance', 12, 2)->default(0);

            $table->decimal('other_allowance', 12, 2)->default(0);

             $table->decimal('gross_salary',12,2);   

            $table->decimal('deductions',12,2)
                ->default(0);


            $table->string('status')->default(PayslipStatus::DRAFT->value);
             

            $table->decimal('unpaid_leave_deduction', 12, 2)->default(0);

            $table->unsignedInteger('unpaid_leave_days')->default(0);

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