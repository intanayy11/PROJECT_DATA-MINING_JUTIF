# REVISI LENGKAP BAB 3 (HASIL DAN PEMBAHASAN JURNAL)
*Catatan untuk Nadine: Silakan salin teks di bawah ini ke dalam draf Jurnal MS Word kamu di bagian Bab 3.*

---

## 3.1. Analisis Kata Kunci (Frequent Itemsets)
Tahap pertama dari penemuan pengetahuan adalah mengidentifikasi seberapa sering suatu topik atau algoritma muncul dalam koleksi artikel JUTIF. Berdasarkan analisis frekuensi kata kuci (dengan minimal support 0.015), ditemukan bahwa topik dasar seperti **Machine Learning** menempati posisi teratas dengan nilai *support* sebasar **0.0789** (muncul di 7% dari keseluruhan data). Algoritma populer lainnya seperti **CNN** juga terekam sebagai salah satu kata kunci dengan kemunculan tinggi (support = **0.0344**). Hal ini menunjukkan adanya kecenderungan ketertarikan peneliti pada pendekatan kecerdasan buatan dan jaringan saraf tiruan. *(Lihat Tabel 1 pada Gambar 2/Web).*

## 3.2. Penentuan Nilai k Optimal (Metode Elbow & Silhouette)
Sebelum mengelompokkan data menggunakan K-Means, ditentukan jumlah klaster ($k$) yang paling ideal untuk mencegah *overfitting* atau pengelompokan yang terlalu kasar. Evaluasi dilakukan pada $k=2$ hingga $k=10$ dengan menganalisis Nilai *Inertia* (SSE) menggunakan Metode Elbow, didampingi pengujian *Silhouette Score*. 
Berdasarkan Gambar grafik evaluasi (Gambar 3), titik patahan optimal di mana inertia mulai menurun stabil beririsan dengan *Silhouette Score* yang cukup baik berada pada posisi **$k=7$**. Oleh karena itu, penelitian ini membagi artikel ke dalam 7 tema utama.

## 3.3. Hasil K-Means Clustering & Distribusi Topik 
Setelah pembobotan *Weighted TF-IDF* dikonfigurasi (memberikan prioritas pada judul artikel), 988 artikel dikelompokkan menjadi 7 klaster (Visualisasi Pie/Bar Chart pada Gambar 4). Adapun hasil nama kelompok beserta proporsinya adalah sebagai berikut:

- **C0 (Information Security & Network)**: 77 artikel (7,7%)
- **C1 (Machine Learning & Classification)**: 45 artikel (4,5%)
- **C2 (Data Mining & Clustering)**: 117 artikel (11,8%)
- **C3 (Sentiment Analysis & NLP)**: 408 artikel (41,2%)
- **C4 (Software Engineering & Web)**: 96 artikel (9,7%)
- **C5 (IoT & Embedded Systems)**: 42 artikel (4,2%)
- **C6 (Digital Image Processing & CNN)**: 203 artikel (20,5%)

**Pembahasan:** Topik penelitian JUTIF didominasi secara masif oleh **Klaster 3 (Sentiment Analysis & NLP)** yang meraup 41,2% dari total publikasi. Sebaliknya, penelitian terkait perangkat keras seperti **Klaster 5 (IoT & Embedded Systems)** menjadi yang paling sedikit diminati (hanya 4,2%).

## 3.4. Dinamika dan Tren Penelitian (2022–2026)
Analisis lanjutan dilakukan untuk melihat bagaimana ketertarikan pada topik-topik tersebut berevolusi dari waktu ke waktu (Gambar 6). Berdasarkan plot tren per tahun:
1. **Topik Stabil:** Klaster C3 (Sentiment Analysis) secara konstan berada di posisi puncak setiap tahunnya dengan volume publikasi tertinggi.
2. **Topik Naik Daun (*Trending*):** Klaster **C6 (Digital Image Processing)** menunjukkan lonjakan paling tajam, tumbuh dari jumlah yang sangat minimum di tahun 2022 hingga meledak drastis dengan ratusan jurnal di kisaran tahun 2024-2025. Performa ini menjadikan C6 sebagai *hot topic* mahasiswa.

## 3.5. Penemuan Aturan Asosiasi dengan Apriori (ARM)
Untuk memetakan pola metodologi mahasiswa, algoritma Apriori menghasilkan *Association Rules* yang memvisualisasikan korelasi algoritma pendamping (seperti ditunjukkan pada Network Graph di Gambar 7). Ditemukan **4 aturan terkuat** di dalam ekosistem publikasi, di mana dua temuan paling bermakna adalah:

1. **Aturan 1 (Sentiment Analysis ↔ SVM)**: Terdapat kecenderungan mutlak bahwa penelitian yang mengangkat studi kasus *Sentiment Analysis* akan dipasangkan dengan algoritma *Support Vector Machine (SVM)*, dengan nilai observasi support 0.0202 (lift ratio positif kuat).
2. **Aturan 2 (Machine Learning ↔ Random Forest)**: Algoritma menangkap pola yang sangat solid (Lift Ratio = 4.06) antara kata kunci *Random Forest* dan domain *Machine Learning*. Fakta penemuan ini mensahihkan hipotesis bahwa Random Forest dianggap sebagai algoritma pokok untuk klasifikasi numerik dan kategorikal di mata civitas akademika JUTIF.
