<?php

namespace App\Utiliter\Contracts;

/**
 * Enforces a consistent contract across all import services.
 */
interface ImportInterface
{
    /**
     * Execute the import process.
     * @return void
     */
    public function import(): void;
}