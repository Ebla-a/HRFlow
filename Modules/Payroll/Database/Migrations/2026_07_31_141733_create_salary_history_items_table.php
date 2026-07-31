<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('salary_history_items', function (Blueprint $table) {

            $table->id();

            $table->foreignId('salary_history_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('name');

            $table->decimal('old_amount',12,2);

            $table->decimal('new_amount',12,2);

            $table->timestamps();

            $table->index('salary_history_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('salary_history_items');
    }
};