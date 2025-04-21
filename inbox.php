<?php
session_start();
include "db.php";

ini_set('display_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$received_messages_stmt = $conn->prepare("SELECT m.id, m.sender_id, m.message, m.sent_at, m.is_read, m.ai_flag, u.username 
                                          FROM messages m
                                          JOIN users u ON m.sender_id = u.id
                                          WHERE m.receiver_id = ? 
                                          ORDER BY m.sent_at DESC");
$received_messages_stmt->bind_param("i", $user_id);
$received_messages_stmt->execute();
$received_messages_stmt->bind_result($message_id, $sender_id, $message, $sent_at, $is_read, $ai_flag, $sender_username);

$received_messages = [];
while ($received_messages_stmt->fetch()) {
    $received_messages[] = [
        'message_id' => $message_id,
        'sender_id' => $sender_id,
        'sender_username' => $sender_username,
        'message' => $message,
        'sent_at' => $sent_at,
        'is_read' => $is_read,
        'ai_flag' => $ai_flag
    ];
}

// Mark unread messages as read
foreach ($received_messages as $message) {
    if ($message['is_read'] == 0) {
        $update_read_status_stmt = $conn->prepare("UPDATE messages SET is_read = 1 WHERE id = ?");
        $update_read_status_stmt->bind_param("i", $message['message_id']);
        $update_read_status_stmt->execute();
        $update_read_status_stmt->close();
    }
}

$received_messages_stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Inbox</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f0f2f5;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 800px;
            margin: 40px auto;
            padding: 20px;
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #333;
        }
        .message {
            background-color: #f9f9f9;
            border-left: 6px solid #007bff;
            padding: 15px 20px;
            margin-bottom: 20px;
            border-radius: 8px;
            position: relative;
        }
        .message.new {
            background-color: #e8f9e8;
            border-left-color: #28a745;
        }
        .sender {
            font-weight: bold;
            color: #333;
        }
        .time {
            font-size: 0.85em;
            color: #888;
            margin-top: 5px;
        }
        .message-text {
            margin-top: 10px;
            font-size: 1rem;
            line-height: 1.4;
            color: #444;
        }
        .new-message {
            color: #28a745;
            font-weight: bold;
            position: absolute;
            top: 15px;
            right: 20px;
        }
        .phishing-alert {
            color: red;
            font-weight: bold;
            margin-top: 10px;
        }
        .return-button {
            text-align: center;
            margin-top: 40px;
        }
        .return-button a {
            display: inline-block;
            padding: 12px 24px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-size: 1rem;
        }
        .return-button a:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

    <div class="container">
        <h2>Your Inbox</h2>

        <?php if (empty($received_messages)): ?>
            <p style="text-align:center; color:#777;">No messages found.</p>
        <?php else: ?>
            <?php foreach ($received_messages as $message): ?>
                <div class="message <?php echo $message['is_read'] == 0 ? 'new' : ''; ?>">
                    <div class="sender">From: <?php echo htmlspecialchars($message['sender_username']); ?></div>
                    <?php if ($message['is_read'] == 0): ?>
                        <div class="new-message">New</div>
                    <?php endif; ?>
                    <?php if ($message['ai_flag'] === 'Phishing Email'): ?>
                        <div class="phishing-alert">⚠️ AI Warning: This message may be a phishing attempt.</div>
                    <?php endif; ?>
                    <div class="message-text"><?php echo htmlspecialchars($message['message']); ?></div>
                    <div class="time">Sent at: <?php echo htmlspecialchars($message['sent_at']); ?></div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <div class="return-button">
        <a href="home.php">🏠 Return to Home</a>
    </div>

</body>
</html>

