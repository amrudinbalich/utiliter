<?php

namespace App\Utiliter\Services;

use App\Utiliter\Foundation\Database;
use SimpleXMLElement;

class XmlProductImport {
    public function __construct(private Database $db) {}

    public function import(): void
    {
        $xml = simplexml_load_string(storage('data.xml'));

        foreach ($xml->CatalogProduct as $product) {
            $id = $this->insertProduct($product);
            $this->insertOpisi($id, $product);
        }
    }

    private function insertProduct(SimpleXMLElement $p): int
    {
        return $this->db->execute(
            'INSERT IGNORE INTO zadatak3_produkti 
                (code, category, manufacturer, tax, tax_include_in_price,
                basic_price, discount_percent, available_qty, visible,
                disable_added_to_cart, weight, extra_categories, extra_price,
                minimum_qty, position, loyalty_point)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
            [
                (string) $p['code'],
                (string) $p['category'] ?: null,
                (string) $p['manufacturer'] ?: null,
                (float) $p['tax'] ?: null,
                (int) $p['tax_include_in_price'] ?: 0,
                (float) $p['basic_price'] ?: null,
                (float) $p['discount_percent'] ?: 0,
                (int) $p['available_qty'] ?: 0,
                (int) $p['visible'] ?: 1,
                (int) $p['disable_added_to_cart'] ?: 0,
                (float) $p['weight'] ?: null,
                (string) $p['extra_categories'] ?: null,
                (float) $p['extra_price_trgovina'] ?: null,
                (int) $p['minimum_qty'] ?: null,
                (int) $p['position'] ?: null,
                (int) $p['loyalty_point'] ?: null,
            ]
        );
    }

    private function insertOpisi(int $productId, SimpleXMLElement $p): void
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
}