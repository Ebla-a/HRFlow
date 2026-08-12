<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exchange_rates', function (Blueprint $table): void {
            $table->id();

            $table->string('from_currency', 3);
            $table->string('to_currency', 3);

            $table->decimal('rate', 20, 8);

            $table->date('rate_date');

            $table->string('provider', 50);

            $table->timestamps();

           $table->unique([
                'from_currency',
                'to_currency',
                'rate_date',
                'provider',
            ], 'exchange_rates_uq');

            $table->index([
                'from_currency',
                'to_currency',
                'rate_date',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exchange_rates');
    }
};