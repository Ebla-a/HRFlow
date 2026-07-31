<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {

            $table->id();

            $table->foreignId('employee_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->date('attendance_date');

            $table->timestamp('check_in')->nullable();

            $table->timestamp('check_out')->nullable();

            $table->integer('worked_minutes')
                ->default(0);

            $table->integer('late_minutes')
                ->default(0);

            $table->integer('overtime_minutes')
                ->default(0);

            $table->string('status');

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->unique([
                'employee_id',
                'attendance_date'
            ]);

            $table->index([
                'attendance_date',
                'status'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};