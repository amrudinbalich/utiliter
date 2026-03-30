<?php

namespace App\Utiliter\Services;

use App\Utiliter\Contracts\ImportInterface;
use App\Utiliter\Foundation\Database;

class ProductImport implements ImportInterface
{
    public function __construct(private Database $db) {}

    /**
     * Import the JSON data into database.
     * @return void
     */
    public function import(): void
    {
        // prepare data
        $data = json_decode(storage('data.json'), associative: true);
        [$categories, $manufacturers, $products] = $this->normalize($data);

        // perform inserts
        $this->insertCategories($categories);
        $this->insertManufacturers($manufacturers);
        $this->insertProducts(
            products: $products,
            categoryMap: $this->buildMap('zadatak2_kategorije'),
            manufacturerMap: $this->buildMap('zadatak2_proizvodjaci')
        );
    }

    /**
     * Parse and normalize the JSON structure before performing an input.
     * @param array $data
     * @return array<array>
     */
    private function normalize(array $data): array
    {
        // category names
        $categories = array_values(array_filter(
            array_unique(array_column($data, column_key: 'kategorija'))
        ));

        // normalized manufacturer names
        $manufacturers = array_values(array_unique(array_filter(
            array_map(fn($m) => normalizeString($m),
            array_filter(array_column($data, 'proizvodjac')))
        )));

        // normalized products
        $products = array_map(fn($p) => [
            'robaid' => $p['robaid'],
            'sifra' => $p['SIfra'],
            'barcode' => $p['barcode'] ?? $p['barcode2'] ?? null,
            'naziv' => $p['naziv'],
            'opis' => $p['opis'] ?? null,
            'specifikacija' => $p['specifikacija'] ?? null,
            'cijena' => $p['MPCijena'],
            'cijena_popust' => $p['mpcijenapopust'] ?? $p['MPCijena'], // default mpcijena
            'jedinica_mjere' => $p['jm'] ?? null,
            'stanje' => $p['stanje'] ?? 0, // quantity
            'status' => $p['status'] ?? 1, // 0 or 1
            'dob' => $p['dob'] ?? null,
            'spol' => $p['spol'] ?? null,
            'kategorija' => $p['kategorija'] ?? null, // kategorija_id (INT OR NULL)
            'proizvodjac' => isset($p['proizvodjac']) ? normalizeString($p['proizvodjac']) : null, // proizvodjac_id (INT OR NULL)
        ], $data);

        return [
            $categories,
            $manufacturers,
            $products
        ];
    }

    /**
     * Insert categories.
     * @param array $categories
     * @return void
     */
    private function insertCategories(array $categories): void
    {
        foreach ($categories as $naziv) {
            $this->db->execute(
                "INSERT IGNORE INTO zadatak2_kategorije (naziv) VALUES (?)",
                [$naziv]
            );
        }
    }

    /**
     * Insert manufacturers.
     * @param array $manufacturers
     * @return void
     */
    private function insertManufacturers(array $manufacturers): void
    {
        foreach ($manufacturers as $naziv) {
            $this->db->execute(
                "INSERT IGNORE INTO zadatak2_proizvodjaci (naziv) VALUES (?)",
                [$naziv]
            );
        }
    }

    /**
     * Before inserting products, fetch existing categories/manufacturers
     * from database with its title/id.
     * @param string $table
     * @return array
     */
    private function buildMap(string $table): array
    {
        $rows = $this->db->fetchAll("SELECT id, naziv FROM {$table}");
        $map = [];
        foreach ($rows as $row) {
            $map[$row->naziv] = $row->id;
        }
        return $map;
    }

    /**
     * Insert data into products with foreign keys (if any).
     * @param array $products
     * @param array $categoryMap
     * @param array $manufacturerMap
     * @return void
     */
    private function insertProducts(array $products, array $categoryMap, array $manufacturerMap): void
    {
        foreach ($products as $p) {
            $this->db->execute(
                "INSERT IGNORE INTO zadatak2_produkti
                    (robaid, sifra, barcode, naziv, opis, specifikacija,
                    cijena, cijena_popust, jedinica_mjere, stanje, status,
                    dob, spol, kategorija_id, proizvodjac_id)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
                [
                    $p['robaid'],
                    $p['sifra'],
                    $p['barcode'],
                    $p['naziv'],
                    $p['opis'],
                    $p['specifikacija'],
                    $p['cijena'],
                    $p['cijena_popust'],
                    $p['jedinica_mjere'],
                    $p['stanje'],
                    $p['status'],
                    $p['dob'],
                    $p['spol'],
                    isset($p['kategorija']) ? ($categoryMap[$p['kategorija']] ?? null) : null,
                    isset($p['proizvodjac']) ? ($manufacturerMap[$p['proizvodjac']] ?? null) : null
                ]
            );
        }
    }

}