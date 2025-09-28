<?php
$title = "Cảm ơn bạn";
include __DIR__ . "/../app/Views/layouts/header.php";
$orderId = $_GET['order_id'] ?? 0;
?>
<div class="container">
  <h2>🎉 Cảm ơn bạn đã đặt hàng!</h2>
  <p>Đơn hàng của bạn (#<?= htmlspecialchars($orderId) ?>) đã được ghi nhận.</p>
  <p>Chúng tôi sẽ liên hệ với bạn sớm nhất để xác nhận và giao hàng.</p>
  <a href="index.php" class="btn">Tiếp tục mua sắm</a>
</div>
<?php include __DIR__ . "/../app/Views/layouts/footer.php"; ?>
