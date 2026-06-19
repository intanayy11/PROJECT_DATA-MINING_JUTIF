import pandas as pd
from mlxtend.frequent_patterns import apriori, association_rules
from mlxtend.preprocessing import TransactionEncoder
from collections import Counter
import warnings
warnings.filterwarnings('ignore')

# =====================
# 1. LOAD & NORMALISASI
# =====================
print("📂 Loading dataset...")
df = pd.read_excel('dataset_clean.xlsx')
df = df.dropna(subset=['keywords_clean'])
df['keywords_clean'] = df['keywords_clean'].astype(str)

normalisasi = {
    'convolutional neural network': 'cnn',
    'convolutional neural networks': 'cnn',
    'convolutional neural network (cnn)': 'cnn',
    'support vector machine': 'svm',
    'support vector machines': 'svm',
    'support vector machines (svm)': 'svm',
    'naïve bayes': 'naive bayes',
    'naïve bayes classifier': 'naive bayes',
    'naive bayes classifier': 'naive bayes',
    'k-nearest neighbor': 'knn',
    'k-nearest neighbors': 'knn',
    'k-nearest neighbour': 'knn',
    'k nearest neighbor': 'knn',
    'k-means clustering': 'k-means',
    'long short-term memory': 'lstm',
    'long short term memory': 'lstm',
    'internet of things': 'iot',
    'natural language processing': 'nlp',
    'artificial neural network': 'neural network',
    'artificial neural networks': 'neural network',
    'deep neural network': 'neural network',
    'information systems': 'information system',
    'recommender system': 'recommendation system',
    'yolov8': 'yolo',
    'recurrent neural network': 'rnn',
    'recurrent neural networks': 'rnn',
}

def normalize(kw_str):
    items = [normalisasi.get(k.strip().lower(), k.strip().lower()) for k in kw_str.split(',') if k.strip()]
    return list(set(items))

all_transactions = [normalize(kw) for kw in df['keywords_clean']]

# =====================
# 2. FILTER TOP KEYWORDS
# =====================
all_kw = [kw for t in all_transactions for kw in t]
freq_all = Counter(all_kw)

# Ambil keyword yang muncul >= 8x
TOP_KEYWORDS = {kw for kw, cnt in freq_all.items() if cnt >= 8}
print(f"✓ Top keywords (≥8x): {len(TOP_KEYWORDS)}")

# Filter tiap transaksi, hanya simpan keyword yang masuk top
transactions = []
for t in all_transactions:
    filtered = [kw for kw in t if kw in TOP_KEYWORDS]
    if len(filtered) >= 1:
        transactions.append(filtered)

print(f"✓ Artikel dengan top keyword: {len(transactions)}")

freq = Counter([kw for t in transactions for kw in t])
print(f"\nTop 20 keyword:")
for kw, cnt in freq.most_common(20):
    print(f"  {kw:<35} {cnt:>4}x  support={cnt/len(transactions):.4f}")

# =====================
# 3. ENCODE & APRIORI
# =====================
print("\n⚙️  Encoding...")
te = TransactionEncoder()
te_array = te.fit_transform(transactions)
df_te = pd.DataFrame(te_array, columns=te.columns_)
print(f"✓ Matrix: {df_te.shape[0]} artikel x {df_te.shape[1]} keyword")

print("\nTest support values:")
for sup in [0.05, 0.04, 0.03, 0.025, 0.02]:
    fi = apriori(df_te, min_support=sup, use_colnames=True, max_len=3)
    r = association_rules(fi, metric="confidence", min_threshold=0.2) if len(fi) > 1 else pd.DataFrame()
    print(f"  support={sup}: {len(fi)} itemsets, {len(r)} rules")

# Pakai support 0.03
print("\n⚙️  Apriori final (support=0.03, confidence=0.25)...")
frequent_itemsets = apriori(df_te, min_support=0.03, use_colnames=True, max_len=3)
frequent_itemsets['length'] = frequent_itemsets['itemsets'].apply(len)
print(f"✓ Frequent itemsets: {len(frequent_itemsets)}")
print(f"  - 1-itemset: {len(frequent_itemsets[frequent_itemsets['length']==1])}")
print(f"  - 2-itemset: {len(frequent_itemsets[frequent_itemsets['length']==2])}")
print(f"  - 3-itemset: {len(frequent_itemsets[frequent_itemsets['length']==3])}")

# =====================
# 4. ASSOCIATION RULES
# =====================
rules = association_rules(frequent_itemsets, metric="confidence", min_threshold=0.25)
rules = rules.sort_values('lift', ascending=False)
rules['antecedents'] = rules['antecedents'].apply(lambda x: ', '.join(sorted(x)))
rules['consequents'] = rules['consequents'].apply(lambda x: ', '.join(sorted(x)))

print(f"\n✓ Total rules: {len(rules)}")
print(f"\n🏆 Top 15 Rules (by Lift):")
print(rules[['antecedents','consequents','support','confidence','lift']].head(15).to_string(index=False))

# =====================
# 5. SIMPAN KE EXCEL
# =====================
print("\n💾 Menyimpan...")
with pd.ExcelWriter('hasil_apriori_jutif.xlsx', engine='openpyxl') as writer:

    rules_export = rules[['antecedents','consequents','support','confidence','lift','leverage','conviction']].copy()
    for col in ['support','confidence','lift','leverage','conviction']:
        rules_export[col] = rules_export[col].round(4)
    rules_export.to_excel(writer, sheet_name='Association Rules', index=False)

    fi_export = frequent_itemsets.copy()
    fi_export['itemsets'] = fi_export['itemsets'].apply(lambda x: ', '.join(sorted(x)))
    fi_export['support'] = fi_export['support'].round(4)
    fi_export.sort_values('support', ascending=False).to_excel(writer, sheet_name='Frequent Itemsets', index=False)

    rules.head(20)[['antecedents','consequents','support','confidence','lift']].to_excel(
        writer, sheet_name='Top 20 by Lift', index=False)

    rules.sort_values('confidence', ascending=False).head(20)[
        ['antecedents','consequents','support','confidence','lift']].to_excel(
        writer, sheet_name='Top 20 by Confidence', index=False)

    pd.DataFrame({
        'Metric': ['Total Artikel (semua)', 'Artikel dengan top keyword',
                   'Top Keywords (≥8x muncul)', 'Min Support', 'Min Confidence',
                   'Frequent Itemsets', 'Total Rules', 'Max Lift', 'Avg Confidence'],
        'Value': [len(all_transactions), len(transactions),
                  len(TOP_KEYWORDS), '0.03 (3%)', '0.25 (25%)',
                  len(frequent_itemsets), len(rules),
                  round(rules['lift'].max(), 4) if len(rules) > 0 else 0,
                  round(rules['confidence'].mean(), 4) if len(rules) > 0 else 0]
    }).to_excel(writer, sheet_name='Ringkasan', index=False)

print(f"✅ Selesai! hasil_apriori_jutif.xlsx tersimpan")
print(f"\n📊 Ringkasan:")
print(f"   Total artikel         : {len(all_transactions)}")
print(f"   Artikel terfilter     : {len(transactions)}")
print(f"   Frequent itemsets     : {len(frequent_itemsets)}")
print(f"   Association rules     : {len(rules)}")
if len(rules) > 0:
    print(f"   Max lift              : {rules['lift'].max():.4f}")
    print(f"   Avg confidence        : {rules['confidence'].mean():.4f}")