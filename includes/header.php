<?php
require_once "../config.php"; 
?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<link rel="stylesheet" href="../assets/css/style.css">
<script src="https://cdn.lordicon.com/lordicon.js"></script>

<nav class="navbar navbar-expand-lg main-navbar">
    <div class="container d-flex align-items-center justify-content-between">
        <!-- Logo -->
        <a class="navbar-brand d-flex align-items-center" href="../index.php">
            <img src="../assets/images/logo.png" alt="Logo" height="50" width="210">
        </a>

        <!-- Tìm kiếm -->
        <div class="search-container d-flex flex-grow-1 mx-4">
            <form class="d-flex w-100" id="searchForm" action="search.php" method="GET">
                <input class="form-control search-input" type="search" name="query" placeholder="Tìm kiếm sự kiện..." aria-label="Search">
                <button class="btn search-btn" type="submit">
                    <i class="fas fa-search"></i>
                </button>
            </form>
        </div>

        <?php if (isset($_SESSION["user_id"])) { ?>
            <div class="user-greeting fw-bold ms-2 text-nowrap">
                Xin chào, <?php echo htmlspecialchars($_SESSION["fullname"] ?? "User"); ?>!
            </div>
        <?php } ?>

        <!-- Menu toggle trên mobile -->
        <button class="navbar-toggler text-white" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <i class="bi bi-list"></i>
        </button>

        <!-- Menu chính -->
        <div class="collapse navbar-collapse justify-content-end text-center" id="navbarNav">
            <ul class="navbar-nav align-items-center">
                <li class="nav-item">
                    <a class="nav-link nav-pill nav-pill-main" href="events.php">
                        <i class="fas fa-calendar-alt"></i> Sự kiện
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link nav-pill nav-pill-main" href="my_tickets.php" id="myTicketsBtn">
                        <i class="bi bi-ticket-perforated-fill"></i> Vé của tôi
                    </a>
                </li>

                <?php if (isset($_SESSION["user_id"])) { ?>
                    <li class="nav-item">
                        <a class="nav-link nav-pill nav-logout" href="../auth/logout.php">
                            <i class="fas fa-sign-out-alt"></i> Đăng xuất
                        </a>
                    </li>
                <?php } else { ?>
                    <li class="nav-item">
                        <button class="btn openLogin">
                            <i class="fas fa-sign-in-alt"></i> Đăng nhập
                        </button>
                    </li>
                <?php } ?>
            </ul>
        </div>
    </div>
</nav>

<div class="sub-navbar">
    <ul class="sub-nav-list">
        <li><a href="../pages/event_type.php?event_type=<?php echo urlencode('all'); ?>">Tất cả</a></li>
        <li><a href="../pages/event_type.php?event_type=<?php echo urlencode('music'); ?>">Âm nhạc</a></li>
        <li><a href="../pages/event_type.php?event_type=<?php echo urlencode('art'); ?>">Văn hóa nghệ thuật</a></li>
        <li><a href="../pages/event_type.php?event_type=<?php echo urlencode('visit'); ?>">Tham quan</a></li>
        <li><a href="../pages/event_type.php?event_type=<?php echo urlencode('tournament'); ?>">Giải đấu</a></li>
        <li><a href="../pages/event_type.php?event_type=<?php echo urlencode('movie'); ?>">Phim</a></li>
    </ul>
</div>

