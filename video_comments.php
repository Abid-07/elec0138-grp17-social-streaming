<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include "db.php";

// === Session Check ===
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// === Video Info Retrieval ===
if (!isset($_GET['id'])) {
    die("Video ID not provided.");
}
$video_id = $_GET['id'];

$query = "SELECT title, description, filename, price_in_credits, user_id AS video_uploader_id FROM videos WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $video_id);
$stmt->execute();
$stmt->bind_result($title, $description, $filename, $price_in_credits, $video_uploader_id);
$stmt->fetch();
$stmt->close();

if (!$title) {
    die("Video not found.");
}

// === Check Purchase Status ===
$purchase_query = "SELECT 1 FROM purchases WHERE user_id = ? AND video_id = ?";
$purchase_stmt = $conn->prepare($purchase_query);
$purchase_stmt->bind_param("ii", $user_id, $video_id);
$purchase_stmt->execute();
$purchase_stmt->store_result();

// === Mock Quantum-Safe Signature ===
function mock_oqs_sign($message, $private_key) {
    return hash_hmac('sha256', $message, $private_key);
}

function mock_oqs_verify($message, $signature, $public_key) {
    $expected = hash_hmac('sha256', $message, $public_key);
    return hash_equals($expected, $signature);
}

function triggerQuantumAlert() {
    error_log("Quantum-safe signature verification failed!");
    echo "Security alert: Invalid request signature.";
}

// === Mock Homomorphic Encryption ===
function homomorphic_encrypt($value, $key = "homomorphic_key") {
    return base64_encode($value ^ crc32($key));
}

function homomorphic_decrypt($encrypted, $key = "homomorphic_key") {
    return base64_decode($encrypted) ^ crc32($key);
}

// === Nonce Helpers ===
function generateNonce() {
    return bin2hex(random_bytes(16));
}

function isNonceUsed($nonce) {
    return isset($_SESSION['used_nonces']) && in_array($nonce, $_SESSION['used_nonces']);
}

function markNonceUsed($nonce) {
    $_SESSION['used_nonces'][] = $nonce;
}

// === Handle Donations ===
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['donate_credits'], $_POST['nonce'])) {
    $donation_amount_plain = intval($_POST['donate_credits']);
    $nonce = $_POST['nonce'];

    if (isNonceUsed($nonce)) {
        echo "Replay detected: This donation request has already been processed.";
        exit;
    }

    if ($donation_amount_plain > 0) {
        $encrypted_amount = homomorphic_encrypt($donation_amount_plain);
        $message = "{$user_id}|{$video_id}|{$encrypted_amount}|{$nonce}";
        $quantum_private_key = "secret_simulated_private_key";
        $quantum_public_key = "secret_simulated_private_key";

        $signature = mock_oqs_sign($message, $quantum_private_key);

        if (!mock_oqs_verify($message, $signature, $quantum_public_key)) {
            triggerQuantumAlert();
            exit;
        }

        markNonceUsed($nonce);

        $user_query = "SELECT credits FROM users WHERE id = ?";
        $user_stmt = $conn->prepare($user_query);
        $user_stmt->bind_param("i", $user_id);
        $user_stmt->execute();
        $user_stmt->bind_result($user_credits);
        $user_stmt->fetch();
        $user_stmt->close();

        if ($user_credits >= $donation_amount_plain) {
            $conn->begin_transaction();
            try {
                $donor_update = $conn->prepare("UPDATE users SET credits = credits - ? WHERE id = ?");
                $donor_update->bind_param("ii", $donation_amount_plain, $user_id);
                $donor_update->execute();

                $recipient_update = $conn->prepare("UPDATE users SET credits = credits + ? WHERE id = ?");
                $recipient_update->bind_param("ii", $donation_amount_plain, $video_uploader_id);
                $recipient_update->execute();

                $conn->commit();
                echo "Donation successful! You donated {$donation_amount_plain} credits (encrypted: {$encrypted_amount}).";
            } catch (Exception $e) {
                $conn->rollback();
                echo "An error occurred while processing the donation.";
            }
        } else {
            echo "You don't have enough credits to donate.";
        }
    }
}

// === Handle Comment Submission ===
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment'])) {
    $comment = trim($_POST['comment']);
    if (!empty($comment)) {
        $insert_comment = $conn->prepare("INSERT INTO comments (video_id, user_id, comment_text, created_at) VALUES (?, ?, ?, NOW())");
        $insert_comment->bind_param("iis", $video_id, $user_id, $comment);
        $insert_comment->execute();
        $insert_comment->close();

        header("Location: video_comments.php?id=" . $video_id);
        exit;
    }
}

