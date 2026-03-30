<?php

namespace App\Utiliter\Services;

use App\Utiliter\Contracts\ImportInterface;
use App\Utiliter\Foundation\Database;
use GuzzleHttp\Client;

class EcbImport implements ImportInterface
{

    const string ECB = 'https://www.ecb.europa.eu/stats/eurofxref/eurofxref-daily.xml';

    private string $date;

    private bool $alreadyImported = false;

    public function __construct(
        private Database $db, 
        private Client $client = new Client()
    ) {}

    /**
     * Import latest central bank currency data for this date.
     * @return void
     */
    public function import(): void
    {
        [$date, $currencyRates] = $this->fetch();
        $this->date = $date;

        if($this->currenciesExists($date)) {
            $this->alreadyImported = true;
            return;
        }

        $this->insertCurrencies($date, $currencyRates);
    }

    /**
     * Import finished? Then inform user that it is imported.
     * If the import is finished in this runtime, then notify about the status.
     * @return string
     */
    public function getImportTextStatus(): string
    {
        if ($this->alreadyImported) {
            return "<strong>info:</strong> Currencies for <strong>{$this->date}</strong> are already imported.";
        }
    
        return "Currencies imported for (date): <strong>{$this->date}</strong>";
    }

    /**
     * Fetch ECB Data from cloud.
     * @return array [ date, currencyRates[] ]
     */
    private function fetch(): array
    {
        $response = $this->client->get(self::ECB);
        $contents = $response->getBody()->getContents();

        $envelope = simplexml_load_string($contents);

        $date = (string) $envelope->Cube->Cube['time'];
        $currencyRates = [];

        foreach ($envelope->Cube->Cube->Cube as $cube) {
            $currencyRates[] = [
                'currency' => (string) $cube['currency'],
                'rate' => (float) $cube['rate']
            ];
        }

        return [$date, $currencyRates];
    }

    /**
     * Check if currencies for a given date already exist in the database.
     * @param string $date
     * @return bool
     */
    private function currenciesExists(string $date): bool
    {
        $existing = $this->db->fetch(
            'SELECT id FROM zadatak1_ecb WHERE date = ? LIMIT 1',
            [$date]
        );

        return $existing !== false;
    }

    /**
     * Bulk insert currencies into the database.
     * @param string $date
     * @param array $currencyRates
     * @return void
     */
    private function insertCurrencies(string $date, array $currencyRates): void
    {
        $placeholders = implode(', ', array_fill(0, count($currencyRates), '(?, ?, ?)'));

        $params = [];
        foreach ($currencyRates as $row) {
            $params[] = $row['currency'];
            $params[] = $row['rate'];
            $params[] = $date;
        }

        $this->db->execute(
            "INSERT IGNORE INTO zadatak1_ecb (currency, rate, date) VALUES {$placeholders}",
            $params
        );
    }

}