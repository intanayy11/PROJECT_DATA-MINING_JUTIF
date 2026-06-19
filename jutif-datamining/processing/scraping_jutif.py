import requests
from bs4 import BeautifulSoup
import pandas as pd
import time

BASE = "https://jutif.if.unsoed.ac.id/index.php/jurnal"

def get_all_issue_urls():
    url = BASE + "/issue/archive"
    res = requests.get(url, timeout=10)
    soup = BeautifulSoup(res.text, "html.parser")
    links = soup.select("a[href*='/issue/view/']")
    urls = list(set([a['href'] for a in links]))
    print(f"✓ Ditemukan {len(urls)} issue")
    return urls

def get_article_urls(issue_url):
    res = requests.get(issue_url, timeout=10)
    soup = BeautifulSoup(res.text, "html.parser")
    # coba beberapa selector
    links = soup.select("h3.title a") or soup.select(".obj_article_summary .title a") or soup.select("a[href*='/article/view/']")
    return list(set([a['href'] for a in links]))

def get_article_detail(url):
    res = requests.get(url, timeout=10)
    soup = BeautifulSoup(res.text, "html.parser")

    title    = soup.select_one("h1.page_title") or soup.select_one("h1")
    authors  = soup.select(".authors .name") or soup.select(".author")
    keywords = soup.select_one(".item.keywords .value") or soup.select_one(".keywords")
    abstract = soup.select_one(".item.abstract p") or soup.select_one(".abstract p")
    year     = soup.select_one(".item.published .value") or soup.select_one(".published")

    return {
        "title"    : title.text.strip() if title else "",
        "authors"  : ", ".join([a.text.strip() for a in authors]),
        "keywords" : keywords.text.strip() if keywords else "",
        "abstract" : abstract.text.strip() if abstract else "",
        "year"     : year.text.strip() if year else "",
        "url"      : url
    }

# ======= MAIN =======
all_data = []
issues = get_all_issue_urls()

for issue_url in issues:
    print(f"\n📂 Scraping: {issue_url}")
    try:
        article_urls = get_article_urls(issue_url)
        print(f"   → {len(article_urls)} artikel ditemukan")

        for art_url in article_urls:
            try:
                detail = get_article_detail(art_url)
                all_data.append(detail)
                print(f"  ✓ {detail['title'][:60]}")
                time.sleep(0.8)
            except Exception as e:
                print(f"  ✗ Error artikel: {e}")
    except Exception as e:
        print(f"  ✗ Error issue: {e}")

# Simpan ke Excel
df = pd.DataFrame(all_data)
df.to_excel("dataset_jutif.xlsx", index=False)
print(f"\n✅ Selesai! {len(df)} artikel tersimpan di dataset_jutif.xlsx")