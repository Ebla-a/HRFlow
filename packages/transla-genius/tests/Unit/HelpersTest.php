<?php

namespace CodingPartners\TranslaGenius\Tests\Unit;

use CodingPartners\TranslaGenius\Tests\TestCase;

/**
 * Unit tests for global helper functions
 *
 * Tests the behavior of package helper functions including:
 * - Locale detection and retrieval
 * - Supported languages listing
 * - Custom language exclusion
 */
class HelpersTest extends TestCase
{
    /**
     * Test that get_current_locale returns the current application locale
     *
     * Verifies that:
     * 1. The helper correctly retrieves the current locale
     * 2. Returns the same value as app()->getLocale()
     *
     * @return void
     */
    public function test_get_current_locale_returns_default_locale()
    {
        // Set up test environment
        app()->setLocale('en');

        // Assert the helper returns expected value
        $this->assertEquals('en', get_current_locale());
    }

    /**
     * Test that get_supported_languages returns all languages except current
     *
     * Verifies that:
     * 1. The helper returns all supported languages
     * 2. Automatically excludes the current locale
     * 3. Maintains correct order of languages
     *
     * @return void
     */
    public function test_get_supported_languages_returns_all_except_current()
    {
        // Set up test environment
        app()->setLocale('en');

        // Define expected result
        $expected = ['ar', 'es'];

        // Assert the helper returns expected languages
        $this->assertEquals($expected, get_supported_languages());
    }

    /**
     * Test that get_supported_languages respects custom exclude parameter
     *
     * Verifies that:
     * 1. The helper accepts custom language to exclude
     * 2. Returns all supported languages except the excluded one
     * 3. Can include current locale if not explicitly excluded
     *
     * @return void
     */
    public function test_get_supported_languages_with_custom_exclude()
    {
        // Set up test environment
        app()->setLocale('en');

        // Define expected result with custom exclusion
        $expected = ['en', 'es'];

        // Assert the helper respects custom exclusion
        $this->assertEquals($expected, get_supported_languages('ar'));
    }
}
