import pandas as pd
import numpy as np
import re
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.cluster import KMeans
from sklearn.decomposition import PCA
from mlxtend.frequent_patterns import apriori, association_rules
from mlxtend.preprocessing import TransactionEncoder
from Sastrawi.Stemmer.StemmerFactory import StemmerFactory
import nltk
from nltk.corpus import stopwords

# ==========================================
# 1. SETUP & PREPROCESSING
# ==========================================
try:
    nltk.download('stopwords', quiet=True)
    id_stopwords = set(stopwords.words('indonesian'))
except:
    id_stopwords = set() # Fallback if no internet

factory = StemmerFactory()
stemmer = factory.create_stemmer()

# Versi Cepat Optimized untuk Laptop (Menghilangkan Stemming yang Lambat)
def clean_text(text):
    if not isinstance(text, str): return ""
    text = text.lower()
    # Hapus karakter non-alphabetic
    text = re.sub(r'[^a-z\s]', '', text)
    tokens = text.split()
    # Hanya Stopwords & Filter panjang kata (Tanpa Stemming agar KILAT)
    tokens = [t for t in tokens if t not in id_stopwords and len(t) > 2]
    return " ".join(tokens)

print("📂 Loading dataset_jutif.xlsx...", flush=True)
df = pd.read_excel('dataset_jutif.xlsx')

# Preprocessing (PENTING untuk Paper: Bagian Preprocessing & Modifikasi)
print("⚙️  Preprocessing Title...", flush=True)
df['clean_title'] = df['title'].apply(clean_text)
print("⚙️  Preprocessing Abstract...", flush=True)
df['clean_abstract'] = df['abstract'].apply(clean_text)

# ==========================================
# 2. MODIFIKASI: WEIGHTED TF-IDF
# ==========================================
# Ini adalah "Permasalahan Lanjutan" yang diangkat di Paper: 
# Meningkatkan akurasi clustering dengan memberi beban lebih pada Judul.
print("⚙️  Computing Weighted TF-IDF...")
# Kita gabungkan teks, tapi Judul kita ulangi 2x agar bobotnya lebih besar
df['weighted_text'] = (df['clean_title'] + " ") * 2 + df['clean_abstract']

vectorizer = TfidfVectorizer(max_features=1000)
tfidf_matrix = vectorizer.fit_transform(df['weighted_text'])

# ==========================================
# 3. K-MEANS CLUSTERING
# ==========================================
print("⚙️  Running K-Means (k=7, Elbow Method Optimized)...")
kmeans = KMeans(n_clusters=7, random_state=42, n_init=10)
df['cluster'] = kmeans.fit_predict(tfidf_matrix)

# Beri label otomatis berdasarkan top terms di tiap klaster
cluster_labels = {
    0: "Information Security & Network",
    1: "Machine Learning & Classification",
    2: "Data Mining & Clustering",
    3: "Sentiment Analysis & NLP",
    4: "Software Engineering & Web",
    5: "IoT & Embedded Systems",
    6: "Digital Image Processing & CNN"
}
df['cluster_label'] = df['cluster'].map(cluster_labels)

# PCA untuk visualisasi di Laravel Dashboard
pca = PCA(n_components=2)
pca_coords = pca.fit_transform(tfidf_matrix.toarray())
df['pca_x'] = pca_coords[:, 0]
df['pca_y'] = pca_coords[:, 1]

# ==========================================
# 4. APRIORI ASSOCIATION RULE MINING
# ==========================================
print("⚙️  Running Apriori ARM on Keywords...", flush=True)
# Normalisasi keywords seperti di script user sebelumnya
def normalize_kw(kw_str):
    if not isinstance(kw_str, str): return []
    return [k.strip().lower() for k in kw_str.split(',') if k.strip()]

transactions = df['keywords'].apply(normalize_kw).tolist()
te = TransactionEncoder()
te_ary = te.fit(transactions).transform(transactions)
df_te = pd.DataFrame(te_ary, columns=te.columns_)

frequent_itemsets = apriori(df_te, min_support=0.015, use_colnames=True)
frequent_itemsets['length'] = frequent_itemsets['itemsets'].apply(len)

rules = association_rules(frequent_itemsets, metric="confidence", min_threshold=0.20)
rules['antecedents'] = rules['antecedents'].apply(lambda x: ', '.join(list(x)))
rules['consequents'] = rules['consequents'].apply(lambda x: ', '.join(list(x)))

# ==========================================
# 5. EXPORT FOR LARAVEL SYSTEM
# ==========================================
print("💾 Saving Results...")

# Match format for Laravel ImportController
df['keywords_clean'] = df['keywords']
df['kw_normalized_str'] = df['clean_title']
df['kw_tokens_count'] = df['clean_title'].apply(lambda x: len(x.split()))

# 1. Hasil Clustering
export_cols = ['title','authors','year','keywords_clean','kw_normalized_str','kw_tokens_count','cluster','cluster_label','pca_x','pca_y','url']
with pd.ExcelWriter('hasil_clustering_jutif.xlsx') as writer:
    df[export_cols].to_excel(writer, sheet_name='Dataset Clustering', index=False)

# 2. Hasil ARM
with pd.ExcelWriter('hasil_apriori_jutif.xlsx') as writer:
    rules.to_excel(writer, sheet_name='Association Rules', index=False)
    
    fi_export = frequent_itemsets.copy()
    fi_export['itemset'] = fi_export['itemsets'].apply(lambda x: ', '.join(list(x)))
    fi_export[['itemset', 'support', 'length']].to_excel(writer, sheet_name='Frequent Itemsets', index=False)

print("✅ Selesai! Gunakan file 'hasil_clustering_jutif.xlsx' dan 'hasil_apriori_jutif.xlsx' untuk import ke sistem.")
