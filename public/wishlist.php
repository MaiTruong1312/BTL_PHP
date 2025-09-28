<?php
session_start();
$title = "Wishlist - Yêu thích";

require_once __DIR__ . "/../config/connect.php";
include __DIR__ . "/../app/Views/layouts/header.php";

// Khởi tạo wishlist nếu chưa có
if (!isset($_SESSION['wishlist'])) {
    $_SESSION['wishlist'] = [];
}

// Xử lý thêm sản phẩm vào wishlist
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = (int)$_POST['id'];
    // Kiểm tra sản phẩm có tồn tại trong DB
    $stmt = $conn->prepare("SELECT id FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $exists = $stmt->fetchColumn();

    if ($exists && !in_array($id, $_SESSION['wishlist'])) {
        $_SESSION['wishlist'][] = $id;
    }
}

// Xử lý xóa sản phẩm khỏi wishlist
if (isset($_GET['remove'])) {
    $removeId = (int)$_GET['remove'];
    $_SESSION['wishlist'] = array_filter($_SESSION['wishlist'], fn($pid) => $pid !== $removeId);
}

// Lấy danh sách sản phẩm yêu thích từ DB
$wishlistProducts = [];

if (!empty($_SESSION['wishlist'])) {
    $placeholders = implode(',', array_fill(0, count($_SESSION['wishlist']), '?'));
    $stmt = $conn->prepare("SELECT id, name, price, slug FROM products WHERE id IN ($placeholders)");
    $stmt->execute($_SESSION['wishlist']);
    $wishlistProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<style>
body.main {
  margin: 0;
  background: linear-gradient(-45deg, #1e1e2f, #2a2a3a, #1a1a2a, #2e2e4a);
  background-size: 400% 400%;
  animation: gradientBG 15s ease infinite;
  font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
  color: #fff;
}

@keyframes gradientBG {
  0% { background-position: 0% 50%; }
  50% { background-position: 100% 50%; }
  100% { background-position: 0% 50%; }
}

.container {
  max-width: 900px;
  margin: 30px auto;
  padding: 20px 30px;
  background: rgba(255, 255, 255, 0.05);
  backdrop-filter: blur(15px);
  border-radius: 20px;
  box-shadow: 0 0 40px rgba(0, 123, 255, 0.2);
  text-align: center;
}

.container h2.name {
  margin-bottom: 25px;
  font-size: 24px;
  text-shadow: 0 0 6px rgba(255, 255, 255, 0.2);
}

.cart-table {
  width: 100%;
  border-collapse: collapse;
  margin-bottom: 20px;
}

.cart-table th,
.cart-table td {
  padding: 12px;
  text-align: center;
  border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.cart-table th {
  background: rgba(255, 255, 255, 0.08);
  font-weight: 600;
}

.cart-table tr:hover {
  background: rgba(255, 255, 255, 0.05);
}

.cart-table img {
  border-radius: 10px;
  box-shadow: 0 0 10px rgba(0,0,0,0.4);
}

.btn {
  display: inline-block;
  padding: 8px 15px;
  border-radius: 8px;
  background: linear-gradient(to right, #0d6efd, #66b2ff);
  color: #fff;
  font-weight: 600;
  text-decoration: none;
  transition: all 0.3s ease;
}

.btn:hover {
  background: linear-gradient(to right, #1a75ff, #80ccff);
  box-shadow: 0 0 15px rgba(0,123,255,0.5);
}

.cart-empty {
  text-align: center;
  padding: 30px;
}
</style>
<body class="main">
<div class="container">
  <h2 class ="name">❤️Wishlist của bạn</h2>
  <?php if (count($wishlistProducts) > 0): ?>
    <table class="cart-table">
      <tr>
        <th>Ảnh</th>
        <th>Sản phẩm</th>
        <th>Giá</th>
        <th>Xóa</th>
      </tr>
      <?php foreach ($wishlistProducts as $product): ?>
        <tr>
          <td><img src="../images/<?= htmlspecialchars($product['slug']) ?>.jpg" width="80" alt="<?= htmlspecialchars($product['name']) ?>"></td>
          <td><?= htmlspecialchars($product['name']) ?></td>
          <td><?= number_format($product['price'], 0, ',', '.') ?> VNĐ</td>
          <td><a href="wishlist.php?remove=<?= $product['id'] ?>" class="btn remove">Xóa</a></td>
        </tr>
      <?php endforeach; ?>
    </table>
  <?php else: ?>
    <img src="../images/wishlist.png" alt="Giỏ hàng trống" style="width: 200px; margin-bottom: 20px;">
    <p>Wishlist của bạn đang trống.</p>
    <a href="index.php" class="btn">🛍️ Tiếp tục mua sắm</a>
  <?php endif; ?>
</div>
</body>

<?php include __DIR__ . "/../app/Views/layouts/footer.php"; ?>
