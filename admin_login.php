<?ph<?php
session_start();
require 'db.php';

if (isset($_POST['login'])) {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM admin_users WHERE username=?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_user'] = $user['username'];
        header('Location: admin_dashboard.php');
        exit;
    } else {
        $error = "Invalid username or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Login</title>
<style>
body{font-family:Arial;margin:30px;background:#f5f5f5}
.container{max-width:400px;margin:auto;background:#fff;padding:20px;border-radius:8px}
input{width:100%;padding:8px;margin:5px 0;border:1px solid #ccc;border-radius:4px}
button{width:100%;padding:10px;background:#007bff;color:#fff;border:none;border-radius:4px;cursor:pointer}
button:hover{background:#0056b3}
.error{color:red;margin-bottom:10px}
</style>
</head>
<body>
<div class="container">
<h2>Admin Login</h2>
<?php if (isset($error))
    echo "<div class='error'>{$error}</div>"; ?>
<form method="POST">
<input type="text" name="username" placeholder="Username" required>
<input type="password" name="password" placeholder="Password" required>
<button type="submit" name="login">Login</button>
</form>
</div>
</body>
</html>

