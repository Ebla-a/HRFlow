<?php

if (!function_exists('get_current_locale')) {
    /**
     * Get the current application locale.
     *
     * @return string
     */
    function get_current_locale()
    {
        return app()->getLocale();
    }
}

if (!function_exists('get_supported_languages')) {
    /**
     * Get all supported languages except the current locale.
     *
     * @param string|null $excludeLang Language to exclude (default: current locale)
     * @return array
     */
    function get_supported_languages(?string $excludeLang = null)
    {
        $excludeLang = $excludeLang ?? get_current_locale();
        $supportedLanguages = config('translaGenius.supported_languages', ['en']);

        return array_values(array_diff($supportedLanguages, [$excludeLang]));
    }
}
