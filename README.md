# ELEC0138 GRP 17 – Digital Social Streaming Platform

This is a secure web-based social streaming platform built for ELEC0138. It allows users to stream videos, purchase credits securely, and includes an AI-powered phishing detection system to flag suspicious messages.

---

## Requirements

- Ubuntu 22.04+ (VM or local machine)
- Apache2
- PHP 8.x
- MySQL/MariaDB
- Python 3.10+
- pip (Python package manager)

---

## Setup Instructions

### 1. Clone the repository

```bash
git clone https://github.com/Abid-07/elec0138-grp17-social-streaming.git
cd elec0138-grp17-social-streaming
```

### 2. Move project into your Apache directory

```bash
sudo mv elec0138-grp17-social-streaming /opt/lampp/lampp/htdocs/social_streaming
cd /opt/lampp/lampp/htdocs/social_streaming
```

### 3. Setup the MySQL Database

#### Using phpMyAdmin (Recommended for Local XAMPP/Ubuntu VM Setup)

1. Start your Apache and MySQL services via XAMPP control panel or by running:

   ```bash
   sudo /opt/lampp/lampp start
   ```

2. Open your browser and go to:

   ```
   http://localhost/phpmyadmin
   ```

3. In the left sidebar, click **New** to create a new database.

4. Enter the name:

   ```
   social_streaming
   ```

   Leave collation as default and click **Create**.

5. After the database is created, click on **Import** tab in the top menu.

6. Click **Choose File** and select the provided SQL file (e.g., `social_streaming_db.sql`).

7. Scroll down and click **Go** to run the import.

8. Once complete, you should see all required tables (`users`, `videos`, `messages`, etc.) inside the `social_streaming` database.

>  If no `.sql` file is available, ensure you manually create required tables as per your PHP code (e.g., `users`, `videos`, `messages`, `purchases`, `comments`, `used_tokens`).

### 4. Configure Database Connection

Update `db.php` with your MySQL credentials:

```php
<?php
$conn = new mysqli("localhost", "root", "", "social_streaming");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
```

---

## Python AI Detection Setup

### 5. Install Required Python Dependencies

```bash
sudo apt update
sudo apt install python3-pip -y
pip3 install -r requirements.txt
```

### 6. Ensure your model file exists

Ensure this file exists in your project:

```
url_pipeline.pkl
```

---

## AI-Based Phishing Detection

The AI detection script is located at:

```bash
/opt/lampp/lampp/htdocs/social_streaming/hybrid_inference.py
```

When users send messages (via `send_message.php`), the platform calls this script using:

```bash
python3 hybrid_inference.py "message content"
```

If a URL is flagged as phishing, it gets stored in the database (`ai_flag` column in `messages` table).

---

## Snort Setup for AI-Based Phishing Detection

### 1.  Check Snort is Installed
```bash
snort -V
```
If not installed:
```bash
sudo apt update
sudo apt install snort
```

---

### 2.  Add AI Alert Rule to `local.rules`
Open Snort’s local rules file:
```bash
sudo nano /etc/snort/rules/local.rules
```
Add this rule at the bottom:
```snort
alert tcp any any -> any 12345 (msg:"⚠️ AI Detected Phishing Attempt"; content:"AI_PREDICTED_PHISHING"; sid:1000009; rev:1;)
```
Save and exit.

---

### 3. 🔧 Make Sure Snort Loads `local.rules`
Edit `snort.conf`:
```bash
sudo nano /etc/snort/snort.conf
```
Find and verify this line is **uncommented** (no `#` in front):
```snort
include $RULE_PATH/local.rules
```
Also confirm `RULE_PATH` is set correctly:
```snort
var RULE_PATH /etc/snort/rules
```

---

### 4. Run Snort in Listening Mode (Loopback)
```bash
sudo snort -A console -q -c /etc/snort/snort.conf -i lo
```
Explanation:
- `-A console`: Print alerts to terminal
- `-q`: Quiet mode (suppress stats)
- `-c`: Path to snort.conf
- `-i lo`: Monitor the loopback interface

---

