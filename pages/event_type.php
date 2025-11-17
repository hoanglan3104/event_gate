<?php
session_start();
require_once "../config.php";
require_once "../includes/db_connect.php";

$eventTypeParam = isset($_GET['event_type']) ? urldecode($_GET['event_type']) : 'latest';

$eventTypeMap = [
    'music'      => 'Âm nhạc',
    'visit'      => 'Tham quan',
    'tournament' => 'Giải đấu',
    'art'        => 'Văn hóa nghệ thuật',
    'movie'      => 'Phim',
    'all'        => 'Tất cả'
];

// Gán tên hiển thị để dùng ở giao diện
$eventTypeDisplay = $eventTypeMap[$eventTypeParam] ?? 'Mới nhất';

$today = date('Y-m-d');

if ($eventTypeParam === 'all') {
    // tất cả sự kiện chưa diễn ra
    $query = "SELECT * FROM events WHERE eStatus = 'Chưa diễn ra' ORDER BY start_time ASC";
    $stmt  = $pdo->prepare($query);
    $stmt->execute();

} elseif ($eventTypeParam === 'movie') {
    // riêng tab Phim: lọc theo event_kind = 'movie'
    $query = "
        SELECT * FROM events 
        WHERE event_kind = 'movie' 
          AND eStatus = 'Chưa diễn ra'
          AND DATE(start_time) >= :today
        ORDER BY start_time ASC
    ";
    $stmt = $pdo->prepare($query);
    $stmt->execute([
        'today' => $today
    ]);

} else {
    // các loại khác: âm nhạc, tham quan, giải đấu, nghệ thuật...
    $query = "
        SELECT * FROM events 
        WHERE event_type = :event_type 
          AND eStatus = 'Chưa diễn ra'
          AND DATE(start_time) >= :today
        ORDER BY start_time ASC
    ";
    $stmt = $pdo->prepare($query);
    $stmt->execute([
        'event_type' => $eventTypeParam,
        'today'      => $today
    ]);
}

$result    = $stmt->fetchAll();
$mainEvent = count($result) > 0 ? $result[0] : null;
?>


<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($eventTypeDisplay); ?> - Sự kiện</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha512-..." crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="../assets/css/event_type.css">
</head>
<body>
    <?php include "../includes/header.php"; ?>
    <?php include "../includes/login_modal.php"; ?>
    <?php include "../includes/register_modal.php"; ?>

    <div class="container_type">
        <div class="breadcrumb">
            <a href="../index.php" class="btn-back">Trang chủ</a>
            <span><?php echo htmlspecialchars($eventTypeDisplay); ?></span>
            <span><?php echo count($result); ?> Sự kiện</span>
        </div>

        <?php if ($mainEvent): ?>
            <?php
                $location = $mainEvent['location'];
                $parts = explode(',', $location);
                $parts = array_map('trim', $parts);

                if (count($parts) >= 2) {
                    $location_display = implode(', ', array_slice($parts, -2));
                } else {
                    $location_display = $location;
                }

                $startTime = strtotime($mainEvent['start_time']);
                $month = date("m", $startTime);
                $day = date("d", $startTime);
                $year = date("Y", $startTime);
            ?>
            <a href="payment.php?event_id=<?= $mainEvent['event_id'] ?>" class="main-single" style="text-decoration: none; color: white;">
                <div class="image-box">
                    <img src="<?= htmlspecialchars($mainEvent['event_img']) ?>" alt="<?= htmlspecialchars($mainEvent['event_img']) ?>">
                </div>
                <div class="content-box">
                    <h4><?= htmlspecialchars($mainEvent['event_name']) ?></h4>
                    <div class="info-row">
                        <span><i class="fa-solid fa-calendar-days"></i> <?= "$day/$month/$year" ?></span><br>
                        <span><i class="fa-solid fa-location-dot"></i> <?= htmlspecialchars($location_display) ?></span>
                    </div>
                    <p class="desc"><?= nl2br(htmlspecialchars($mainEvent['description'] ?? '')) ?></p>
                    <span class="price">VNĐ <?= number_format($mainEvent['price']) ?> +</span>
                </div>
            </a>
        <?php endif; ?>

        <div class="single-list">
            <div class="event-grid">
                <?php if (count($result) > 1):?>
                    <?php foreach ($result as $index => $row): ?>
                        <?php if ($index === 0) continue;?>
                        <?php
                            $location = $row['location'];
                            $parts = explode(',', $location);
                            $parts = array_map('trim', $parts);

                            if (count($parts) >= 2) {
                                $location_display = implode(', ', array_slice($parts, -2));
                            } else {
                                $location_display = $location;
                            }

                            $startTime = strtotime($row['start_time']);
                            $month = date("m", $startTime);
                            $day = date("d", $startTime);
                            $year = date("Y", $startTime);
                        ?>
                        <a href="payment.php?event_id=<?php echo urlencode($row['event_id']); ?>" class="single-card">
                            <img src="<?php echo htmlspecialchars($row['event_img']); ?>" alt="<?php echo htmlspecialchars($row['event_name']); ?>">
                            <div class="card-info">
                                <div class="date-tag">Tháng <?php echo $month; ?><br><strong><?php echo $day; ?></strong></div>
                                <p class="title"><?php echo htmlspecialchars($row['event_name']); ?></p>
                                <span><i class="fa-solid fa-location-dot"></i> <?php echo htmlspecialchars($location_display); ?></span>
                                <span class="price">VNĐ <?php echo number_format($row['price']); ?> +</span>
                            </div>
                        </a>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Không tìm thấy sự kiện nào thuộc loại "<?php echo htmlspecialchars($eventTypeDisplay); ?>".</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php include "../includes/footer.php"; ?>
    <script src="../assets/js/script.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const loginModal = new bootstrap.Modal(document.getElementById("loginModal"), { backdrop: "static" });

            document.querySelectorAll(".openLogin").forEach(btn => {
                btn.addEventListener("click", function (e) {
                    e.preventDefault();
                    loginModal.show();
                });
            });

            const myTicketBtn = document.getElementById("myTicketsBtn");
            if (myTicketBtn) {
                myTicketBtn.addEventListener("click", function (e) {
                    if (!isLoggedIn) {
                        e.preventDefault();
                        loginModal.show(); 
                    } else {
                        window.location.href = "../pages/my_tickets.php"; 
                    }
                });
            }
        });
    </script>
</body>
</html>
