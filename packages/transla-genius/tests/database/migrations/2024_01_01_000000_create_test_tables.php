<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('test_translatable_models', function (Blueprint $table) {
            $table->id();
            $table->json('name');
            $table->json('description');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('test_translatable_models');
    }
};
