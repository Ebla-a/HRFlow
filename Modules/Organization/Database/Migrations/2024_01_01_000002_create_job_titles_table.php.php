<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Organization\Enums\JobTitleGrade;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_titles', function (Blueprint $table) {

            $table->id();

            $table->foreignId('department_id')->constrained('departments')->restrictOnDelete();

            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('grade', JobTitleGrade::cases())->default(JobTitleGrade::JUNIOR->value);

            $table->boolean('is_active')
                ->default(true);

            $table->timestamps();
            $table->softDeletes();


            $table->unique([
                'department_id',
                'title'
            ]);
            $table->index(['title','department_id']);

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_titles');
    }
};

