<?php
session_start();
include "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $receiver_id = $_POST['receiver_id'];
    $message = trim($_POST['message']);
    $ai_flag = null;

    if (!empty($receiver_id) && !empty($message)) {
        $escaped_message = escapeshellarg($message);
        $cmd = "LD_PRELOAD=/usr/lib/x86_64-linux-gnu/libstdc++.so.6 /opt/lampp/htdocs/social_streaming/venv/bin/python3 /opt/lampp/htdocs/social_streaming/hybrid_inference.py $escaped_message";

        $output = shell_exec($cmd);
        file_put_contents("ai_debug.log", "CMD: $cmd\nOutput:\n$output\n", FILE_APPEND);

        if ($output && strpos($output, '{') !== false) {
            $json_start = strpos($output, '{');
            $json = json_decode(substr($output, $json_start), true);
            if ($json && isset($json['label'])) {
                $ai_flag = $json['label'];
            }
        }

        $stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, message, ai_flag, sent_at) VALUES (?, ?, ?, ?, NOW())");
        if ($stmt) {
            $stmt->bind_param("iiss", $user_id, $receiver_id, $message, $ai_flag);
            $stmt->execute();
            $stmt->close();
            header("Location: inbox.php");
            exit;
        } else {
            echo "SQL Error: " . $conn->error;
        }
    } else {
        echo "<div style='color: red; text-align: center;'>Please fill in all fields.</div>";
    }
}

$users_stmt = $conn->prepare("SELECT id, username FROM users WHERE id != ?");
$users_stmt->bind_param("i", $user_id);
$users_stmt->execute();
$users_stmt->bind_result($id, $username);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Send Message</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f0f2f5;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 500px;
            margin: 60px auto;
            padding: 30px;
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            margin-bottom: 25px;
            color: #333;
        }
        label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
            color: #444;
        }
        select, textarea {
            width: 100%;
            padding: 10px;
            margin-top: 8px;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-size: 1rem;
            resize: vertical;
        }
        button {
            margin-top: 20px;
            width: 100%;
            padding: 12px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            cursor: pointer;
            transition: background 0.3s ease;
        }
        button:hover {
            background-color: #0056b3;
        }
        .back-link {
            text-align: center;
            margin-top: 20px;
        }
        .back-link a {
            color: #007bff;
            text-decoration: none;
            display: inline-block;
            margin-bottom: 10px;
        }
        .back-link a:hover {
            text-decoration: underline;
        }
        .home-button {
            display: inline-block;
            background-color: #28a745;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 8px;
            transition: background 0.3s ease;
        }
        .home-button:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>

    <div class="container">
        <h2>Send a Private Message</h2>

        <form method="POST" action="">
            <label for="receiver">Send to:</label>
            <select name="receiver_id" required>
                <?php while ($users_stmt->fetch()): ?>
                    <option value="<?php echo htmlspecialchars($id); ?>">
                        <?php echo htmlspecialchars($username); ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <label for="message">Message:</label>
            <textarea name="message" rows="6" required></textarea>

            <button type="submit">Send</button>
        </form>

        <div class="back-link">
            <a href="inbox.php">📬 Go to Inbox</a><br>
            <a class="home-button" href="home.php">🏠 Back to Home</a>
        </div>
    </div>

</body>
</html>

<?php $users_stmt->close(); ?>

