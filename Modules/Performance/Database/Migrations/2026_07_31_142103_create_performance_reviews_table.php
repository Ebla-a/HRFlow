<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Performance\Enums\PerformanceReviewStatus;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('performance_reviews', function (Blueprint $table) {

            $table->id();

            $table->foreignId('performance_cycle_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('employee_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('reviewer_id')
                ->constrained('employees')
                ->restrictOnDelete();

            $table->decimal('score',5,2);

            $table->json('comments')
                ->nullable();


            $table->enum('status', array_column(PerformanceReviewStatus::cases(), 'value'))
                ->default(PerformanceReviewStatus::DRAFT->value);


            $table->timestamp('reviewed_at')
                ->nullable();

            $table->timestamps();

            $table->unique([
                'performance_cycle_id',
                'employee_id'
            ]);

            $table->index([
                'employee_id',
                'status'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('performance_reviews');
    }
};