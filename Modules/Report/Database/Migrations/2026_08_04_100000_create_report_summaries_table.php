<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('report_summaries', function (Blueprint $table) {
            $table->id();

            // report_type: payroll | attendance | leave | performance | employees
            $table->string('report_type');

            $table->unsignedSmallInteger('month')->nullable();
            $table->unsignedSmallInteger('year')->nullable();

            // snapshot of aggregated data
            $table->json('data');

            $table->timestamp('generated_at')->nullable();

            $table->timestamps();

            $table->index(['report_type', 'month', 'year']);
            $table->unique(['report_type', 'month', 'year']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report_summaries');
    }
};
