<?php

namespace Tests\Zadatak2;

use App\Utiliter\Foundation\Database;
use App\Utiliter\Services\ProductImport;
use PHPUnit\Framework\TestCase;

class ProductImportTest extends TestCase
{
    private Database $db;
    private ProductImport $productImport;

    protected function setUp(): void
    {
        $this->db = new Database();
        $this->db->execute('SET FOREIGN_KEY_CHECKS=1');
        $this->productImport = new ProductImport($this->db);
    }

    protected function tearDown(): void
    {
        $this->db->execute('SET FOREIGN_KEY_CHECKS=0');

        $this->db->execute('TRUNCATE zadatak2_kategorije');
        $this->db->execute('TRUNCATE zadatak2_produkti');
        $this->db->execute('TRUNCATE zadatak2_proizvodjaci');
    }

    public function test_import_inserts_categories_manufacturers_and_products(): void
    {
        $this->productImport->import();

        $categories = $this->db->fetchAll('SELECT * FROM zadatak2_kategorije');
        $manufacturers = $this->db->fetchAll('SELECT * FROM zadatak2_proizvodjaci');
        $products = $this->db->fetchAll('SELECT * FROM zadatak2_produkti');

        $this->assertNotEmpty($categories);
        $this->assertNotEmpty($manufacturers);
        $this->assertNotEmpty($products);
    }
}