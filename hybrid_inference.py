import re
import joblib
import numpy as np
import pandas as pd
import socket
import sys
import json

# Load trained URL pipeline
url_pipeline = joblib.load("/opt/lampp/htdocs/social_streaming/url_pipeline.pkl")

# Extract URLs
def extract_urls(text):
    pattern = r'(https?://\S+|www\.\S+|\S+\.\w{2,})'
    return re.findall(pattern, text)

# Feature engineering
def engineer_url_features(url_list):
    df = pd.DataFrame(url_list, columns=['text'])
    df['url_length'] = df['text'].apply(len)
    df['dot_count'] = df['text'].apply(lambda x: x.count('.'))
    df['has_ip'] = df['text'].str.match(r'^(http[s]?://)?\d{1,3}(\.\d{1,3}){3}').astype(int)
    df['has_suspicious_word'] = df['text'].apply(lambda x: int(any(w in x.lower() for w in ['login', 'verify', 'account'])))
    df['has_at'] = df['text'].str.contains('@').astype(int)
    df['double_slash'] = df['text'].apply(lambda x: x.count('//') > 1).astype(int)
    df['slash_count'] = df['text'].apply(lambda x: x.count('/'))
    df['is_https'] = df['text'].str.startswith('https://').astype(int)
    df['query_param_count'] = df['text'].str.count(r'\?')
    df['has_port_number'] = df['text'].str.contains(r':\d+').astype(int)
    return df

# Main logic
def classify_message(text):
    urls = extract_urls(text)
    if not urls:
        return {"label": "Safe Email", "phishing_score": 0.0, "urls_detected": []}
    features = engineer_url_features(urls)
    score = float(url_pipeline.predict_proba(features)[:, 1].mean())
    label = "Phishing Email" if score > 0.5 else "Safe Email"
    return {"label": label, "phishing_score": score, "urls_detected": urls}

# CLI entry
if __name__ == "__main__":
    import sys
    print(f"[DEBUG] sys.argv = {sys.argv}", file=sys.stderr)

    if len(sys.argv) > 1:
        message = sys.argv[1]
        result = classify_message(message)
        print(json.dumps(result, default=str))
    else:
        print(json.dumps({"label": "Safe Email", "reason": "No argument passed"}, default=str))



