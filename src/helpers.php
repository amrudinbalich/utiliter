<?php

/**
 * Get the enviroment variable (or default).
 * 
 * @param string $key
 * @param string $default
 * 
 * @return string
 */
function env(string $key, ?string $default): string {
    return $_ENV[$key] ?? $default;
}

/**
 * Read a file from the storage directory.
 * @param string $filename
 * @return string|false
 */
function storage(string $filename): string|false
{
    return file_get_contents(__DIR__ . '/../storage/' . $filename);
}

/**
 * Transform uncategorized strings into normalized ucfirst ones.
 * @param string $value
 * @return string
 */
function normalizeString(string $value): string {
    return ucfirst(strtolower(trim(str_replace(' ', '', $value))));
    // example: "Marka 1" → "marka1" → "Marka1"
}