### 5. Triggering Snort from Python (Already in Your Script)
Your Python script (e.g. `hybrid_inference.py`) should include:
```python
import socket

def trigger_snort_alert():
    try:
        s = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
        s.connect(("127.0.0.1", 12345))
        s.send(b"AI_PREDICTED_PHISHING")
        s.close()
    except:
        pass
```
When `trigger_snort_alert()` is called, Snort will capture the signature and show the alert.

---

### Result
Now when your AI classifies a message as phishing and sends `"AI_PREDICTED_PHISHING"` to port `12345`, Snort will flag it with your custom message in the terminal.



## Uploads & File Permissions

Ensure upload directories exist and are writable:

```bash
mkdir -p uploads/videos uploads/profile_pics
chmod -R 777 uploads
```

---

## Running the Site

Start XAMPP (Apache + MySQL):

```bash
sudo /opt/lampp/lampp start
```

> Make sure both Apache and MySQL are running. You can verify by visiting:
>
> http://localhost/phpmyadmin — for database access  
> http://localhost/social_streaming/ — for the main site

---

## Demo Accounts

You may pre-register accounts via the `registration.php` page or manually insert users into the `users` table.

---

## Directory Structure

```
social_streaming/
│
├── includes/               # External PHP libraries (JWT, GoogleAuthenticator)
├── uploads/                # Uploaded videos and profile images
├── hybrid_inference.py     # Phishing detection Python script
├── url_pipeline.pkl        # Trained ML model for URL classification
├── *.php                   # Web application logic (frontend/backend)
└── requirements.txt        # Python dependencies
```

---


### Demonstrating Security Features & Simulations

This platform includes a prototype donation system integrated with secure credit transfer between users. The following steps can be used to test its behavior and observe protections:

(Note: Aside from loopback mode on Wireshark, Kali Linux will not detect localhost on a different VM, therefore a Digital Ocean Server (209.38.160.133) is provided temporarily, to use on the 'victim' device.)

#### Testing Donation & Observing Encrypted Data

1. **Navigate to a Paid Video Page:**
   - After logging in, go to a paid video’s comment page via:  
     ```
     http://209.38.160.133/social_streaming/video_comments.php?id=<video_id>
     ```

2. **Make a Donation:**
   - Use the **Donate Credits** form to donate to the video uploader.

3. **Capture Network Traffic (Loopback - Wireshark):**
   - Open **Wireshark**, and start capturing traffic on the **loopback interface (`lo` or `Loopback`)**.
   - Filter by `http` or `tcp.port == 80` to observe **unencrypted HTTP packets**.
   - Look for the `POST` request to `video_comments.php` and examine the form data submitted during the donation.
   - You will observe **obfuscated or encrypted** fields related to the transaction (homomorphic simulation).
  
#### Buying Credits with ZTA

1. **Navigate to a Buy Credits Page:**
   - After logging in, you can top up 'credits' via:  
     ```
     http://209.97.187.133/social_streaming/buy_credits.php
     ```

2. **Check link for JWT Token:**
   - Use the **Generate New JWT** button to generate a temporary token.
  

4. **Check link for MFA:**
   - Use the **Setup MFA** button, scan/ enter code to setup using Google Authenticator.
   - Enter time limited code on Authenticator app.
  
You can purchase 'credits', but the same JWT Token cannot be used for multiple purchases, as part of the preventative measures for replay/ session attacks.

#### Simulated Attacks (Optional)

You can further test the security of the donation process using Kali Linux tools:

##### Option 1: MITM Proxying
- Use **`mitmproxy`** to intercept traffic on the HTTP endpoint.
- Attempt to alter or replay a donation request.
- The system’s transaction integrity checks should prevent improper manipulation.

##### Option 2: Bettercap Spoofing
- Run `bettercap` on Kali Linux to observe session-level manipulation.
- Use HTTP module to monitor payloads.

>  _These simulations demonstrate an understanding of how real-world attacks might target credit transfer systems, and how defensive strategies (like transaction validation and encryption) can mitigate such risks._


## Full Working Setup Recap: HTTP POST Interception and Replay (Bettercap + mitmproxy)

