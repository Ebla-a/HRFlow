<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Performance\Enums\PerformanceCycleStatus;
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('performance_cycles', function (Blueprint $table) {

            $table->id();

            $table->json('name');

            $table->date('start_date');

            $table->date('end_date');

            $table->enum('status', array_column(PerformanceCycleStatus::cases(), 'value'))
                ->default(PerformanceCycleStatus::ACTIVE->value);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('performance_cycles');
    }
};