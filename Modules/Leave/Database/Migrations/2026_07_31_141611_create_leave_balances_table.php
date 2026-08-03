<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leave_balances', function (Blueprint $table) {
            $table->id(); 

            $table->foreignId('employee_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('leave_type_id')
                ->constrained()
                ->cascadeOnDelete(); 

            $table->integer('total_days')
                ->default(0);

            $table->integer('used_days')
                ->default(0);

            $table->integer('remaining_days')
                ->default(0);

            $table->string('status')
                ->default('active');

            $table->year('year');

            $table->timestamps();

            $table->softDeletes();

            $table->unique([
                'employee_id',
                'leave_type_id',
                'year'
           ]);

          });
    }

    public function down(): void
    {
        Schema::dropIfExists('leave_balances');
    }
};
 