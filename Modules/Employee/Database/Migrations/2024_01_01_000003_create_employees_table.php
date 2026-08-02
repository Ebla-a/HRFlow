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
                ->unique()
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('department_id')
                ->constrained()
                ->nullOnDelete();

            $table->foreignId('job_title_id')
                ->constrained()
                ->restrictOnDelete();

            $table->foreignId('manager_id')
                ->nullable()
                ->constrained('employees')
                ->nullOnDelete();

            $table->string('employee_number')->unique();

            $table->string('first_name');

            $table->string('last_name');

            $table->string('phone',30)->nullable();

            $table->string('national_id')->unique();

            $table->date('birth_date');

            $table->string('gender');

            $table->text('address')->nullable();

            $table->string('employment_type');

            $table->string('status');

            $table->date('hire_date');

            $table->date('termination_date')->nullable();

            $table->text('termination_reason')->nullable();

            $table->timestamps();

            $table->index([
                'department_id',
                'job_title_id',
                'status'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
