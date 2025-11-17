<?php
require_once "../includes/db_connect.php";
date_default_timezone_set("Asia/Ho_Chi_Minh");
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

$now = new DateTime('now', new DateTimeZone('Asia/Ho_Chi_Minh'));
$startOfWeek = (clone $now)->modify('monday this week')->setTime(0, 0, 0);
$endOfWeek = (clone $startOfWeek)->modify('+6 days')->setTime(23, 59, 59);

$stmt = $pdo->query("SELECT COUNT(*) AS total_users FROM users");
$totalUsers = $stmt->fetch()['total_users'];

$stmt = $pdo->query("SELECT COUNT(*) AS total_events FROM events");
$totalEvents = $stmt->fetch()['total_events'];

$stmt = $pdo->query("SELECT COUNT(*) AS total_orders FROM orders");
$totalorders = $stmt->fetch()['total_orders'];

$stmt = $pdo->query("SELECT SUM(amount) AS total_paid FROM payments WHERE pStatus = 'paid'");
$totalPaids = $stmt->fetch()['total_paid'] ?? 0;

$today = date('Y-m-d');
$revenueDate = $_GET['revenue_date'] ?? $today;

$stmt = $pdo->prepare("SELECT SUM(amount) AS daily_total FROM payments WHERE pStatus = 'paid' AND DATE(payment_time) = ?");
$stmt->execute([$revenueDate]);
$dailyTotal = $stmt->fetch()['daily_total'] ?? 0;

$filteredTotal = 0;
if (!empty($_GET['from_date']) && !empty($_GET['to_date'])) {
    $from = $_GET['from_date'];
    $to = $_GET['to_date'];
    $stmt = $pdo->prepare("SELECT SUM(amount) AS filtered_total FROM payments WHERE pStatus = 'paid' AND DATE(payment_time) BETWEEN ? AND ?");
    $stmt->execute([$from, $to]);
    $filteredTotal = $stmt->fetch()['filtered_total'] ?? 0;
}

