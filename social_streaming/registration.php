<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
</head>
<body>
    <h2>Register</h2>
    <form action="register_process.php" method="post">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" required><br>

        <label for="email">Email</label>
        <input type="email" id="email" name="email" required><br>

        <label for="password">Password</label>
        <input type="password" id="password" name="password" required><br>

        <input type="submit" value="Register">
    </form>
    
    <!-- Back to Home button -->
    <br>
    <a href="index.php"><button>Back to Home</button></a>
</body>
</html>
