<?php

namespace App\Utiliter\Services;

use App\Utiliter\Contracts\ImportInterface;
use App\Utiliter\DTO\ProductDTO;
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
            categoryMap: $this->db->buildMap('zadatak2_kategorije', 'naziv'),
            manufacturerMap: $this->db->buildMap('zadatak2_proizvodjaci', 'naziv')
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

        $products = array_map(
            fn($p) => ProductDTO::fromArray($p), 
            $data
        );

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
                ProductDTO::toIndex($p)
            );
        }
    }

}