<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('salary_histories', function (Blueprint $table) {

            $table->id();

            $table->foreignId('employee_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('reason');

            $table->date('effective_date');

            $table->foreignId('changed_by')
                ->constrained('users')
                ->restrictOnDelete();
            $table->text('note')->nullable();

            $table->timestamps();


            $table->index([
                'employee_id',
                'effective_date',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('salary_histories');
    }
};
