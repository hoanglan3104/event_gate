<?php
session_start();
require_once "includes/db_connect.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);

    $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ?");
    $stmt->execute([$username]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['admin_id'] = $admin['id'];
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Sai tài khoản hoặc mật khẩu.";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng nhập Admin</title>
    <link rel="stylesheet" href="css/login.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <div class="login-wrapper">
        <div class="login-image">
            <img src="../assets/images/logo.png" alt="EventGate">
        </div>

        <div class="login-container">
            <h2>
                <i class="bi bi-person-vcard-fill"></i>
                <span>Admin Login</span>
            </h2>
            <?php if (!empty($error)): ?>
                <p class="error"><?= $error ?></p>
            <?php endif; ?>

            <form method="post">
                <div class="form-group">
                    <label><i class="bi bi-person-square"></i> Tên đăng nhập</label>
                    <input type="text" name="username" placeholder="Tên đăng nhập" required>
                </div>
                <div class="form-group">
                    <label><i class="bi bi-lock-fill"></i> Mật khẩu</label>
                    <input type="password" name="password" placeholder="Mật khẩu" required>
                </div>
                <button type="submit">
                    <i class="bi bi-door-open-fill"></i>
                    <span>Đăng nhập</span>
                </button>
            </form>
        </div>
    </div>
</body>
</html>
