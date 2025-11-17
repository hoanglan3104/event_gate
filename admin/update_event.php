<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
require_once "../includes/db_connect.php";

function generateEventId($pdo) {
    $stmt = $pdo->query("SELECT MAX(CAST(SUBSTRING(event_id, 2) AS UNSIGNED)) FROM events");
    $maxId = $stmt->fetchColumn();
    $nextId = (int)$maxId + 1;
    return 'E0' . str_pad($nextId, 2, '0', STR_PAD_LEFT);
}

$event_id    = $_POST['event_id']    ?? '';
$event_name  = $_POST['event_name']  ?? '';
$start_time  = $_POST['start_time']  ?? '';
$price       = $_POST['price']       ?? 0;
$duration    = $_POST['duration']    ?? 1;
$location    = $_POST['location']    ?? '';
$total_seats = $_POST['total_seats'] ?? 50;
$event_type  = $_POST['event_type']  ?? 'music';
$event_kind  = $_POST['event_kind']  ?? 'event';  // Sự kiện / Phim
$eStatus     = $_POST['eStatus']     ?? 'Chưa diễn ra';

// ảnh
$event_img = $_POST['old_event_img'] ?? '';
if (!empty($_FILES['event_img']['name'])) {
    $uploadDir  = '../assets/images/';
    $uploadFile = $uploadDir . basename($_FILES['event_img']['name']);
    if (move_uploaded_file($_FILES['event_img']['tmp_name'], $uploadFile)) {
        $event_img = basename($_FILES['event_img']['name']);
    }
} elseif (!empty($_POST['event_img_link'])) {
    $event_img = $_POST['event_img_link'];
}

// check tồn tại
$stmtCheck = $pdo->prepare("SELECT COUNT(*) FROM events WHERE event_id = ?");
$stmtCheck->execute([$event_id]);
$isUpdate = $stmtCheck->fetchColumn() > 0;

if ($isUpdate) {
    // update
    $stmt = $pdo->prepare("
        UPDATE events 
        SET event_name = ?, 
            start_time  = ?, 
            price       = ?, 
            duration    = ?, 
            location    = ?, 
            total_seats = ?, 
            event_type  = ?, 
            event_kind  = ?, 
            eStatus     = ?, 
            event_img   = ?
        WHERE event_id = ?
    ");

    $success = $stmt->execute([
        $event_name,
        $start_time,
        $price,
        $duration,
        $location,
        $total_seats,
        $event_type,
        $event_kind,
        $eStatus,
        $event_img,
        $event_id
    ]);

    $_SESSION['success'] = $success ? "Cập nhật sự kiện thành công!" : "Có lỗi khi cập nhật.";
} else {
    // insert mới
    $event_id = generateEventId($pdo);

    $stmt = $pdo->prepare("
        INSERT INTO events 
            (event_id, event_name, start_time, price, duration, location, total_seats, event_type, event_kind, eStatus, event_img)
        VALUES 
            (?,        ?,          ?,          ?,     ?,        ?,        ?,           ?,          ?,         ?,        ?)
    ");

    $success = $stmt->execute([
        $event_id,
        $event_name,
        $start_time,
        $price,
        $duration,
        $location,
        $total_seats,
        $event_type,
        $event_kind,
        $eStatus,
        $event_img
    ]);

    // tạo ghế
    $vipCount     = floor($total_seats * 0.2);
    $regularCount = $total_seats - $vipCount;

    $row       = 'A';
    $col       = 1;

    $stmtMaxSeat = $pdo->query("SELECT MAX(CAST(SUBSTRING(seat_id, 2) AS UNSIGNED)) AS max_id FROM seats");
    $max         = $stmtMaxSeat->fetchColumn();
    $seatIndex   = ($max ?? 0) + 1;

    $insertSeat = $pdo->prepare("
        INSERT INTO seats (seat_id, event_id, seat_type, seat_number, sStatus, seat_price)
        VALUES (?, ?, ?, ?, ?, ?)
    ");

    for ($i = 0; $i < $vipCount; $i++) {
        $seat_number = $row . $col++;
        if ($col > 10) { $col = 1; $row++; }

        $insertSeat->execute([
            'S' . str_pad($seatIndex++, 3, '0', STR_PAD_LEFT),
            $event_id,
            'vip',
            $seat_number,
            'Còn trống',
            round($price * 1.5)
        ]);
    }

    for ($i = 0; $i < $regularCount; $i++) {
        $seat_number = $row . $col++;
        if ($col > 10) { $col = 1; $row++; }

        $insertSeat->execute([
            'S' . str_pad($seatIndex++, 3, '0', STR_PAD_LEFT),
            $event_id,
            'normal',
            $seat_number,
            'Còn trống',
            $price
        ]);
    }

    $_SESSION['success'] = $success ? "Tạo sự kiện mới thành công!" : "Có lỗi khi tạo sự kiện.";
}

header("Location: events.php");
exit();
