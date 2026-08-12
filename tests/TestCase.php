<?php

namespace Tests;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed all modules (roles, permissions, employees, performance...)
        $this->seed(\Database\Seeders\DatabaseSeeder::class);
    }
}
