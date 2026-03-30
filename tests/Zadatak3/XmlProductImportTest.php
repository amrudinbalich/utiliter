<?php

namespace Tests\Zadatak3;

use App\Utiliter\Foundation\Database;
use App\Utiliter\Services\XmlProductImport;
use PHPUnit\Framework\TestCase;

class XmlProductImportTest extends TestCase
{
    private Database $db;
    private XmlProductImport $xmlImport;

    protected function setUp(): void
    {
        $this->db = new Database();
        $this->db->execute('SET FOREIGN_KEY_CHECKS=1');
        $this->xmlImport = new XmlProductImport($this->db);
    }

    protected function tearDown(): void
    {
        $this->db->execute('SET FOREIGN_KEY_CHECKS=0');

        $this->db->execute('TRUNCATE TABLE zadatak3_extras');
        $this->db->execute('TRUNCATE TABLE zadatak3_opisi');
        $this->db->execute('TRUNCATE TABLE zadatak3_produkti');
        $this->db->execute('TRUNCATE TABLE zadatak3_categories');
        $this->db->execute('TRUNCATE TABLE zadatak3_manufacturers');
    }

    public function test_import_inserts_all_related_data(): void
    {
        $this->xmlImport->import();

        $categories = $this->db->fetchAll('SELECT * FROM zadatak3_categories');
        $manufacturers = $this->db->fetchAll('SELECT * FROM zadatak3_manufacturers');
        $products = $this->db->fetchAll('SELECT * FROM zadatak3_produkti');
        $descriptions = $this->db->fetchAll('SELECT * FROM zadatak3_opisi');
        $extras = $this->db->fetchAll('SELECT * FROM zadatak3_extras');

        $this->assertNotEmpty($categories);
        $this->assertNotEmpty($manufacturers);
        $this->assertNotEmpty($products);
    }
}