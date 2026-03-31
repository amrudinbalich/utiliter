<?php

namespace App\Utiliter\Contracts;

interface DTOInterface
{
    /**
     * Convert array to object representative.
     * @param array $data
     * @return self
     */
    public static function fromArray(array $data): array;
}