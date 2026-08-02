<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payroll_runs', function (Blueprint $table) {

            $table->id();

            $table->unsignedSmallInteger('month');

            $table->unsignedSmallInteger('year');

            $table->string('status')->default('draft');

            $table->timestamp('processed_at')
                ->nullable();

            $table->foreignId('processed_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->unique([
                'month',
                'year'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payroll_runs');
    }
};