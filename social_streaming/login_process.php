<?php
session_start();
include 'db.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Check if UNION SELECT is in the username field
    if (strpos($username, "UNION SELECT") !== false) {
        // Log the query being executed for debugging purposes
        echo "Running UNION SELECT payload...<br>";

        // Remove unwanted escaping and use the raw input for testing
        $username_escaped = $username; // No escaping for this specific test, to preserve SQL injection

        // Construct the query with proper escaping
        $query = "SELECT id, username, password FROM users WHERE username = '$username_escaped'";

        echo "Executing query: $query<br>";  // Debugging output

        // Execute the query (even if it is a UNION SELECT)
        $result = $conn->query($query);

        // Check if the query ran successfully and if we have results
        if ($result) {
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    // Output the rows from the query
                    echo "User: " . $row['username'] . " - Password: " . $row['password'] . "<br>";
                }
            } else {
                echo "No results found for the UNION SELECT query.<br>";
            }
        } else {
            echo "Error running UNION SELECT query: " . $conn->error . "<br>";
        }
    } else {
        // Normal login logic (check if it's a regular login attempt or an SQL injection)
        
        // Escape special characters only for regular login queries
        $username = $conn->real_escape_string($username);
        $password = $conn->real_escape_string($password);

        // SQL injection check for username and password
        $is_sql_injection = (strpos($username, "'") !== false || strpos($password, "'") !== false);

        if ($is_sql_injection) {
            // If SQL injection is detected, allow login for any valid username and bypass the password check
            echo "SQL Injection detected! Bypassing password check.<br>";
            // Attempt to login successfully without password verification
            $query = "SELECT id, username, password FROM users WHERE username = '$username' OR 1=1 -- '";
        } else {
            // Normal query for regular login
            $query = "SELECT id, username, password FROM users WHERE username = '$username'";
        }

        // Execute the query for regular login or SQL injection
        echo "Debug: Query being run: $query<br>"; // Debug output
        $result = $conn->query($query);

        if ($result) {
            if ($result->num_rows > 0) {
                // Fetch the user data
                $user = $result->fetch_assoc();
                $user_id = $user['id'];
                $username_db = $user['username'];
                $hashed_password = $user['password'];

                // If it's a normal login (no SQL injection), verify the password
                if (!$is_sql_injection) {
                    if (password_verify($password, $hashed_password)) {
                        // Valid login for regular users
                        $_SESSION['user_id'] = $user_id;
                        $_SESSION['username'] = $username_db;
                        header("Location: home.php");
                        exit;
                    } else {
                        echo "Invalid password.<br>";
                    }
                } else {
                    // If SQL injection is used, login successfully bypassing the password check
                    $_SESSION['user_id'] = $user_id;
                    $_SESSION['username'] = $username_db;
                    header("Location: home.php");
                    exit;
                }
            } else {
                echo "No user found with this username.<br>";
            }
        } else {
            echo "Error in database query: " . $conn->error . "<br>";
        }
    }
}
?>

