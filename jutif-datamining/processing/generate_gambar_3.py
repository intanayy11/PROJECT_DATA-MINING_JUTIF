import pandas as pd
import numpy as np
import matplotlib.pyplot as plt
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.cluster import KMeans
from sklearn.metrics import silhouette_score
import re
import warnings

warnings.filterwarnings("ignore")

print("1. Membaca dataset...")
df = pd.read_excel('dataset_jutif.xlsx')

def clean_text(text):
    if not isinstance(text, str): return ""
    text = text.lower()
    text = re.sub(r'[^a-z\s]', '', text)
    tokens = [t for t in text.split() if len(t) > 2] # simple clean
    return " ".join(tokens)

print("2. Preprocessing & Pembobotan...")
df['clean_title'] = df['title'].apply(clean_text)
df['clean_abstract'] = df['abstract'].apply(clean_text)
df['weighted_content'] = (df['clean_title'] + " ") * 2 + df['clean_abstract']

tfidf = TfidfVectorizer(max_features=1000)
tfidf_matrix = tfidf.fit_transform(df['weighted_content'])

K_range = range(2, 11) # Cek k dari 2 sampai 10
inertia_vals = []
silhouette_vals = []

print("3. Menghitung Evaluasi K-Means (Estimasi 30 Detik)...")
for k in K_range:
    kmeans = KMeans(n_clusters=k, random_state=42, n_init=10)
    labels = kmeans.fit_predict(tfidf_matrix)
    inertia_vals.append(kmeans.inertia_)
    silhouette_vals.append(silhouette_score(tfidf_matrix, labels))

print("4. Menggambar Grafik Elbow & Silhouette...")
fig, ax1 = plt.subplots(figsize=(10, 6))

# Plot Inertia (Elbow) di sumbu Y kiri
color_inertia = 'tab:blue'
ax1.set_xlabel('Nilai k (Jumlah Klaster)', fontsize=12, fontweight='bold')
ax1.set_ylabel('Inertia (SSE)', color=color_inertia, fontsize=12, fontweight='bold')
line1 = ax1.plot(K_range, inertia_vals, marker='o', color=color_inertia, linewidth=2.5, markersize=8, label='Inertia Curve (Elbow)')
ax1.tick_params(axis='y', labelcolor=color_inertia)
ax1.grid(True, linestyle='--', alpha=0.5)

# Plot Silhouette di sumbu Y kanan
ax2 = ax1.twinx()
color_sil = 'tab:red'
ax2.set_ylabel('Silhouette Score', color=color_sil, fontsize=12, fontweight='bold')
line2 = ax2.plot(K_range, silhouette_vals, marker='s', color=color_sil, linewidth=2.5, markersize=8, label='Silhouette Score')
ax2.tick_params(axis='y', labelcolor=color_sil)

# Highlight k=7 (Titik Optimal Kita)
ax1.axvline(x=7, color='green', linestyle=':', linewidth=2, label='k=7 (Titik Optimal)')

# Gabung Legends
lines = line1 + line2 + [plt.Line2D([0],[0], color='green', linestyle=':', linewidth=2)]
labels = [l.get_label() for l in lines]
ax1.legend(lines, labels, loc='upper center', bbox_to_anchor=(0.5, 1.15), ncol=3)

plt.title('Evaluasi Penentuan Jumlah Klaster (Elbow Method & Silhouette Score)', pad=40, fontsize=14, fontweight='bold')
plt.tight_layout()

# Simpan Grafik
filename = "Gambar_3_Elbow_Silhouette.png"
plt.savefig(filename, dpi=300)
print(f"✅ BERHASIL! File gambar tersimpan sebagai: {filename}")
