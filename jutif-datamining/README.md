# JUTIF Research Analyzer

Web app untuk pemetaan tren topik penelitian JUTIF UNSOED menggunakan K-Means Clustering dan Apriori ARM.

## Tech Stack
- Laravel 11 + PHP 8.2
- MySQL
- Tailwind CSS + Chart.js
- PhpSpreadsheet (baca Excel)

---

## Cara Setup di Laragon

### 1. Letakkan Project
Ekstrak folder `jutif-analyzer` ke:
```
C:\laragon\www\jutif-analyzer
```

### 2. Install Dependencies
Buka Terminal Laragon (klik kanan → Terminal), lalu:
```bash
cd C:\laragon\www\jutif-analyzer
composer install
```

### 3. Setup Environment
```bash
cp .env.example .env
php artisan key:generate
```

### 4. Buat Database
- Buka phpMyAdmin (http://localhost/phpmyadmin)
- Buat database baru: `jutif_analyzer`
- Pastikan `.env` sudah sesuai:
```
DB_DATABASE=jutif_analyzer
DB_USERNAME=root
DB_PASSWORD=
```

### 5. Jalankan Migration
```bash
php artisan migrate
```

### 6. Akses Aplikasi
Buka browser: http://jutif-analyzer.test

Atau jika tidak ada virtual host, akses via:
http://localhost/jutif-analyzer/public

---

## Cara Import Data

1. Buka menu **Import Data** di sidebar
2. Upload `hasil_clustering_jutif.xlsx` → klik **Import Artikel**
3. Upload `hasil_apriori_jutif.xlsx` → klik **Import ARM**
4. Selesai! Dashboard akan langsung menampilkan data

---

## Struktur Folder Penting
```
app/
  Http/Controllers/
    DashboardController.php   — Dashboard utama
    ClusteringController.php  — K-Means results
    ArmController.php         — Association Rules
    ArticleController.php     — Data Explorer
    ImportController.php      — Import Excel
  Models/
    Article.php
    ArmRule.php
    FrequentItemset.php
database/
  migrations/                 — Skema database
resources/views/
  layouts/app.blade.php       — Template utama
  dashboard/                  — Dashboard
  clustering/                 — Halaman clustering
  arm/                        — Halaman ARM
  articles/                   — Data artikel
  import/                     — Import data
routes/web.php                — Semua routes
```

---

## Fitur
| Halaman | Deskripsi |
|---|---|
| Dashboard | Statistik, chart tren per tahun, distribusi klaster, top keywords, top ARM rules |
| K-Means Clustering | Ringkasan 7 klaster, tabel tren per tahun, klik masuk ke detail klaster |
| Detail Klaster | Daftar artikel per klaster, word cloud keywords, filter tahun/search |
| Visualisasi PCA | Scatter plot interaktif 988 artikel dalam 2D PCA space |
| Association Rules | Filter rules by lift/confidence, sortable table |
| Frequent Itemsets | Filter by length (1/2/3-itemset), search |
| Data Artikel | Search, filter tahun + klaster, pagination |
| Import Data | Upload Excel hasil analisis Python |
