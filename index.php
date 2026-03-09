<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login | Billing System</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="/billing_system/assets/css/animations.css">
    <link rel="stylesheet" href="assets/css/login.css">
</head>
<body class="center">

    <form class="login-card" method="POST" action="auth/login_process.php">

        <h2>Login</h2>

        <input type="text" name="username" placeholder="Username" required>

        <input type="password" name="password" placeholder="Password" required>

        <button type="submit">Login</button>
    </form>

</body>

</html>
