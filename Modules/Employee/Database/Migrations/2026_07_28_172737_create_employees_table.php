<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignId('department_id')
                ->constrained()
                ->restrictOnDelete();

            $table->foreignId('job_title_id')
                ->constrained()
                ->restrictOnDelete();

            $table->foreignId('manager_id')
                ->nullable()
                ->constrained('employees')
                ->nullOnDelete();

            $table->string('employee_number')
                ->unique();

            $table->enum('employment_type', [
                'full_time',
                'part_time',
                'contract'
            ]);

            $table->date('hire_date');

            $table->enum('status', [
                'active',
                'on_leave',
                'suspended',
                'terminated'
            ])->default('active');

            $table->string('national_id')
                ->nullable()
                ->unique();

            $table->string('phone')
                ->nullable();

            $table->text('address')
                ->nullable();

            $table->date('birth_date')
                ->nullable();

            $table->enum('gender', [
                'male',
                'female'
            ])->nullable();

            $table->date('termination_date')
                ->nullable();

            $table->text('termination_reason')
                ->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};