<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       Schema::table('payroll_runs', function (Blueprint $table): void {
    $table
        ->string('currency', 3)
        ->default('USD')
        ->after('status');

    $table
        ->decimal('exchange_rate', 20, 8)
        ->nullable()
        ->after('currency');

    $table
        ->date('exchange_rate_date')
        ->nullable()
        ->after('exchange_rate');

    $table
        ->string('exchange_rate_provider', 50)
        ->nullable()
        ->after('exchange_rate_date');
});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payroll_runs', function (Blueprint $table) {

        });
    }
};
