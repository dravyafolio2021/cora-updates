#!/usr/bin/env python3
"""
Cora Studio OS - Instant IndexNow & Search Engine Ping Engine
Notifies IndexNow (Bing, Yandex, Seznam) and search engines of updated URL index.
"""
import urllib.request
import json
import ssl

BASE_URL = "https://heycora.in"
INDEXNOW_KEY = "cora-studio-indexnow-2026"
URLS_TO_INDEX = [
    f"{BASE_URL}/",
    f"{BASE_URL}/features/",
    f"{BASE_URL}/ai-agent/",
    f"{BASE_URL}/use-cases/",
    f"{BASE_URL}/pricing/",
    f"{BASE_URL}/compare/",
    f"{BASE_URL}/compare/cora-vs-honeybook/",
    f"{BASE_URL}/compare/cora-vs-studio-ninja/",
    f"{BASE_URL}/compare/cora-vs-hubspot/",
    f"{BASE_URL}/compare/cora-vs-docusign/",
    f"{BASE_URL}/compare/cora-vs-gohighlevel/",
    f"{BASE_URL}/compare/cora-vs-clickup/",
    f"{BASE_URL}/compare/cora-vs-zoho/",
    f"{BASE_URL}/compare/cora-vs-freshbooks/",
    f"{BASE_URL}/tools/",
    f"{BASE_URL}/tools/gst-calculator/",
    f"{BASE_URL}/tools/listing-ai/",
    f"{BASE_URL}/about/",
    f"{BASE_URL}/contact/",
    f"{BASE_URL}/changelog/",
    f"{BASE_URL}/status/",
    f"{BASE_URL}/terms/",
    f"{BASE_URL}/privacy/",
    f"{BASE_URL}/refund-policy/",
    f"{BASE_URL}/security/",
    f"{BASE_URL}/sla/",
    f"{BASE_URL}/sitemap.xml",
    f"{BASE_URL}/llms.txt"
]

def ping_indexnow():
    payload = {
        "host": "heycora.in",
        "key": INDEXNOW_KEY,
        "keyLocation": f"{BASE_URL}/{INDEXNOW_KEY}.txt",
        "urlList": URLS_TO_INDEX
    }
    
    ctx = ssl.create_default_context()
    ctx.check_hostname = False
    ctx.verify_mode = ssl.CERT_NONE
    
    endpoints = [
        "https://api.indexnow.org/indexnow",
        "https://www.bing.com/indexnow"
    ]
    
    data = json.dumps(payload).encode('utf-8')
    
    for ep in endpoints:
        try:
            req = urllib.request.Request(
                ep,
                data=data,
                headers={"Content-Type": "application/json; charset=utf-8"}
            )
            with urllib.request.urlopen(req, context=ctx, timeout=10) as res:
                print(f"IndexNow Ping to {ep}: Status {res.getcode()}")
        except Exception as e:
            print(f"IndexNow Ping to {ep}: {e}")

if __name__ == "__main__":
    print(f"=== Pinging Search & AI Crawlers with {len(URLS_TO_INDEX)} URLs ===")
    ping_indexnow()
    print("=== Indexing Ping Completed ===")
