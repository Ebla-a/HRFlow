<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('departments', function (Blueprint $table) {

            $table->id();

            $table->string('name')->unique();

            $table->string('code',20)->unique();

            $table->foreignId('parent_id')
                ->nullable()
                ->constrained('departments')
                ->nullOnDelete();

            $table->foreignId('manager_id')
                ->nullable()
                ->constrained('employees')
                ->nullOnDelete();

            $table->boolean('is_active')
                ->default(true);

            $table->timestamps();

            $table->index([
                'parent_id',
                'manager_id'
            ]);

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('departments');
    }
};