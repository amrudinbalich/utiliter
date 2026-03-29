## Utiliter

Aplikacija dolazi u sa dockerom, kako bi se osigurao local development sa istim depencencijima na više mašina.
Requirement za pokretanje aplikacije je imati docker instaliran na mašini.
Aplikacija dolazi zipovana, ali u slučaju da je potreban versioning, takoðer repository je i na Githubu.

Github repo link: https://github.com/amrudinbalich/utiliter

Pokretanje aplikacije:
1. unzip / git clone https://github.com/amrudinbalich/utiliter
2. ```docker compose up -d```

Portovi podeseni na docker-compose.yml fajlu bi trebali biti nezauzeti.

Struktura aplikacije / dependenciji:
1. Server: nginx
2. Server-side jezik: PHP 8.5 / PHP-FPM (Fast CGI Process)
3. SQL DBMS: mySQL 8.4

---

### Zadatak 1 — ECB Import

Otvoriti `localhost:8080/zadatak1/` u browseru.

Skripta fetchuje zadnje tečajeve sa ECB-a i importuje ih u bazu.
Ako su podaci za trenutni datum već importovani, prikazuje info poruku.

Dedicated servis: `src/Services/EcbImport.php`

---

### Zadatak 2 — JSON Import

Otvoriti `localhost:8080/zadatak2.php` u browseru.

Skripta učitava `storage/data.json` i importuje proizvode u bazu podataka.
Response prikazuje importovane podatke u JSON formatu kao vizuelni prikaz funkcionalnosti.

#### Struktura tablica

- `zadatak2_kategorije` — jedinstvene kategorije proizvoda
- `zadatak2_proizvodjaci` — normalizirani nazivi proizvođača
- `zadatak2_produkti` — proizvodi s foreign key referencama na kategorije i proizvođače

#### Proces normalizacije

Izvorni JSON je denormaliziran (flat struktura) — kategorije i proizvođači se ponavljaju uz svaki proizvod. Servis ih re-normalizira u relacijsku strukturu prije inserta.

**Koraci:**
1. JSON se parsira i raspoređuje u tri grupe: kategorije, proizvođači, proizvodi
2. Izvlače se jedinstvene kategorije i normaliziraju nazivi proizvođača (`"Marka 1"` → `"Marka1"`)
3. Kategorije i proizvođači se insertaju u zasebne tablice
4. Fetchaju se ID mape (`naziv → id`) za obje tablice
5. Proizvodi se insertaju s odgovarajućim `kategorija_id` i `proizvodjac_id` foreign keyevima

#### Dedicated servis

`src/Services/ProductImport.php`

Servis exposeuje jednu javnu metodu `import(): void` koja interno orkestrira normalizaciju i unos podataka u bazu.

---

### Zadatak 3 — XML Import + Filter

#### Import

Otvoriti `localhost:8080/zadatak3-import.php` u browseru.

Skripta učitava `storage/data.xml` i importuje proizvode u bazu podataka.

#### Filter / Showcase

Otvoriti `localhost:8080/zadatak3.php` u browseru.

Prikazuje proizvode ovisno o odabranom jeziku. Proizvodi koji nemaju opis na odabranom jeziku se **ne prikazuju**.

Dostupni jezici: `HR` | `EN`

---

#### Struktura tablica

- `zadatak3_produkti` — atributi proizvoda iz XML-a (`code`, `category`, `price`...)
- `zadatak3_opisi` — naslovi i sadržaj po jeziku, FK na `zadatak3_produkti`

Relacija: jedan produkt → više opisa (one-to-many)

---

#### Proces importa

1. XML se učitava sa diska (`storage/data.xml`) i parsira pomoću `SimpleXML`
2. Za svaki `CatalogProduct` — inseraju se atributi u `zadatak3_produkti`
3. Za svaki `Description` child element — inseraju se `lang`, `title`, `content` u `zadatak3_opisi`
4. Proizvodi bez opisa (`product8`, `product11`-`product20`) se importaju bez opisa

#### Filter logika
```sql
SELECT p.code, o.title, o.content 
FROM zadatak3_produkti p
INNER JOIN zadatak3_opisi o ON p.id = o.product_id
WHERE o.lang = ?
```

`INNER JOIN` osigurava da se prikazuju **samo** proizvodi koji imaju opis na odabranom jeziku.

---

#### Dedicated servis

`src/Services/XmlProductImport.php`

Servis exposeuje jednu javnu metodu `import(): void` koja interno orkestrira parsiranje XML-a i unos podataka u bazu.