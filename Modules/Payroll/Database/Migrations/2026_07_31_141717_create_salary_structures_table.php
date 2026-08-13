<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('salary_structures', function (Blueprint $table) {

            $table->id();

            $table->foreignId('employee_id')
                ->unique()
                ->constrained()
                ->cascadeOnDelete();

            $table->decimal('basic_salary',12,2);

            $table->decimal('housing_allowance',12,2)->default(0);

            $table->decimal('transport_allowance',12,2)->default(0);

            $table->decimal('other_allowance',12,2)->default(0);
            $table->date('effective_date');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('salary_structures');
    }
};