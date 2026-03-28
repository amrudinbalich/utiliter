CREATE TABLE zadatak1_ecb (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    currency VARCHAR(3) NOT NULL,
    rate DECIMAL(10, 4) NOT NULL,
    date DATE NOT NULL,
    UNIQUE KEY unique_currency_date (currency, date)
);