<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_titles', function (Blueprint $table) {

            $table->id();

            $table->foreignId('department_id')
                ->constrained()
                ->restrictOnDelete();

            $table->string('title');

            $table->string('grade',50)->nullable();

            $table->boolean('is_active')
                ->default(true);

            $table->timestamps();

            $table->index([
                'department_id'
            ]);

            $table->unique([
                'department_id',
                'title'
            ]);

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_titles');
    }
};