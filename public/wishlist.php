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

<?php include __DIR__ . "/../app/Views/layouts/footer.php"; ?>
