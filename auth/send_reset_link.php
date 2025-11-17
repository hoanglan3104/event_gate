<?php
require_once "../includes/db_connect.php";

$email = $_POST['email'] ?? '';
if (!$email) {
    echo "<script>alert('Thiếu email!'); history.back();</script>";
    exit;
}

// kiểm tra email tồn tại
$stmt = $pdo->prepare("SELECT user_id FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo "<script>alert('Email không tồn tại!'); history.back();</script>";
    exit;
}

// tạo token + hạn dùng
$token  = bin2hex(random_bytes(16));
$expire = date("Y-m-d H:i:s", time() + 3600); // 1 tiếng

$stmt = $pdo->prepare("UPDATE users SET reset_token = ?, reset_expire = ? WHERE user_id = ?");
$stmt->execute([$token, $user['user_id']]);

// link đặt lại mật khẩu
$link = "http://localhost/event_gate/auth/reset_password.php?token=" . urlencode($token);

$subject = "Khôi phục mật khẩu EventGate";
$message = "
    <h3>Yêu cầu khôi phục mật khẩu</h3>
    <p>Nhấn vào liên kết sau để đặt lại mật khẩu (hiệu lực trong 1 giờ):</p>
    <p><a href='$link'>$link</a></p>
";
$headers  = "MIME-Version: 1.0\r\n";
$headers .= "Content-type: text/html; charset=UTF-8\r\n";
$headers .= "From: EventGate <halun05062004@gmail.com>\r\n";

if (mail($email, $subject, $message, $headers)) {
    echo "<script>alert('Liên kết khôi phục đã được gửi tới email của bạn!'); window.location.href='../index.php';</script>";
} else {
    echo "<script>alert('Gửi email thất bại, vui lòng thử lại sau!'); history.back();</script>";
}
?>
