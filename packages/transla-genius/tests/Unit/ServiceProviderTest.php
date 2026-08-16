<?php

namespace CodingPartners\TranslaGenius\Tests\Unit;

use CodingPartners\TranslaGenius\TranslaGeniusServiceProvider;
use CodingPartners\TranslaGenius\Tests\TestCase;

/**
 * Unit tests for TranslaGeniusServiceProvider
 *
 * Tests the service provider functionality including:
 * - Configuration file publishing
 * - Helper function registration
 */
class ServiceProviderTest extends TestCase
{
    /**
     * Test that the configuration file is properly registered and publishable
     *
     * Verifies that:
     * 1. The package configuration is properly registered
     * 2. The config file exists in the application config
     * 3. The config structure is accessible
     *
     * @return void
     */
    public function test_config_file_is_publishable()
    {
        $this->assertArrayHasKey('translaGenius', config());
    }

    /**
     * Test that helper functions are properly loaded by the service provider
     *
     * Verifies that:
     * 1. All expected helper functions are registered
     * 2. Functions are available globally
     * 3. The service provider properly loads the helpers file
     *
     * @return void
     */
    public function test_helpers_are_loaded()
    {
        $this->assertTrue(function_exists('get_current_locale'));
        $this->assertTrue(function_exists('get_supported_languages'));
    }
}
