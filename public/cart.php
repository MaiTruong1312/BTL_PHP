<?php
session_start();
$title = "Giỏ hàng";
include __DIR__ . "/../app/Views/layouts/header.php";
require_once __DIR__ . "/../config/connect.php"; // Kết nối DB

// Khởi tạo giỏ hàng nếu chưa có
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Xử lý thêm sản phẩm vào giỏ
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['id'])) {
    $id = (int)$_POST['id'];
    $qty = max(1, (int)($_POST['qty'] ?? 1));

    // Lấy thông tin sản phẩm từ DB
    $stmt = $conn->prepare("SELECT id, name, price, slug FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($product) {
        if (isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id]['qty'] += $qty;
        } else {
            $_SESSION['cart'][$id] = [
                'id' => $product['id'],
                'name' => $product['name'],
                'price' => $product['price'],
                'img' => "../images/" . $product['slug'] . ".jpg",
                'qty' => $qty
            ];
        }
    }
}

// Cập nhật số lượng
if (isset($_POST['update']) && isset($_POST['quantities'])) {
    foreach ($_POST['quantities'] as $id => $qty) {
        if (isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id]['qty'] = max(1, (int)$qty);
        }
    }
}

// Xóa sản phẩm
if (isset($_GET['remove'])) {
    $id = (int)$_GET['remove'];
    unset($_SESSION['cart'][$id]);
}

// Tính tổng tiền
$total = 0;
foreach ($_SESSION['cart'] as $item) {
    $total += $item['price'] * $item['qty'];
}
?>

<div class="container">
  <h2 class="name">🛒 Giỏ hàng của bạn</h2>

  <?php if (count($_SESSION['cart']) > 0): ?>
    <form method="POST" action="cart.php">
      <table class="cart-table">
        <tr>
          <th>Ảnh</th>
          <th>Sản phẩm</th>
          <th>Giá</th>
          <th>Số lượng</th>
          <th>Thành tiền</th>
          <th>Xóa</th>
        </tr>
        <?php foreach ($_SESSION['cart'] as $item): ?>
          <tr>
            <td><img src="<?= $item['img'] ?>" width="80"></td>
            <td><?= htmlspecialchars($item['name']) ?></td>
            <td><?= number_format($item['price'], 0, ',', '.') ?> VNĐ</td>
            <td>
              <input type="number" name="quantities[<?= $item['id'] ?>]" value="<?= $item['qty'] ?>" min="1" style="width:60px;">
            </td>
            <td><b><?= number_format($item['price'] * $item['qty'], 0, ',', '.') ?> VNĐ</b></td>
            <td><a href="cart.php?remove=<?= $item['id'] ?>" class="btn remove">Xóa</a></td>
          </tr>
        <?php endforeach; ?>
      </table>

      <div class="cart-actions">
        <button type="submit" name="update" class="btn">Cập nhật giỏ hàng</button>
      </div>

      <h3>Tổng cộng: <?= number_format($total, 0, ',', '.') ?> VNĐ</h3>
      <a href="checkout.php" class="btn checkout">Thanh toán</a>
    </form>
  <?php else: ?>
    <div class="cart-empty">
      <img src="../images/cart.png" alt="Giỏ hàng trống" style="width: 200px; margin-bottom: 20px;">
      <h3>Giỏ hàng của bạn đang trống</h3>
      <p>Hãy khám phá thêm các sản phẩm hấp dẫn của chúng tôi!</p>
      <a href="index.php" class="btn">🛍️ Tiếp tục mua sắm</a>

      <!-- Gợi ý sản phẩm từ DB -->
      <div class="product-suggestions">
        <h2 class="name">🌟 Gợi ý cho bạn</h2>
        <div class="products">
          <?php
          $stmt = $conn->query("SELECT id, name, price, slug FROM products ORDER BY RAND() LIMIT 12");
          $suggestions = $stmt->fetchAll(PDO::FETCH_ASSOC);
          foreach ($suggestions as $p):
          ?>
            <div class="product">
              <img src="../images/<?= htmlspecialchars($p['slug']) ?>.jpg" width="100">
              <p><?= htmlspecialchars($p['name']) ?></p>
              <p><b><?= number_format($p['price'], 0, ',', '.') ?> VNĐ</b></p>
              <form method="POST" action="cart.php">
                <input type="hidden" name="id" value="<?= $p['id'] ?>">
                <input type="hidden" name="qty" value="1">
                <button type="submit" class="btn">Thêm vào giỏ</button>
              </form>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  <?php endif; ?>
</div>

<?php include __DIR__ . "/../app/Views/layouts/footer.php"; ?>
