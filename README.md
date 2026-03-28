### Kontenjerazacija

Aplikacija dolazi u sa dockerom, kako bi se osigurao local development sa istim depencencijima na više mašina.

Requirement za pokretanje aplikacije je imati docker instaliran na mašini.

Aplikacija dolazi zipovana, ali u slučaju da je potreban versioning, takoðer repository je i na Githubu.

Github link: https://github.com/amrudinbalich/utiliter

Pokretanje aplikacije:
1. unzip / git clone https://github.com/amrudinbalich/utiliter
2. ```docker compose up -d```

Portovi podeseni na docker-compose.yml fajlu bi trebali biti nezauzeti.
Ukoliko build kojim slucajem faila, docker_rebuild.sh je skripta koja rusi kontenjer i ponovo ga pokrece - sluzi generalno za manji debugging. Ako skripta failuje da se pokrene (executa), u tom slucaju joj se moraju dati permisije za egzekuciju: ```chmod +x docker_rebuild.sh```

Struktura aplikacije / dependenciji:
1. Server: nginx
2. Server-side jezik: PHP / PHP-FPM (Fast CGI Process)
3. SQL DBMS: mySQL