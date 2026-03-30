<?php

namespace Tests\Zadatak1;

use App\Utiliter\Foundation\Database;
use App\Utiliter\Services\EcbImport;
use PHPUnit\Framework\TestCase;

class EcbImportTest extends TestCase
{
    private Database $db;
    private EcbImport $ecbImport;

    protected function setUp(): void
    {
        $this->db = new Database();
        $this->ecbImport = new EcbImport($this->db);
    }

    protected function tearDown(): void
    {
        $this->db->execute('TRUNCATE TABLE zadatak1_ecb');
    }

    public function test_import_inserts_currencies(): void
    {
        $this->ecbImport->import();

        $currencies = $this->db->fetchAll('SELECT * FROM zadatak1_ecb');
        $this->assertNotEmpty($currencies);
    }

    public function test_import_does_not_duplicate(): void
    {
        $this->ecbImport->import();
        $this->ecbImport->import();

        $count = count($this->db->fetchAll('SELECT * FROM zadatak1_ecb'));
        $this->assertLessThanOrEqual(29, $count);
    }

    public function test_status_text_on_fresh_import(): void
    {
        $this->ecbImport->import();

        $this->assertStringContainsString(
            'imported', 
            $this->ecbImport->getImportTextStatus()
        );
    }

    public function test_status_text_when_already_imported(): void
    {
        $this->ecbImport->import();
        
        $secondImport = new EcbImport($this->db);
        $secondImport->import();

        $this->assertStringContainsString(
            'already imported', 
            $secondImport->getImportTextStatus()
        );
    }
}