This guide demonstrates how to simulate interception and replay attacks using `mitmproxy` and `Bettercap`. **Note**: Network interface names (e.g., `eth0`) and local IPs will differ based on your machine setup. Use `ip addr` to confirm your actual interface and IP.

---

### 1. Start mitmproxy in transparent mode:

```bash
sudo mitmproxy --mode transparent -p 8080
```

---

### 2. Enable IP forwarding:

```bash
echo 1 | sudo tee /proc/sys/net/ipv4/ip_forward
```

---

### 3. Set up iptables redirection (VERY important):

Replace `eth0` with your actual interface.

```bash
sudo iptables -t nat -A PREROUTING -i eth0 -p tcp --dport 80 -j REDIRECT --to-port 8080
sudo iptables -t nat -A PREROUTING -i eth0 -p tcp --dport 443 -j REDIRECT --to-port 8080
```

---

### 4. Start Bettercap:

```bash
sudo bettercap -iface eth0
```

---

### Inside Bettercap session:

Replace `192.168.1.48` with the target machine’s IP address.

```bash
set arp.spoof.targets 192.168.1.48
arp.spoof on
```

---

You're now ready to intercept and modify HTTP POST requests for testing.



## Phishing Email Attack Simulation

This module demonstrates how an AI-driven phishing attack could be simulated using GPT-2 and user data from a social streaming platform prototype. It generates personalized phishing messages and sends them via the platform's internal messaging system.

### Requirements

Make sure you've installed all dependencies:

```bash
pip install -r requirements.txt
```

### Required Files

- `ai_phishing.py` – The main phishing email generator and sender script.
- `most_subscribed_youtube_channels.csv` – Dataset used to personalize phishing emails with top content creators.
- Ensure the platform API (e.g., `export_users.php`, `send_message.php`, `login_process.php`) is running and accessible.
    -  **Source**: [YouTube Creator Dataset - Kaggle](https://www.kaggle.com/datasets/surajjha101/top-youtube-channels-data/data)

###  How to Run

1. **Activate your environment (if applicable):**

```bash
conda activate your_env_name
# or
source venv/bin/activate
```

2. **Run the phishing simulation:**

```bash
python ai_phishing.py
```

3. The script will:
   - Skip the first 12 users.
   - Randomly select a user.
   - Use GPT-2 to generate a personalized phishing message.
   - Log in as `admin` and send the message via the platform’s internal inbox system.

### Note

This attack is simulated in a controlled environment for educational purposes only. Do **not** deploy or run this outside a sandbox or ethical testing context.



## Phishing URL Detection Model

This module demonstrates how to train a machine learning model to classify URLs as phishing or legitimate using both text-based and structural features. It leverages `XGBoost`, `scikit-learn`, and `TF-IDF` to detect potentially malicious URLs.

### Requirements

Make sure you've installed all dependencies:

```bash
pip install -r requirements.txt
```

### Required Files

- `url_phishing_detector.py` – Main script to preprocess URLs and train the classifier.
- `phishing_site_urls.csv` – Dataset containing labeled URLs with "good" or "bad" classification.
  - **Source**: [Phishing Website Detector Dataset - Kaggle](https://www.kaggle.com/datasets/taruntiwarihp/phishing-site-urls)

### How to Run

1. **Activate your environment (if applicable):**

```bash
conda activate your_env_name
# or
source venv/bin/activate
```

2. **Train the model:**

```bash
python url_phishing_detector.py
```

3. The script will:
   - Load and balance the dataset.
   - Extract both character-level TF-IDF and handcrafted features.
   - Train an XGBoost classifier.
   - Print classification metrics on the test set.
   - Save the trained model as `url_phishing_detector.pkl`.

### Note

Ensure your CSV contains a `Label` column with values "good" and "bad", and a `URL` column. The script also generates numerical features like URL length, dot count, and more.

---

This model is intended for academic and demonstration purposes and should be evaluated further before deployment in production environments.


## Author

Group 17 – ELEC0138  
UCL Security & Privacy Coursework 2024/2025
