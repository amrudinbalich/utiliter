### Utiliter

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