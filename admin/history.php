<?php
session_start();
require_once "../includes/db_connect.php";

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

$status      = $_GET['status'] ?? 'paid';
$search      = $_GET['search'] ?? '';
$filter_date = $_GET['filter_date'] ?? '';

$where  = "1=1";
$params = [];

if ($search !== '') {
    $where .= " AND fullname LIKE ?";
    $params[] = "%$search%";
}
if ($filter_date !== '') {
    $where .= " AND DATE(payment_at) = ?";
    $params[] = $filter_date;
}
if (in_array($status, ['paid', 'pending', 'cancel'])) {
    $where .= " AND pStatus = ?";
    $params[] = $status;
}

$sql = "SELECT * FROM payments WHERE $where ORDER BY payment_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$payments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Lịch sử thanh toán</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="css/dashboard.css">
</head>
<body>
<?php $current_page = 'payments'; include "includes/menu.php"; ?>

<div class="main-content p-4 dashboard-main">
    <div class="card dashboard-section-card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <h2 class="mb-0 d-flex align-items-center gap-2">
                    <i class="bi bi-cash-coin"></i>
                    <span>Lịch sử thanh toán</span>
                </h2>
                <ul class="nav nav-pills my-2">
                    <li class="nav-item">
                        <a class="nav-link <?= $status == 'paid' ? 'active' : '' ?>" href="?status=paid">
                            <i class="bi bi-check-circle"></i> Đã thanh toán
                        </a>
                    </li>
                    <li class="nav-item ms-2">
                        <a class="nav-link <?= $status == 'pending' ? 'active' : '' ?>" href="?status=pending">
                            <i class="bi bi-hourglass-split"></i> Đang xử lý
                        </a>
                    </li>
                    <li class="nav-item ms-2">
                        <a class="nav-link <?= $status == 'cancel' ? 'active' : '' ?>" href="?status=cancel">
                            <i class="bi bi-x-circle"></i> Đã hủy
                        </a>
                    </li>
                </ul>
            </div>

            <form class="row g-2 align-items-center my-3" method="GET">
                <input type="hidden" name="status" value="<?= htmlspecialchars($status) ?>">
                <div class="col-auto">
                    <input type="date"
                           name="filter_date"
                           value="<?= htmlspecialchars($filter_date) ?>"
                           class="form-control">
                </div>
                <div class="col-md-4">
                    <input type="text"
                           name="search"
                           value="<?= htmlspecialchars($search) ?>"
                           class="form-control"
                           placeholder="Tìm người thanh toán">
                </div>
                <div class="col-auto">
                    <button class="btn btn-primary rounded-pill">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-dark">
                    <tr>
                        <th>Mã</th>
                        <th>Người thanh toán</th>
                        <th>Email</th>
                        <th>SDT</th>
                        <th>Thời gian</th>
                        <th>Số tiền</th>
                        <th>Phương thức</th>
                        <th>VNP Transaction</th>
                        <th>Trạng thái</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (empty($payments)): ?>
                        <tr>
                            <td colspan="9" class="text-center">Không có bản ghi nào.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($payments as $pay): ?>
                            <tr>
                                <td><?= htmlspecialchars($pay['payment_id']) ?></td>
                                <td><?= htmlspecialchars($pay['fullname']) ?></td>
                                <td><?= htmlspecialchars($pay['email']) ?></td>
                                <td><?= htmlspecialchars($pay['phone']) ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($pay['payment_at'])) ?></td>
                                <td><?= number_format($pay['amount'], 0, ',', '.') ?>₫</td>
                                <td><?= htmlspecialchars($pay['method']) ?></td>
                                <td><?= htmlspecialchars($pay['vnp_transaction_no'] ?? '-') ?></td>
                                <td>
                                    <?php
                                    $badge = 'secondary';
                                    if ($pay['pStatus'] == 'paid') $badge = 'success';
                                    elseif ($pay['pStatus'] == 'pending') $badge = 'warning';
                                    elseif ($pay['pStatus'] == 'cancel') $badge = 'danger';
                                    ?>
                                    <span class="badge bg-<?= $badge ?> px-3 py-2 text-capitalize">
                                        <?= htmlspecialchars($pay['pStatus']) ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach ?>
                    <?php endif ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</body>
</html>
