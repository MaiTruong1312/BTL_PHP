<?php
session_start();
$title = "Giỏ hàng";
include __DIR__ . "/../app/Views/layouts/header.php";
require_once __DIR__ . "/../config/connect.php";
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['id'])) {
    $id = (int)$_POST['id'];
    $qty = max(1, (int)($_POST['qty'] ?? 1));
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

if (isset($_GET['remove'])) {
    $id = (int)$_GET['remove'];
    unset($_SESSION['cart'][$id]);
}
$total = 0;
foreach ($_SESSION['cart'] as $item) {
    $total += $item['price'] * $item['qty'];
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

input.qty {
  width: 60px;
  padding: 6px;
  border: none;
  border-radius: 8px;
  background: rgba(255,255,255,0.1);
  color: #fff;
  text-align: center;
}

input.qty:focus {
  outline: none;
  background: rgba(255,255,255,0.2);
  box-shadow: 0 0 10px rgba(0,123,255,0.6);
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

.btn.checkout {
  margin-top: 20px;
  display: block;
  text-align: center;
}

.cart-empty {
  text-align: center;
  padding: 30px;
}

.product-suggestions {
  margin-top: 40px;
}

.product-suggestions h2 {
  margin-bottom: 20px;
}

.products {
  display: grid;
  grid-template-columns: repeat(auto-fill,minmax(180px,1fr));
  gap: 20px;
}

.product {
  background: rgba(255,255,255,0.05);
  padding: 15px;
  border-radius: 12px;
  text-align: center;
  box-shadow: 0 0 15px rgba(0,0,0,0.3);
  transition: transform 0.2s;
}

.product:hover {
  transform: translateY(-5px);
}
</style>

<body class="main">
  
<div class="container">
  <h2 class="name">🛒 Giỏ hàng của bạn</h2>
  <?php if (count($_SESSION['cart']) > 0): ?>
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
          <td data-value="<?= $item['price'] ?>">
            <?= number_format($item['price'], 0, ',', '.') ?> VNĐ
          </td>
          <td>
            <input type="number" class="qty" data-id="<?= $item['id'] ?>" 
                   value="<?= $item['qty'] ?>" min="1" style="width:60px;">
          </td>
          <td class="subtotal"><b><?= number_format($item['price'] * $item['qty'], 0, ',', '.') ?> VNĐ</b></td>
          <td><a href="cart.php?remove=<?= $item['id'] ?>" class="btn remove">Xóa</a></td>
        </tr>
      <?php endforeach; ?>
    </table>

    <h3 id="cart-total">Tổng cộng: <?= number_format($total, 0, ',', '.') ?> VNĐ</h3>
    <a href="checkout.php" class="btn checkout">Thanh toán</a>
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
</body>

<script>
// Auto update totals + gửi AJAX
document.addEventListener("DOMContentLoaded", function () {
  const qtyInputs = document.querySelectorAll(".qty");
  const totalElement = document.querySelector("#cart-total");

  function formatCurrency(num) {
    return new Intl.NumberFormat("vi-VN").format(num) + " VNĐ";
  }

  function updateTotals() {
    let total = 0;
    document.querySelectorAll("tr").forEach(row => {
      const priceCell = row.querySelector("td[data-value]");
      const qtyInput = row.querySelector("input.qty");
      const subtotalCell = row.querySelector(".subtotal");
      if (priceCell && qtyInput && subtotalCell) {
        const price = parseInt(priceCell.dataset.value);
        const qty = parseInt(qtyInput.value);
        const subtotal = price * qty;
        subtotalCell.innerHTML = "<b>" + formatCurrency(subtotal) + "</b>";
        total += subtotal;
      }
    });
    totalElement.innerHTML = "Tổng cộng: " + formatCurrency(total);
  }

  qtyInputs.forEach(input => {
    input.addEventListener("input", function () {
      updateTotals();

      // Gửi AJAX update session
      fetch("update_cart.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ id: this.dataset.id, qty: this.value })
      });
    });
  });
});
</script>

<?php include __DIR__ . "/../app/Views/layouts/footer.php"; ?>
