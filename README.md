# Utiliter

Aplikacija dolazi u sa dockerom, kako bi se osigurao local development sa istim depencencijima na više mašina.
Requirement za pokretanje aplikacije je imati docker instaliran na mašini.
Aplikacija dolazi zipovana, ali u slučaju da je potreban versioning, takoðer repository je i na Githubu.

Github repo link: [https://github.com/amrudinbalich/utiliter](https://github.com/amrudinbalich/utiliter)

Pokretanje aplikacije:

1. unzip / git clone [https://github.com/amrudinbalich/utiliter](https://github.com/amrudinbalich/utiliter)
2. `docker compose up -d`

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

#### Struktura tablica

Podaci su normalizirani u pet tablica:

- `zadatak3_categories` — jedinstvene kategorije iz XML-a
- `zadatak3_manufacturers` — jedinstveni proizvođači iz XML-a
- `zadatak3_produkti` — atributi proizvoda s FK referencama na kategorije i proizvođače
- `zadatak3_opisi` — naslovi i sadržaj po jeziku, FK na `zadatak3_produkti` (one-to-many)
- `zadatak3_extras` — dinamički extra atributi proizvoda (EAV pattern)

#### Prijevodi

UI labele su externalizirane u `storage/translations.php` — dodavanje novog jezika ne zahtijeva izmjenu view logike.

#### Dedicated servis

`src/Services/XmlProductImport.php`

Servis exposeuje jednu javnu metodu `import(): void` koja interno orkestrira parsiranje XML-a, normalizaciju i unos podataka u bazu.

---

### Zadatak 4 — Step Counter

Otvoriti `localhost:8080/zadatak4.php` u browseru.

Stranica broji korake klikom na link — bez JavaScripta, state se čuva u PHP sesiji.

**Ponašanje:**
- Početni tekst: *"Kreni hodati"*
- Nakon svakog klika: *"Prošli ste XY koraka"*
- Nakon 10 koraka: *"Čestitamo, prošli ste 10 koraka u XY sec/min!"* — brojač se automatski resetuje

Klikom na dugme 'Reset' citav proces se resetuje, vazno je napomenuti da je interni max limit za korake 10.

#### Dedicated servis

`src/Services/StepCounter.php`

OOP klasa koja enkapsulira svu logiku brojanja — inkrement, reset, provjera završetka i formatiranje prikaza. State se čuva u `$_SESSION`.