// === Fetch Comments ===
$comment_query = "SELECT users.username, comments.comment_text, comments.created_at FROM comments JOIN users ON comments.user_id = users.id WHERE comments.video_id = ? ORDER BY comments.created_at DESC";
$comment_stmt = $conn->prepare($comment_query);
$comment_stmt->bind_param("i", $video_id);
$comment_stmt->execute();
$comment_stmt->bind_result($username, $comment_text, $created_at);

// === Generate New Nonce ===
$nonce = generateNonce();
$_SESSION['current_nonce'] = $nonce;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Video Comments</title>
    <style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f7f8fa;
        color: #333;
        margin: 40px auto;
        max-width: 800px;
        padding: 20px;
        line-height: 1.6;
        border-radius: 8px;
        background: #fff;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.05);
    }

    h2 {
        font-size: 1.8rem;
        margin-bottom: 10px;
    }

    video {
        margin-top: 20px;
        border-radius: 8px;
        width: 100%;
        max-width: 100%;
    }

    form {
        margin-top: 30px;
        padding: 15px;
        background-color: #f1f3f5;
        border-radius: 8px;
    }

    textarea, input[type="number"] {
        width: 100%;
        padding: 10px;
        margin-top: 8px;
        border: 1px solid #ccc;
        border-radius: 6px;
        font-size: 1rem;
        resize: vertical;
    }

    button {
        margin-top: 12px;
        padding: 10px 18px;
        font-size: 1rem;
        border: none;
        border-radius: 6px;
        background-color: #007bff;
        color: #fff;
        cursor: pointer;
        transition: background-color 0.2s ease;
    }

    button:hover {
        background-color: #0056b3;
    }

    .comment-block {
        padding: 12px;
        background-color: #f9f9f9;
        border-radius: 6px;
        margin-bottom: 15px;
        border-left: 3px solid #007bff;
    }

    .comment-block p {
        margin: 4px 0;
    }

    a {
        color: #007bff;
        text-decoration: none;
    }

    a:hover {
        text-decoration: underline;
    }

    label {
        font-weight: bold;
    }

    hr {
        border: none;
        height: 1px;
        background-color: #ddd;
        margin: 20px 0;
    }
    .home-btn {
    display: inline-block;
    margin-top: 10px;
    padding: 10px 16px;
    background-color: #28a745;
    color: white;
    border-radius: 6px;
    font-weight: bold;
    text-decoration: none;
    transition: background-color 0.2s ease;
}

.home-btn:hover {
    background-color: #218838;
}

</style>

</head>
<body>
    <h2><?php echo htmlspecialchars($title); ?></h2>
    <p><?php echo htmlspecialchars($description ? $description : "No description provided."); ?></p>

    <?php
    if ($purchase_stmt->num_rows > 0 || $price_in_credits == 0) {
        echo '<video width="600" controls>';
        echo '<source src="uploads/videos/' . htmlspecialchars($filename) . '" type="video/mp4">';
        echo 'Your browser does not support the video tag.';
        echo '</video>';
    } else {
        echo "You need to purchase this video to watch it.";
    }
    ?>

    <h3>Comments:</h3>
    <?php
    $comments_found = false;
    while ($comment_stmt->fetch()):
        $comments_found = true;
    ?>
        <div>
            <p><strong><?php echo htmlspecialchars($username); ?></strong> (<?php echo $created_at; ?>):</p>
            <p><?php echo htmlspecialchars($comment_text); ?></p>
            <hr>
        </div>
    <?php endwhile; ?>
    <?php if (!$comments_found) echo "<p>No comments yet. Be the first to comment!</p>"; ?>

    <!-- Comment form -->
    <form method="POST" action="video_comments.php?id=<?php echo $video_id; ?>">
        <textarea name="comment" placeholder="Write your comment..." required></textarea><br>
        <button type="submit">Submit Comment</button>
    </form>

    <!-- Donation form -->
    <form method="POST" action="video_comments.php?id=<?php echo $video_id; ?>">
        <label for="donate_credits">Donate Credits:</label>
        <input type="number" name="donate_credits" min="1" placeholder="Amount to donate" required><br>
        <input type="hidden" name="nonce" value="<?php echo $nonce; ?>">
        <button type="submit">Donate</button>
    </form>

    <br>
    <a href="view_videos.php">Back to Your Videos</a>
    <br><br>
	<a href="home.php" class="home-btn">🏠 Back to Home</a>

</body>
</html>

<?php
$comment_stmt->close();
$purchase_stmt->close();
?>

