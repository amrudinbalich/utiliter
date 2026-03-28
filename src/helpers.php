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