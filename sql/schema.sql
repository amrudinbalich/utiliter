CREATE TABLE zadatak1_ecb (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    currency VARCHAR(3) NOT NULL,
    rate DECIMAL(10, 4) NOT NULL,
    date DATE NOT NULL,
    UNIQUE KEY unique_currency_date (currency, date)
);

CREATE TABLE zadatak2_kategorije (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    naziv VARCHAR(60) NOT NULL UNIQUE
);

CREATE TABLE zadatak2_proizvodjaci (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    naziv VARCHAR(60) NOT NULL UNIQUE
);

CREATE TABLE zadatak2_produkti (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    robaid INT UNSIGNED UNIQUE,
    sifra INT NOT NULL,
    barcode VARCHAR(20),

    naziv VARCHAR(120) NOT NULL,
    opis TEXT,
    specifikacija TEXT,

    cijena DECIMAL(10, 2) NOT NULL,
    cijena_popust DECIMAL(10, 2),

    jedinica_mjere VARCHAR(20),
    stanje INT DEFAULT 0,
    status TINYINT(1) DEFAULT 1,
    dob VARCHAR(20),
    spol VARCHAR(5),

    kategorija_id INT UNSIGNED,
    proizvodjac_id INT UNSIGNED,
    FOREIGN KEY (kategorija_id) REFERENCES zadatak2_kategorije(id),
    FOREIGN KEY (proizvodjac_id) REFERENCES zadatak2_proizvodjaci(id)
);

CREATE TABLE zadatak3_categories (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(60) NOT NULL UNIQUE
);

CREATE TABLE zadatak3_manufacturers (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(60) NOT NULL UNIQUE
);

CREATE TABLE IF NOT EXISTS zadatak3_produkti (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) NOT NULL UNIQUE,
    category_id INT UNSIGNED,
    manufacturer_id INT UNSIGNED,
    tax DECIMAL(5, 2),
    tax_include_in_price TINYINT(1) DEFAULT 0,
    basic_price DECIMAL(10, 2),
    discount_percent DECIMAL(5, 2) DEFAULT 0,
    available_qty INT DEFAULT 0,
    visible TINYINT(1) DEFAULT 1,
    disable_added_to_cart TINYINT(1) DEFAULT 0,
    weight DECIMAL(10, 3),
    minimum_qty INT,
    position INT,
    loyalty_point INT,
    FOREIGN KEY (category_id) REFERENCES zadatak3_categories(id),
    FOREIGN KEY (manufacturer_id) REFERENCES zadatak3_manufacturers(id)
);

CREATE TABLE zadatak3_opisi (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_id INT UNSIGNED NOT NULL,
    lang VARCHAR(5) NOT NULL,
    title VARCHAR(255),
    content TEXT,
    FOREIGN KEY (product_id) REFERENCES zadatak3_produkti(id),
    UNIQUE KEY unique_product_lang (product_id, lang)
);

CREATE TABLE zadatak3_extras (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_id INT UNSIGNED NOT NULL,
    key_name VARCHAR(50) NOT NULL,
    value VARCHAR(255),
    FOREIGN KEY (product_id) REFERENCES zadatak3_produkti(id)
);