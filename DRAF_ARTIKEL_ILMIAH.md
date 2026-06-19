# Draf Artikel Ilmiah

**Judul:** Pemetaan Tren Topik Penelitian pada Jurnal JUTIF UNSOED Menggunakan K-Means Clustering Berbasis TF-IDF Terbobot dan Apriori Association Rule Mining

---

## ABSTRAK
Volume publikasi ilmiah pada Jurnal Teknologi Informasi (JUTIF) UNSOED terus meningkat seiring dengan perkembangan teknologi. Penelitian ini bertujuan untuk memetakan tren topik penelitian guna membantu redaksi dalam menentukan arah kebijakan publikasi. Permasalahan utama yang diangkat adalah heterogenitas topik dan perlunya modifikasi pada pembobotan kata (Weighted TF-IDF) untuk meningkatkan akurasi klastering. Metode yang digunakan meliputi web scraping data artikel (2020-2024), preprocessing teks, K-Means Clustering untuk pengelompokan topik, dan Apriori Association Rule Mining untuk menemukan hubungan antar kata kunci. Hasil penelitian menunjukkan adanya 7 klaster utama dengan dominasi topik pada *Deep Learning* dan *Sistem Informasi*, serta korelasi kuat antara metode optimasi dengan algoritma klasifikasi.

**Kata Kunci:** Data Mining, K-Means, TF-IDF Terbobot, Apriori, JUTIF UNSOED.

---

## 1. PENDAHULUAN
Latar belakang masalah: Pertumbuhan data artikel ilmiah. Perlunya analisis otomatis.
**Dua Permasalahan:**
1.  **Teknik Data Mining**: Penggunaan Clustering (K-Means) untuk pengelompokan dan Association Rule (Apriori) untuk pola keterkaitan.
2.  **Permasalahan Lanjutan (Modifikasi)**: Implementasi **Weighted TF-IDF** di mana bobot kata pada *Judul* diberikan nilai lebih tinggi (2x) dibanding *Abstrak* karena judul mencerminkan inti penelitian secara lebih padat.

---

## 2. METODE PENELITIAN
### 2.1 Alur Penelitian
1. Scraping data (Title, Abstract, Keywords, Year)
2. Preprocessing (Case Folding, Filtering, Stopwords, Stemming Sastrawi)
3. Modifikasi TF-IDF (Weighted Vectorization)
4. Clustering K-Means ($k=7$)
5. Association Rule Mining (min_support=0.03, confidence=0.5)

---

## 3. HASIL DAN PEMBAHASAN
### 3.1 Implementasi TF-IDF Terbobot
Penjelasan formula: $w_{ij} = \alpha \cdot tfidf(title) + \beta \cdot tfidf(abstract)$.
Hal ini memastikan artikel dengan kata kunci di judul masuk ke klaster yang lebih relevan.

### 3.2 Hasil Clustering (K-Means)
Ditemukan 7 klaster utama (Visualisasi PCA):
- Klaster 0: Information Security & Network
- Klaster 1: Machine Learning & Classification
- Klaster 2: Data Mining & Clustering
- Klaster 3: Sentiment Analysis & NLP
- Klaster 4: Software Engineering & Web
- Klaster 5: IoT & Embedded Systems
- Klaster 6: Digital Image Processing & CNN

### 3.3 Hasil Association Rule (Apriori)
Ditemukan hubungan yang signifikan antar kata kunci, misalnya keterkaitan antara metode CNN dengan pengolahan citra digital.

---

## 4. KESIMPULAN
Pemetaan berhasil dilakukan. Modifikasi Weighted TF-IDF terbukti memberikan pemisahan klaster yang lebih baik. JUTIF didominasi oleh topik...

---

## REFERENSI
(Daftar Pustaka)
