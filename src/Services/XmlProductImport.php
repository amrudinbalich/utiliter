<?php

namespace App\Utiliter\Services;

use App\Utiliter\Contracts\ImportInterface;
use App\Utiliter\Foundation\Database;
use SimpleXMLElement;

class XmlProductImport implements ImportInterface
{
    public function __construct(private Database $db) {}

    public function import(): void
    {
        $xml = simplexml_load_string(data: storage('data.xml'));

        foreach ($xml->CatalogProduct as $product) {
            $this->insertCategory((string) $product['category']);
            $this->insertManufacturer((string) $product['manufacturer']);
        }

        // get maps
        $categoryMap = $this->buildMap('zadatak3_categories');
        $manufacturerMap = $this->buildMap('zadatak3_manufacturers');
    
        foreach ($xml->CatalogProduct as $product) {
            $id = $this->insertProduct($product, $categoryMap, $manufacturerMap);
            $this->insertDescription($id, $product);
            $this->insertExtras($id, $product);
        }
    }

    /**
     * Before inserting products, fetch existing categories/manufacturers
     * from database with its title/id.
     * @param string $table
     * @return array
     */
    // implement DRY Principle
    private function buildMap(string $table): array
    {
        $rows = $this->db->fetchAll("SELECT id, name FROM {$table}");
        $map = [];
        foreach ($rows as $row) {
            $map[$row->name] = $row->id;
        }
        return $map;
    }

    private function insertProduct(SimpleXMLElement $p, array $categoryMap, array $manufacturerMap): int
    {
        // todo: implement dto
        return $this->db->execute(
            'INSERT IGNORE INTO zadatak3_produkti 
                (code, category_id, manufacturer_id, tax, tax_include_in_price,
                basic_price, discount_percent, available_qty, visible,
                disable_added_to_cart, weight, minimum_qty, position, loyalty_point)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
            [
                (string) $p['code'],
                $categoryMap[(string) $p['category']] ?? null,
                $manufacturerMap[(string) $p['manufacturer']] ?? null,
                (float) $p['tax'] ?: null,
                (int) $p['tax_include_in_price'] ?: 0,
                (float) $p['basic_price'] ?: null,
                (float) $p['discount_percent'] ?: 0,
                (int) $p['available_qty'] ?: 0,
                (int) $p['visible'] ?: 1,
                (int) $p['disable_added_to_cart'] ?: 0,
                (float) $p['weight'] ?: null,
                (int) $p['minimum_qty'] ?: null,
                (int) $p['position'] ?: null,
                (int) $p['loyalty_point'] ?: null,
            ]
        );
    }

    private function insertCategory(string $name): void
    {
        if (empty($name)) return;
        $this->db->execute(
            "INSERT IGNORE INTO zadatak3_categories (name) VALUES (?)",
            [$name]
        );
    }

    private function insertManufacturer(string $name): void
    {
        if (empty($name)) return;
        $this->db->execute(
            "INSERT IGNORE INTO zadatak3_manufacturers (name) VALUES (?)",
            [$name]
        );
    }

    private function insertDescription(int $productId, SimpleXMLElement $p): void
    {
        foreach ($p->Description as $desc) {
            // skip inserting descriptions if missing
            if (empty((string) $desc->Title)) {
                continue;
            }

            $this->db->execute(
                "INSERT IGNORE INTO zadatak3_opisi (product_id, lang, title, content)
                VALUES (?, ?, ?, ?)",
                [
                    $productId,
                    (string) $desc['lang'],
                    (string) $desc->Title,
                    (string) $desc->Content,
                ]
            );
        }
    }

    private function insertExtras(int $productId, SimpleXMLElement $p): void
    {
        if (!empty((string) $p['extra_categories'])) {
            // split comma values and insert into table
            foreach (explode(',', (string) $p['extra_categories']) as $cat) {
                $this->db->execute(
                    "INSERT IGNORE INTO zadatak3_extras (product_id, key_name, value) VALUES (?, ?, ?)",
                    [$productId, 'extra_category', trim($cat)]
                );
            }
        }

        if (!empty((string) $p['extra_price_trgovina'])) {
            $this->db->execute(
                "INSERT IGNORE INTO zadatak3_extras (product_id, key_name, value) VALUES (?, ?, ?)",
                [$productId, 'extra_price_trgovina', (string) $p['extra_price_trgovina']]
            );
        }
    }

}