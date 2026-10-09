<?php

namespace Tests\Unit\Localization;

use PHPUnit\Framework\TestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class FrontendTranslationKeysTest extends TestCase
{
    private const GENERATED_DIRECTORIES = ['actions', 'routes', 'wayfinder'];

    // Matches t('key') and t("key"); group 2 is the key.
    private const KEY_PATTERN = '/\\bt\\(\\s*([\'"])((?:(?!\\1)[^\\\\]|\\\\.)*)\\1/';

    private function basePath(string $path = ''): string
    {
        return dirname(__DIR__, 3) . ($path !== '' ? '/' . $path : '');
    }

    /**
     * @return array<string, string> file path => contents
     */
    private function frontendSources(): array
    {
        $root = $this->basePath('resources/js');
        $sources = [];

        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root, RecursiveDirectoryIterator::SKIP_DOTS));

        foreach ($iterator as $file) {
            if (! in_array($file->getExtension(), ['ts', 'tsx'], true)) {
                continue;
            }

            $relative = substr($file->getPathname(), strlen($root) + 1);

            if (in_array(explode('/', $relative)[0], self::GENERATED_DIRECTORIES, true)) {
                continue;
            }

            $sources[$relative] = file_get_contents($file->getPathname());
        }

        return $sources;
    }

    /**
     * @return array<string, string>
     */
    private function germanTranslations(): array
    {
        return json_decode(file_get_contents($this->basePath('lang/de.json')), true, flags: JSON_THROW_ON_ERROR);
    }

    public function test_frontend_uses_translation_keys()
    {
        $keys = [];

        foreach ($this->frontendSources() as $source) {
            preg_match_all(self::KEY_PATTERN, $source, $matches);
            $keys = [...$keys, ...$matches[2]];
        }

        $this->assertNotEmpty($keys, 'No t() calls found in resources/js.');
    }

    public function test_every_frontend_translation_key_has_a_german_translation()
    {
        $translations = $this->germanTranslations();
        $missing = [];

        foreach ($this->frontendSources() as $file => $source) {
            preg_match_all(self::KEY_PATTERN, $source, $matches);

            foreach ($matches[2] as $key) {
                $key = stripslashes($key);

                if (! array_key_exists($key, $translations)) {
                    $missing[] = "{$file}: {$key}";
                }
            }
        }

        $this->assertSame([], array_values(array_unique($missing)), 'Missing German translations in lang/de.json.');
    }

    public function test_frontend_contains_no_hardcoded_german_text()
    {
        $offenders = [];

        foreach ($this->frontendSources() as $file => $source) {
            foreach (explode("\n", $source) as $number => $line) {
                if (preg_match('/[äöüÄÖÜß]/u', $line)) {
                    $offenders[] = $file . ':' . ($number + 1) . ': ' . trim($line);
                }
            }
        }

        $this->assertSame([], $offenders, 'Hardcoded German text found; use t() with an English key instead.');
    }

    public function test_german_locale_file_has_no_duplicate_keys()
    {
        preg_match_all('/^\s*"((?:[^"\\\\]|\\\\.)*)"\s*:/m', file_get_contents($this->basePath('lang/de.json')), $matches);

        $duplicates = array_keys(array_filter(array_count_values($matches[1]), fn (int $count) => $count > 1));

        $this->assertSame([], $duplicates);
    }
}
