<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$username = $_SESSION['username'] ?? null;
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title><?= $title ?? "Computer Store" ?></title>
  <link rel="stylesheet" href="../public/assets/css/style.css">
  <link rel="stylesheet" href="../public/assets/css/cursor-custom.css" />
    <link rel="stylesheet" href="../public/assets/css/cart.css" />
  <script src="../public/assets/js/cursor-custom.js"></script>
  <script src="../public/assets/js/wishlistaction.js"></script>
  <script src="../public/assets/js/index.js"></script>
  <!-- AOS CSS -->
<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
<script>
document.addEventListener("DOMContentLoaded", function() {
  let lastScrollTop = 0;
  const header = document.querySelector("header");

  window.addEventListener("scroll", function() {
    let scrollTop = window.pageYOffset || document.documentElement.scrollTop;
    
    if (scrollTop > lastScrollTop) {
      header.classList.add("hide");   // cuộn xuống -> ẩn mượt
    } else {
      header.classList.remove("hide"); // cuộn lên -> hiện mượt
    }
    
    lastScrollTop = scrollTop <= 0 ? 0 : scrollTop;
  });
});
</script>



  </head>
<body class = "main">
  <!-- Header -->
  <header>
    <div class="logo">
  <a href="../public/index.php" style="display: flex; align-items: center; gap: 10px;">
    <img src="../images/logo.jpg" alt="Computer Store Logo" />
    <span>TechCorePC</span>
  </a>
</div>

    <nav>
      <a href="../public/index.php">Trang chủ</a>
      <a href="../public/cart.php">Giỏ hàng</a>
      <a href="../public/wishlist.php">WishList</a>
      <?php if ($username): ?>
        <span>Xin chào, <?= htmlspecialchars($username) ?></span>
        <a href="../app/Views/auth/logout.php">Đăng xuất</a>
      <?php else: ?>
        <a href="../app/Views/auth/login.php">Đăng nhập</a>
        <a href="../app/Views/auth/register.php">Đăng ký</a>
      <?php endif; ?>
    </nav>
  </header>
