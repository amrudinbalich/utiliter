<?php

namespace App\Utiliter\DTO;

use App\Utiliter\Contracts\DTOInterface;

class ProductDTO implements DTOInterface
{

    public static function fromArray(array $data): array
    {
        return [
            'robaid' => (int) $data['robaid'],
            'sifra' => (int) $data['SIfra'],
            'barcode' => $data['barcode'] ?? $data['barcode2'] ?? null,
            'naziv' => $data['naziv'],
            'opis' => $data['opis'] ?? null,
            'specifikacija' => $data['specifikacija'] ?? null,
            'cijena' => (float) $data['MPCijena'],
            'cijena_popust' => (float) ($data['mpcijenapopust'] ?? $data['MPCijena']),
            'jedinica_mjere' => $data['jm'] ?? null,
            'stanje' => (float) ($data['stanje'] ?? 0),
            'status' => (int) ($data['status'] ?? 1),
            'dob' => $data['dob'] ?? null,
            'spol' => $data['spol'] ?? null,
            'kategorija' => isset($data['kategorija']) ? (string) $data['kategorija'] : null,
            'proizvodjac' => isset($data['proizvodjac']) ? normalizeString($data['proizvodjac']) : null,
        ];
    }

    public static function toIndex(array $data): array
    {
        return array_values(
            array: self::fromArray($data)
        );
    }

}