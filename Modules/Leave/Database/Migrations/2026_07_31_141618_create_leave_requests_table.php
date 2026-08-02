<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leave_requests', function (Blueprint $table) {

            $table->id();

            $table->foreignId('employee_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('leave_type_id')
                ->constrained()
                ->restrictOnDelete();
 
            $table->date('start_date');

            $table->date('end_date');

            $table->unsignedInteger('days_count');
 
            $table->string('status')
              ->default('pending');

            $table->text('reason')
               ->nullable();

            $table->text('rejection_reason')
                ->nullable();

            $table->string('attachment_path')
                ->nullable();

            // Manager Approval
            $table->string('manager_approval_status')
                ->default('pending');

            $table->timestamp('manager_approved_at')
                ->nullable();

            $table->foreignId('approved_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // HR Approval
            $table->string('hr_approval_status')
                ->default('pending');

            $table->timestamp('hr_approved_at')
                ->nullable();

            $table->foreignId('hr_approved_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->softDeletes();

            $table->index([
                'employee_id',
                'status'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leave_requests');
    }
};
 