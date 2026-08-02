<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_documents', function (Blueprint $table) {

            $table->id();

            $table->foreignId('employee_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('uploaded_by')
                ->constrained('users')
                ->restrictOnDelete();

            $table->string('title');

            $table->string('type');

            $table->string('file_path');

            $table->string('disk')->default('public');

            $table->string('original_name');

            $table->string('mime_type');

            $table->unsignedBigInteger('file_size');

            $table->timestamps();

            $table->index([
                'employee_id',
                'type'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_documents');
    }
};
