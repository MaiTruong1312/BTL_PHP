<?php
$title = "Chi tiết sản phẩm";
include __DIR__ . "/../app/Views/layouts/header.php";
require_once __DIR__ . "/../config/connect.php";

// Lấy id sản phẩm từ URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Lấy thông tin sản phẩm
$stmt = $conn->prepare("SELECT p.*, c.name AS cat_name, c.slug AS cat_slug 
                        FROM products p 
                        LEFT JOIN categories c ON p.category_id = c.id 
                        WHERE p.id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);
// Nếu user gửi form review
if (isset($_POST['submit_review'])) {
    $product_id = (int)$_POST['product_id'];
    $rating     = (int)$_POST['rating'];
    $comment    = trim($_POST['comment']);
    $user_id    = $_SESSION['user_id'] ?? 1;

    if ($product_id && $rating && $comment) {
        $stmtInsert = $conn->prepare("INSERT INTO reviews (product_id, user_id, rating, comment, created_at) 
                                      VALUES (?, ?, ?, ?, NOW())");
        $stmtInsert->execute([$product_id, $user_id, $rating, $comment]);

        // Reload lại trang để thấy review mới
        header("Location: product_detail.php?id=" . $product_id);
        exit;
    }
}

if ($product) {
    // Lấy thông số kỹ thuật (JSON -> mảng)
    $specs = [];
    if (!empty($product['specs'])) {
        $specs = json_decode($product['specs'], true);
    }

    // Lấy đánh giá sản phẩm
    $stmtReview = $conn->prepare("SELECT r.*, u.name AS user_name 
                                  FROM reviews r
                                  JOIN users u ON r.user_id = u.id
                                  WHERE r.product_id = ?
                                  ORDER BY r.created_at DESC");
    $stmtReview->execute([$id]);
    $reviews = $stmtReview->fetchAll(PDO::FETCH_ASSOC);

    // Lấy sản phẩm tương tự (cùng category, khác id)
    $stmtSimilar = $conn->prepare("SELECT id, name, slug, price 
                                   FROM products 
                                   WHERE category_id = ? AND id != ? 
                                   LIMIT 4");
    $stmtSimilar->execute([$product['category_id'], $id]);
    $similarProducts = $stmtSimilar->fetchAll(PDO::FETCH_ASSOC);
}
?>

<div class="container">
  <?php if ($product): ?>
    <h2 class="name">San Pham</h2>
    <div class="product-detail">
      <div class="product-image">
        <img src="../images/<?= htmlspecialchars($product['slug']) ?>.jpg" 
             alt="<?= htmlspecialchars($product['name']) ?>" 
             style="max-width:400px;">
      </div>

      <div class="product-info">
        <h2><?= htmlspecialchars($product['name']) ?></h2>
        <p class="price">Giá: <b><?= number_format($product['price'], 0, ',', '.') ?> VNĐ</b></p>
        <p><?= nl2br(htmlspecialchars($product['description'] ?? "Không có mô tả.")) ?></p>

        <form method="POST" action="cart.php">
          <input type="hidden" name="id" value="<?= $product['id'] ?>">
          <label>Số lượng: </label>
          <input type="number" name="qty" value="1" min="1" style="width:60px;">
          <button type="submit" class="btn">🛒 Thêm vào giỏ hàng</button>
        </form>
      </div>
    </div>
    
    <!-- Thông số kỹ thuật -->
    <div class="product-specs">
      <h2 class="name">🔧 Thông số kỹ thuật</h2>
      <table border="1" cellpadding="8" cellspacing="0">
        <tr><td><b>Danh mục</b></td><td><?= htmlspecialchars($product['cat_name']) ?></td></tr>
        <tr><td><b>Tình trạng</b></td><td><?= $product['stock'] > 0 ? 'Còn hàng' : 'Hết hàng' ?></td></tr>
        <tr><td><b>Bảo hành</b></td><td>36 tháng</td></tr>
        <?php if (!empty($specs)): ?>
          <?php foreach ($specs as $key => $val): ?>
            <tr>
              <td><b><?= htmlspecialchars(ucfirst($key)) ?></b></td>
              <td><?= htmlspecialchars($val) ?></td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </table>
    </div>

    <!-- Đánh giá sản phẩm -->
    <div class="product-reviews">
      <h2 class="name">⭐ Đánh giá sản phẩm</h2>
      <?php if ($reviews): ?>
        <?php foreach ($reviews as $r): ?>
          <div class="review">
            <p><b><?= htmlspecialchars($r['user_name']) ?></b> - 
               <?= str_repeat("⭐", $r['rating']) ?></p>
            <p><?= nl2br(htmlspecialchars($r['comment'])) ?></p>
            <hr>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p>Chưa có đánh giá nào cho sản phẩm này.</p>
      <?php endif; ?>
    </div>
<!-- Form viết đánh giá -->
<div class="review-form">
  <h2 class="name">✍️ Viết đánh giá của bạn</h2>
  <form method="POST" action="">
    <input type="hidden" name="product_id" value="<?= $product['id'] ?>">

    <label for="rating">Chọn số sao:</label>
    <select name="rating" id="rating" required>
      <option value="5">⭐ 5</option>
      <option value="4">⭐ 4</option>
      <option value="3">⭐ 3</option>
      <option value="2">⭐ 2</option>
      <option value="1">⭐ 1</option>
    </select>
    <br><br>

    <label for="comment">Nội dung đánh giá:</label><br>
    <textarea name="comment" id="comment" rows="4" cols="50" required></textarea>
    <br><br>

    <button type="submit" name="submit_review" class="btn">Gửi đánh giá</button>
  </form>
</div>

    <!-- Sản phẩm tương tự -->
    <div class="similar-products">
      <h2 class="name">🛍️ Sản phẩm tương tự</h2>
      <div style="display:flex; gap:20px;">
        <?php foreach ($similarProducts as $sp): ?>
          <div class="product">
            <img src="../images/<?= htmlspecialchars($sp['slug']) ?>.jpg" 
                 alt="<?= htmlspecialchars($sp['name']) ?>" style="width:100%;">
            <h4><?= htmlspecialchars($sp['name']) ?></h4>
            <p><b><?= number_format($sp['price'], 0, ',', '.') ?> VNĐ</b></p>
            <a href="product_detail.php?id=<?= $sp['id'] ?>" class="btn">Xem chi tiết</a>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  <?php else: ?>
    <p>Không tìm thấy sản phẩm.</p>
  <?php endif; ?>
</div>

<?php include __DIR__ . "/../app/Views/layouts/footer.php"; ?>
