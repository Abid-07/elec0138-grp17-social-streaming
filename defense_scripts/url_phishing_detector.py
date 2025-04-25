import pandas as pd
import re
from urllib.parse import urlparse
from xgboost import XGBClassifier
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.pipeline import Pipeline
from sklearn.compose import ColumnTransformer
from sklearn.preprocessing import StandardScaler
from sklearn.model_selection import train_test_split
from sklearn.metrics import classification_report

# ---------- Step 1: Load and Balance Dataset ----------

url = pd.read_csv("phishing_site_urls.csv")

# Balance the dataset
bad_urls = url[url['Label'] == 'bad']
good_urls = url[url['Label'] == 'good'].sample(n=len(bad_urls), random_state=42)
balanced_url = pd.concat([bad_urls, good_urls]).sample(frac=1, random_state=42).reset_index(drop=True)

# ---------- Step 2: Feature Engineering ----------

phishing_keywords = ['login', 'verify', 'account', 'banking', 'update', 'secure', 'password', 'confirm', 'signin']

def has_ip(url):
    return 1 if re.match(r'^(http[s]?://)?\d{1,3}(\.\d{1,3}){3}', url) else 0

def extract_features(df):
    df = df.copy()
    df['url_length'] = df['URL'].apply(len)
    df['dot_count'] = df['URL'].apply(lambda x: x.count('.'))
    df['has_ip'] = df['URL'].apply(has_ip)
    df['has_suspicious_word'] = df['URL'].apply(lambda x: int(any(word in x.lower() for word in phishing_keywords)))
    df['has_at'] = df['URL'].apply(lambda x: 1 if '@' in x else 0)
    df['double_slash'] = df['URL'].apply(lambda x: 1 if x.count('//') > 1 else 0)
    df['slash_count'] = df['URL'].apply(lambda x: x.count('/'))
    df['is_https'] = df['URL'].apply(lambda x: 1 if x.lower().startswith('https://') else 0)
    df['query_param_count'] = df['URL'].apply(lambda x: len(urlparse(x).query.split('&')) if urlparse(x).query else 0)
    df['has_port_number'] = df['URL'].apply(lambda x: 1 if ':' in urlparse(x).netloc else 0)
    df['tld'] = df['URL'].apply(lambda x: urlparse(x).netloc.split('.')[-1] if '.' in urlparse(x).netloc else 'none')
    
    # One-hot encode TLDs
    df = pd.get_dummies(df, columns=['tld'], drop_first=True)
    return df

# Apply feature extraction
url_features = extract_features(balanced_url)

# ---------- Step 3: Define Features and Labels ----------

numerical_features = ['url_length', 'dot_count', 'has_ip', 'has_suspicious_word', 'has_at',
                      'double_slash', 'slash_count', 'is_https', 'query_param_count', 'has_port_number']

X = url_features.drop(columns=['URL', 'Label'])
y = url_features['Label'].apply(lambda x: 1 if x == 'bad' else 0)

# Add raw URL string back for TF-IDF vectorization
X['url_raw'] = balanced_url['URL']

# Train-test split
X_train, X_test, y_train, y_test = train_test_split(X, y, stratify=y, random_state=42)

# ---------- Step 4: Preprocessing and Pipeline ----------

tfidf = TfidfVectorizer(analyzer='char_wb', ngram_range=(3, 5), max_features=500)

preprocessor = ColumnTransformer(transformers=[
    ('num', StandardScaler(), numerical_features),
    ('txt', tfidf, 'url_raw')
])

pipeline = Pipeline([
    ('preprocessor', preprocessor),
    ('clf', XGBClassifier(n_estimators=200, max_depth=7, learning_rate=0.1,
                          use_label_encoder=False, eval_metric='logloss'))
])

# ---------- Step 5: Train and Evaluate ----------

pipeline.fit(X_train, y_train)
y_pred = pipeline.predict(X_test)

print("\n📊 Classification Report:\n")
print(classification_report(y_test, y_pred))
