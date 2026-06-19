import pandas as pd
import numpy as np
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.cluster import KMeans
from sklearn.decomposition import PCA
from mlxtend.frequent_patterns import apriori, association_rules
import matplotlib.pyplot as plt
import seaborn as sns
import nltk
from nltk.corpus import stopwords
from Sastrawi.Stemmer.StemmerFactory import StemmerFactory

# Initialize Sastrawi
factory = StemmerFactory()
stemmer = factory.create_stemmer()
nltk.download('stopwords', quiet=True)
id_stopwords = set(stopwords.words('indonesian'))

def preprocess_text(text):
    if not isinstance(text, str): return ""
    # Lowercase
    text = text.lower()
    # Remove numbers and punctuation
    text = re.sub(r'[^a-zA-Z\s]', '', text)
    # Tokenize (simple split)
    tokens = text.split()
    # Remove stopwords and stem
    cleaned = [stemmer.stem(t) for t in tokens if t not in id_stopwords and len(t) > 2]
    return " ".join(cleaned)

def perform_analysis(filepath):
    print("Loading dataset...")
    df = pd.read_excel(filepath)
    
    # Fill NaN
    df['abstract'] = df['abstract'].fillna('')
    df['keywords'] = df['keywords'].fillna('')
    
    print("Preprocessing text (this may take a while)...")
    df['clean_title'] = df['title'].apply(preprocess_text)
    df['clean_abstract'] = df['abstract'].apply(preprocess_text)
    
    # PERFORMANCE MODIFICATION: Weighted TF-IDF
    # Topics in titles are given 2x weight compared to abstract
    print("Computing Weighted TF-IDF...")
    tfidf_title = TfidfVectorizer(max_features=500)
    tfidf_abstract = TfidfVectorizer(max_features=1000)
    
    matrix_title = tfidf_title.fit_transform(df['clean_title']).toarray()
    matrix_abstract = tfidf_abstract.fit_transform(df['clean_abstract']).toarray()
    
    # Normalize and Weight (Title: 0.7, Abstract: 0.3)
    # We combine them into a single feature space or just weight the combined sparse matrix
    # Simplified: Concatenate and apply weights to columns
    # But a better "Weighted TF-IDF" for topics is:
    combined_text = (df['clean_title'] + " ") * 2 + df['clean_abstract']
    tfidf_vec = TfidfVectorizer(max_features=1500)
    X = tfidf_vec.fit_transform(combined_text)
    
    # 1. K-Means Clustering
    print("Running K-Means (k=7)...")
    kmeans = KMeans(n_clusters=7, random_state=42, n_init=10)
    df['cluster'] = kmeans.fit_predict(X)
    
    # Dimensionality Reduction for Visualization (PCA)
    pca = PCA(n_components=2)
    coords = pca.fit_transform(X.toarray())
    df['pca_x'] = coords[:, 0]
    df['pca_y'] = coords[:, 1]
    
    # 2. Association Rule Mining (Apriori)
    print("Running Apriori ARM...")
    # Prepare keywords (transactions)
    # We use cleaned keywords for this
    def clean_kw(kw):
        return [k.strip().lower() for k in str(kw).split(',')] if kw else []
    
    transactions = df['keywords'].apply(clean_kw).tolist()
    
    # One-hot encoding for Apriori
    from mlxtend.preprocessing import TransactionEncoder
    te = TransactionEncoder()
    te_ary = te.fit(transactions).transform(transactions)
    df_te = pd.DataFrame(te_ary, columns=te.columns_)
    
    # Filter out empty or too rare keywords to save memory
    frequent_itemsets = apriori(df_te, min_support=0.03, use_colnames=True)
    rules = association_rules(frequent_itemsets, metric="lift", min_threshold=1.0)
    
    # Saving results for Laravel Import
    print("Saving results to Excel...")
    
    # Matching Laravel ImportController Expectations
    df['keywords_clean'] = df['keywords']
    df['kw_normalized_str'] = df['clean_title'] # Temporary mapping
    df['kw_tokens_count'] = df['clean_title'].apply(lambda x: len(x.split()))
    
    # Rename columns to match Laravel's map
    export_df = df[[
        'title', 'authors', 'year', 'keywords_clean', 'kw_normalized_str', 
        'kw_tokens_count', 'cluster', 'cluster_label', 'pca_x', 'pca_y', 'url'
    ]]
    
    # Save with specific sheet name
    with pd.ExcelWriter("hasil_clustering_jutif.xlsx") as writer:
        export_df.to_excel(writer, sheet_name='Dataset Clustering', index=False)
    
    # Save ARM rules
    rules['antecedents'] = rules['antecedents'].apply(lambda x: ', '.join(list(x)) if isinstance(x, (set, frozenset)) else x)
    rules['consequents'] = rules['consequents'].apply(lambda x: ', '.join(list(x)) if isinstance(x, (set, frozenset)) else x)
    
    with pd.ExcelWriter("hasil_apriori_jutif.xlsx") as writer:
        rules.to_excel(writer, sheet_name='Association Rules', index=False)
        
        # Also include Frequent Itemsets in the same file as per some OJS exports or common practice
        frequent_itemsets['itemset'] = frequent_itemsets['itemsets'].apply(lambda x: ', '.join(list(x)))
        frequent_itemsets['length'] = frequent_itemsets['itemsets'].apply(lambda x: len(x))
        fi_export = frequent_itemsets[['itemset', 'support', 'length']]
        fi_export.to_excel(writer, sheet_name='Frequent Itemsets', index=False)
    
    print("Process Complete! Files 'hasil_clustering_jutif.xlsx' and 'hasil_apriori_jutif.xlsx' are ready for Laravel import.")

if __name__ == "__main__":
    # perform_analysis("dataset_jutif.xlsx")
    pass
