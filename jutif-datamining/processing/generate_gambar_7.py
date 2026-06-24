import pandas as pd
import matplotlib.pyplot as plt
import networkx as nx
import warnings
warnings.filterwarnings("ignore")

print("1. Membaca data Top Aturan Asosiasi (Apriori)...")
try:
    # Memilih aturan paling kuat dari hasil Apriori
    df_rules = pd.read_excel('hasil_apriori_jutif.xlsx', sheet_name='Top 20 by Lift')
except Exception as e:
    df_rules = pd.read_excel('hasil_apriori_jutif.xlsx', sheet_name='Association Rules').head(20)

# Membuat Objek Jaringan (Network Graph)
G = nx.DiGraph()

print("2. Menyusun Jaringan Titik (Nodes) dan Garis (Edges)...")
for index, row in df_rules.iterrows():
    # Ambil kata asal dan kata tujuan
    ant = str(row.get('antecedents', '')).strip()
    con = str(row.get('consequents', '')).strip()
    
    # Ketebalan panah berdasarkan seberapa kuat hubungannya (Confidence)
    confidence = row.get('confidence', 0.5)
    weight = confidence * 3 
    
    # Hanya hubungkan jika kata valid
    if ant and con and ant.lower() != 'nan' and con.lower() != 'nan':
        G.add_edge(ant, con, weight=weight)

print("3. Menggambar Graph...")
plt.figure(figsize=(14, 10))

# Tata letak memencar secara otomatis seperti pegas
pos = nx.spring_layout(G, k=0.5, seed=42)

# Menggambar Bulatan (Node)
nx.draw_networkx_nodes(G, pos, node_size=3500, node_color='#dbeafe', edgecolors='#3b82f6', linewidths=2, alpha=0.9)

# Menggambar Garis Panah (Edges)
edges = nx.draw_networkx_edges(
    G, pos, 
    arrowstyle='-|>', 
    arrowsize=25, 
    edge_color='#94a3b8',
    width=[d['weight'] for u, v, d in G.edges(data=True)],
    connectionstyle='arc3,rad=0.15' # Membuat panah sedikit melengkung agar elegan
)

# Menambahkan Teks pada Bulatan
nx.draw_networkx_labels(G, pos, font_size=10, font_family="sans-serif", font_weight='bold')

plt.title('Network Graph Hubungan Kata Kunci Topik (Apriori Association Rules)', pad=20, fontsize=18, fontweight='bold', color='#1e293b')
plt.axis('off') # Sembunyikan garis pinggir
plt.tight_layout()

# Simpan Grafik
filename = 'Gambar_7_Network_Graph.png'
plt.savefig(filename, dpi=300, bbox_inches='tight', transparent=False)
print(f"✅ Selesai! Graph visualisasi jaringan tersimpan sebagai: {filename}")