$currentYear = date('Y');
$monthlyRevenue = array_fill(1, 12, 0);
$stmt = $pdo->prepare("
    SELECT MONTH(payment_time) AS month, SUM(amount) AS total 
    FROM payments 
    WHERE pStatus = 'paid' AND YEAR(payment_time) = ? 
    GROUP BY MONTH(payment_time)
");
$stmt->execute([$currentYear]);
while ($row = $stmt->fetch()) {
    $monthlyRevenue[(int)$row['month']] = (int)$row['total'];
}

$quarterRevenue = [
    'Quý 1' => $monthlyRevenue[1] + $monthlyRevenue[2] + $monthlyRevenue[3],
    'Quý 2' => $monthlyRevenue[4] + $monthlyRevenue[5] + $monthlyRevenue[6],
    'Quý 3' => $monthlyRevenue[7] + $monthlyRevenue[8] + $monthlyRevenue[9],
    'Quý 4' => $monthlyRevenue[10] + $monthlyRevenue[11] + $monthlyRevenue[12],
];

$weeklyRevenueLabels = [];
$weeklyRevenueData = [];
for ($i = 0; $i < 7; $i++) {
    $day = (clone $startOfWeek)->modify("+$i days");
    $label = $day->format('D d/m');
    $weeklyRevenueLabels[] = $label;
    $stmt = $pdo->prepare("
        SELECT SUM(amount) AS total 
        FROM payments 
        WHERE pStatus = 'paid' 
        AND payment_time BETWEEN ? AND ?
    ");
    $startTime = $day->format('Y-m-d 00:00:00');
    $endTime = $day->format('Y-m-d 23:59:59');
    $stmt->execute([$startTime, $endTime]);
    $total = $stmt->fetchColumn() ?? 0;
    $weeklyRevenueData[] = (int)$total;
}

$yearRevenue = [];
$startYear = $currentYear - 4;
$stmt = $pdo->prepare("
    SELECT YEAR(payment_time) AS year, SUM(amount) AS total 
    FROM payments 
    WHERE pStatus = 'paid' AND YEAR(payment_time) BETWEEN ? AND ? 
    GROUP BY YEAR(payment_time)
    ORDER BY year ASC
");
$stmt->execute([$startYear, $currentYear]);
while ($row = $stmt->fetch()) {
    $yearRevenue[(int)$row['year']] = (int)$row['total'];
}
for ($y = $startYear; $y <= $currentYear; $y++) {
    if (!isset($yearRevenue[$y])) {
        $yearRevenue[$y] = 0;
    }
}
ksort($yearRevenue);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin</title>
    <link rel="stylesheet" href="css/dashboard.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>

<?php $current_page = 'dashboard'; include "includes/menu.php"; ?>

<div class="main-content p-4 dashboard-main">
    <h1 class="dashboard-title d-flex align-items-center gap-2">
        <i class="bi bi-speedometer2"></i>
        <span>Bảng điều khiển</span>
    </h1>

    <div class="row g-3">
        <div class="col-md-3">
            <div class="card dashboard-stat-card">
                <div class="card-body d-flex align-items-center">
                    <div class="me-3 card-icon">
                        <i class="bi bi-people"></i>
                    </div>
                    <div>
                        <h5 class="card-title mb-1"><a href="users.php">Người dùng</a></h5>
                        <p class="card-text text-muted mb-0"><?= $totalUsers ?> tài khoản</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card dashboard-stat-card">
                <div class="card-body d-flex align-items-center">
                    <div class="me-3 card-icon">
                        <i class="bi bi-calendar-event"></i>
                    </div>
                    <div>
                        <h5 class="card-title mb-1"><a href="events.php">Sự kiện &amp; Phim</a></h5>
                        <p class="card-text text-muted mb-0"><?= $totalEvents ?> sự kiện &amp; Phim</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card dashboard-stat-card">
                <div class="card-body d-flex align-items-center">
                    <div class="me-3 card-icon">
                        <i class="bi bi-ticket-perforated"></i>
                    </div>
                    <div>
                        <h5 class="card-title mb-1"><a href="orders.php">Vé đã bán</a></h5>
                        <p class="card-text text-muted mb-0"><?= $totalorders ?> vé</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card dashboard-stat-card">
                <div class="card-body d-flex align-items-center">
                    <div class="me-3 card-icon">
                        <i class="bi bi-bar-chart-line"></i>
                    </div>
                    <div>
                        <h5 class="card-title mb-1"><a href="revenue.php">Tổng doanh thu</a></h5>
                        <p class="card-text text-muted mb-0"><?= number_format($totalPaids, 0, ',', '.') ?> VND</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card dashboard-section-card mt-4">
        <div class="card-body">
            <h5 class="card-title">Doanh thu theo ngày</h5>
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label for="revenue_date" class="form-label">Chọn ngày</label>
                    <input type="date" class="form-control" id="revenue_date" name="revenue_date" value="<?= $_GET['revenue_date'] ?? date('Y-m-d') ?>">
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary w-100 rounded-pill shadow-sm">Xem</button>
                </div>
            </form>
            <?php if ($dailyTotal > 0): ?>
                <div class="alert alert-success mt-4">
                    Tổng <strong><?= date('d/m/Y', strtotime($revenueDate)) ?></strong>:
                    <strong><?= number_format($dailyTotal, 0, ',', '.') ?> VND</strong>
                </div>
            <?php else: ?>
                <div class="alert alert-success mt-4">
                    Không có doanh thu nào vào ngày <strong><?= date('d/m/Y', strtotime($revenueDate)) ?></strong>.
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="card dashboard-section-card mt-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="card-title mb-0">Thống kê doanh thu</h5>
                <select id="chartFilter" class="form-select w-auto rounded-pill shadow-sm border-0">
                    <option value="week">Theo tuần</option>
                    <option value="month">Theo tháng</option>
                    <option value="quarter">Theo quý</option>
                    <option value="year">Theo năm</option>
                </select>
            </div>
            <canvas id="revenueChart" height="100"></canvas>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    let chart;
    let chartData = {
        month: {
            labels: ["1", "2", "3", "4", "5", "6", "7", "8", "9", "10", "11", "12"],
            data: <?= json_encode(array_values($monthlyRevenue)) ?>
        },
        quarter: {
            labels: ["Q1", "Q2", "Q3", "Q4"],
            data: <?= json_encode(array_values($quarterRevenue)) ?>
        },
        year: {
            labels: [<?php
                for ($i = 4; $i >= 0; $i--) echo '"' . ($currentYear - $i) . '",';
            ?>],
            data: [
                <?php
                for ($i = 4; $i >= 0; $i--) {
                    $year = $currentYear - $i;
                    $stmt = $pdo->prepare("SELECT SUM(amount) AS total FROM payments WHERE pStatus = 'paid' AND YEAR(payment_time) = ?");
                    $stmt->execute([$year]);
                    echo (int)($stmt->fetchColumn() ?? 0) . ',';
                }
                ?>
            ]
        },
        week: {
            labels: <?= json_encode($weeklyRevenueLabels) ?>,
            data: <?= json_encode($weeklyRevenueData) ?>
        }
    };

    function renderChart(type) {
        const ctx = document.getElementById('revenueChart').getContext('2d');
        if (chart) chart.destroy();
        const total = chartData[type].data.reduce((a, b) => a + b, 0);
        chart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: chartData[type].labels,
                datasets: [{
                    label: 'Doanh thu (VND)',
                    data: chartData[type].data,
                    backgroundColor: '#0ea5e9',
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        align: 'end'
                    },
                    subtitle: {
                        display: true,
                        text: 'Tổng: ' + total.toLocaleString('vi-VN') + ' VND',
                        align: 'end',
                        position: 'top',
                        font: {
                            size: 14,
                            weight: 'normal'
                        },
                        padding: {
                            bottom: 10
                        }
                    }
                },
                scales: {
                    y: {
                        ticks: {
                            callback: function(value) {
                                return value.toLocaleString('vi-VN') + ' VND';
                            }
                        }
                    }
                }
            }
        });
    }

    document.getElementById('chartFilter').addEventListener('change', function () {
        renderChart(this.value);
    });
    window.onload = function() {
        document.getElementById('chartFilter').value = 'week';
        renderChart('week');
    };
</script>

</body>
</html>
