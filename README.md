# Utiliter

## Docker / Kontenjerizacija

Aplikacija dolazi u sa dockerom, kako bi se osigurao local development sa istim depencencijima na više mašina.
Requirement za pokretanje aplikacije je imati docker instaliran na mašini.
Aplikacija dolazi zipovana, ali u slučaju da je potreban versioning, takoðer repository je i na Githubu.

## Testovi

Testovi su coverani sa PHPUnti testing frameworkom i oni pokrivaju esencijalne funkcionalnosti u 'aplikaciji'.
Sve testove mozete naci u ```tests``` folderu.

Testovi se pokrecu dok su u docker kontenjeru, sa komandom:
```bash
docker compose exec php ./vendor/bin/phpunit
```

Usput, baci pogled na ```docker-compose.yml``` - vidis nesto zanimljivo u servisima?
Dev baza i test baza su odvojeni :)

To znaci da prilikom performanja feature testova podaci koji su u dev bazi ostaju netaknuti.

---

## 🚀 Pokretanje aplikacije

1. unzip / git clone https://github.com/amrudinbalich/utiliter  
2. pokrenuti:

```bash
docker compose up -d
```

3. composer install

⚠️ Portovi definisani u `docker-compose.yml` moraju biti slobodni.

Port aplikacije: http://localhost:8080/

---

## ⚙️ Tehnologije

- **Server:** nginx  
- **Backend:** PHP 8.5 (PHP-FPM)  
- **Baza:** MySQL 8.4 (dev i test)

---

## 🗄️ Database dijagram

[Database diagram](docs/database-diagram.png)  
[Database ERD Link](https://dbdiagram.io/d/697f95ddbd82f5fce244f840)

---

## 📌 Zadatak 1 — ECB Import

Otvoriti:  
`http://localhost:8080/zadatak1/`

Skripta dohvaća najnovije tečajeve sa ECB-a i importuje ih u bazu podataka.  
Ako su podaci za trenutni datum već importovani, prikazuje se informativna poruka i preskače unos.

**Servis:**  
`src/Services/EcbImport.php`

---

## 📌 Zadatak 2 — JSON Import

Otvoriti:  
`http://localhost:8080/zadatak2.php`

Skripta učitava `storage/data.json` i importuje proizvode u bazu.  
Response vraća JSON prikaz importovanih podataka kao potvrdu funkcionalnosti.

### Struktura tablica

- `zadatak2_kategorije` — jedinstvene kategorije proizvoda  
- `zadatak2_proizvodjaci` — normalizirani proizvođači  
- `zadatak2_produkti` — proizvodi sa FK referencama  

### Proces normalizacije

Izvorni JSON je denormaliziran (flat struktura), gdje se kategorije i proizvođači ponavljaju uz svaki proizvod.  
Servis vrši transformaciju u relacijsku strukturu prije inserta.

**Koraci:**

1. Parsiranje JSON-a  
2. Ekstrakcija jedinstvenih kategorija i normalizacija proizvođača (`"Marka 1"` → `"Marka1"`)  
3. Insert kategorija i proizvođača  
4. Mapiranje (`naziv → id`)  
5. Insert proizvoda sa FK referencama  

**Servis:**  
`src/Services/ProductImport.php`

---

## 📌 Zadatak 3 — XML Import + Filter

### Import

Otvoriti:  
`http://localhost:8080/zadatak3-import.php`

Skripta učitava `storage/data.xml` i importuje proizvode u bazu.

### Filter / Showcase

Otvoriti:  
`http://localhost:8080/zadatak3.php`

Prikaz proizvoda zavisi od odabranog jezika.  
Proizvodi bez odgovarajućeg prijevoda se **ne prikazuju**.

Dostupni jezici: `HR` | `EN`

### Struktura tablica

- `zadatak3_categories`
- `zadatak3_manufacturers`
- `zadatak3_produkti`
- `zadatak3_opisi`
- `zadatak3_extras`

### Prijevodi

UI labele su izdvojene u `storage/translations.php`, što omogućava lako dodavanje novih jezika bez izmjene view logike.

**Servis:**  
`src/Services/XmlProductImport.php`

---

## 📌 Zadatak 4 — Step Counter

Otvoriti:  
`http://localhost:8080/zadatak4.php`

Jednostavna aplikacija za brojanje koraka putem klikova — bez JavaScript-a.  
State se čuva u PHP sesiji.

### Ponašanje

- Početno: *"Kreni hodati"*  
- Nakon klika: *"Prošli ste XY koraka"*  
- Nakon 10 koraka:  
  *"Čestitamo, prošli ste 10 koraka u XY sec/min!"*  

Brojač se automatski resetuje nakon limita.

Klikom na **Reset** dugme proces se vraća na početno stanje.  
Maksimalni broj koraka: **10**

**Servis:**  
`src/Services/StepCounter.php`
