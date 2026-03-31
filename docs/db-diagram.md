# Database Diagram Documentation

See live docs on: [dbdiagram.io](https://dbdiagram.io/d/697f95ddbd82f5fce244f840)
See migration files on: ```sql/schema.sql```

---

## Tables

### zadatak1_ecb
```sql
id int [pk, increment]
currency varchar(3) [not null]
rate decimal(10,4) [not null]
date date [not null]

indexes {
  (currency, date) [unique]
}
```

### zadatak2_kategorije
```sql
id int [pk, increment]
naziv varchar(60) [not null, unique]
```

### zadatak2_proizvodjaci
```sql
id int [pk, increment]
naziv varchar(60) [not null, unique]
```

### zadatak2_produkti
```sql
id int [pk, increment]
robaid int [unique]
sifra int [not null]
barcode varchar(20)
naziv varchar(120) [not null]
opis text
specifikacija text
cijena decimal(10,2) [not null]
cijena_popust decimal(10,2)
jedinica_mjere varchar(20)
stanje int [default: 0]
status boolean [default: 1]
dob varchar(20)
spol varchar(5)
kategorija_id int
proizvodjac_id int
```

### zadatak3_categories
```sql
id int [pk, increment]
name varchar(60) [not null, unique]
```

### zadatak3_manufacturers
```sql
id int [pk, increment]
name varchar(60) [not null, unique]
```

### zadatak3_produkti
```sql
id int [pk, increment]
code varchar(50) [not null, unique]
category_id int
manufacturer_id int
tax decimal(5,2)
tax_include_in_price boolean [default: 0]
basic_price decimal(10,2)
discount_percent decimal(5,2) [default: 0]
available_qty int [default: 0]
visible boolean [default: 1]
disable_added_to_cart boolean [default: 0]
weight decimal(10,3)
minimum_qty int
position int
loyalty_point int
```

### zadatak3_opisi
```sql
id int [pk, increment]
product_id int [not null]
lang varchar(5) [not null]
title varchar(255)
content text

indexes {
  (product_id, lang) [unique]
}
```

### zadatak3_extras
```sql
id int [pk, increment]
product_id int [not null]
key_name varchar(50) [not null]
value varchar(255)
```

---

## Foreign Key References

- zadatak2_produkti.kategorija_id → zadatak2_kategorije.id
- zadatak2_produkti.proizvodjac_id → zadatak2_proizvodjaci.id

- zadatak3_produkti.category_id → zadatak3_categories.id
- zadatak3_produkti.manufacturer_id → zadatak3_manufacturers.id

- zadatak3_opisi.product_id → zadatak3_produkti.id
- zadatak3_extras.product_id → zadatak3_produkti.id

