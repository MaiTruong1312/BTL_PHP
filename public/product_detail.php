<?php
session_start();
$title = "Chi tiết sản phẩm";
require_once __DIR__ . "/../config/connect.php";
include __DIR__ . "/../app/Views/layouts/header.php";

// Lấy user ID nếu đã login
$user_id = $_SESSION['user']['id'] ?? null;

// Lấy id sản phẩm từ URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Lấy thông tin sản phẩm
$stmt = $conn->prepare("SELECT p.*, c.name AS cat_name, c.slug AS cat_slug 
                        FROM products p 
                        LEFT JOIN categories c ON p.category_id = c.id 
                        WHERE p.id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

// Biến thông báo
$error_review = "";
$success_review = "";

// Nếu user gửi form review
if (isset($_POST['submit_review'])) {
    $product_id = (int)$_POST['product_id'];
    $rating     = (int)$_POST['rating'];
    $comment    = trim($_POST['comment']);

    if (!$user_id) {
        $error_review = "Bạn cần đăng nhập để gửi đánh giá.";
    } elseif ($product_id && $rating && $comment) {
        $stmtInsert = $conn->prepare("INSERT INTO reviews (product_id, user_id, rating, comment, created_at) 
                                      VALUES (?, ?, ?, ?, NOW())");
        $stmtInsert->execute([$product_id, $user_id, $rating, $comment]);
        $success_review = "Đánh giá của bạn đã được gửi thành công!";
        // Reload lại trang để thấy review mới
        header("Location: product_detail.php?id=" . $product_id);
        exit;
    } else {
        $error_review = "Vui lòng điền đầy đủ thông tin đánh giá.";
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
<style>
/* === Product detail chung === */
.product-detail {
  display: flex;
  gap: 30px;
  margin-bottom: 40px;
}

.product-image img {
  max-width: 400px;
  border-radius: 10px;
  box-shadow: 0 4px 15px rgba(0,0,0,0.2);
}

.product-info {
  flex: 1;
  background: rgba(255,255,255,0.05);
  padding: 20px;
  border-radius: 12px;
}

.product-info h2 {
  margin-top: 0;
}

.product-info .price {
  font-size: 20px;
  color: #ffcc00;
}

/* === Thông số kỹ thuật === */
.product-specs table {
  width: 100%;
  border-collapse: collapse;
  background: rgba(255,255,255,0.05);
  border-radius: 10px;
  overflow: hidden;
}

.product-specs td {
  padding: 12px;
  border-bottom: 1px solid rgba(255,255,255,0.1);
}

/* === Đánh giá === */
.product-reviews .review {
  background: rgba(255,255,255,0.05);
  padding: 15px;
  border-radius: 10px;
  margin-bottom: 15px;
}

.success-msg {
  color: lightgreen;
  margin: 10px 0;
}

.error-msg {
  color: pink;
  margin: 10px 0;
}

/* === Form đánh giá === */
.review-form {
  margin-top: 30px;
  background: rgba(255,255,255,0.05);
  padding: 20px;
  border-radius: 12px;
}

.review-form textarea {
  width: 100%;
  padding: 12px 15px;
  margin-top: 10px;
  border-radius: 10px;
  border: none;
  background: rgba(255,255,255,0.08);
  color: #fff;
  resize: vertical;
  min-height: 120px;
  box-shadow: inset 0 0 10px rgba(0,0,0,0.25);
  outline: none;
  box-sizing: border-box;
  transition: all 0.3s ease;
}

.review-form textarea:focus {
  background: rgba(0,0,0,0.25);
  border: 1px solid #00d4ff;
  box-shadow: 0 0 12px rgba(0,212,255,0.6);
}

.review-form textarea::placeholder {
  color: #aaa;
  font-style: italic;
}

/* === Star rating kiểu CH Play === */
.star-rating {
  display: flex;
  flex-direction: row-reverse;
  justify-content: center; /* căn giữa */
  gap: 5px;
  font-size: 32px;
  margin: 10px 0;
}
.star-rating input {
  display: none;
}

.star-rating label {
  cursor: pointer;
  color: #bbb;
  transition: color 0.2s, transform 0.2s;
}

.star-rating label:hover,
.star-rating label:hover ~ label,
.star-rating input:checked ~ label {
  color: #ffb400;
  text-shadow: 0 0 5px #ffdd00, 0 0 10px #ff9900;
  transform: scale(1.2);
}

.star-rating input:checked ~ label {
  color: gold;
}
.stars {
  --rating: 0;
  display: inline-block;
  font-size: 18px;
  unicode-bidi: bidi-override;
  color: #ccc;
  position: relative;
}

.stars::before {
  content: "★★★★★";
}

.stars::after {
  content: "★★★★★";
  color: white;
  position: absolute;
  left: 0;
  top: 0;
  width: calc(var(--rating)/5*100%);
  overflow: hidden;
}

/* === Sản phẩm tương tự === */
.similar-products {
  margin-top: 40px;
}

.similar-products .product {
  width: 220px;
  background: rgba(255,255,255,0.05);
  padding: 15px;
  border-radius: 12px;
  text-align: center;
  transition: transform 0.3s ease;
}

.similar-products .product img {
  border-radius: 8px;
  margin-bottom: 10px;
}

.similar-products .product:hover {
  transform: scale(1.05);
}

</style>
<div class="container">
  <?php if ($product): ?>
    <h2 class="name">Chi tiết sản phẩm</h2>
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
      <table>
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
      <?php if ($success_review): ?><p class="success-msg"><?= $success_review ?></p><?php endif; ?>
      <?php if ($error_review): ?><p class="error-msg"><?= $error_review ?></p><?php endif; ?>

      <?php if ($reviews): ?>
        <?php foreach ($reviews as $r): ?>
          <div class="review">
            <p><b><?= htmlspecialchars($r['user_name']) ?></b> 
              <span class="stars" style="--rating: <?= $r['rating'] ?>"></span>
            </p>
            <p><?= nl2br(htmlspecialchars($r['comment'])) ?></p>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p>Chưa có đánh giá nào cho sản phẩm này.</p>
      <?php endif; ?>
    </div>

    <!-- Form viết đánh giá -->
    <div class="review-form">
      <h2 class="name">✍️ Viết đánh giá của bạn</h2>
      <?php if ($user_id): ?>
        <form method="POST" action="">
          <input type="hidden" name="product_id" value="<?= $product['id'] ?>">

          <!-- Thay đoạn select bằng rating UI -->
<label for="rating">Chọn số sao:</label>
<div class="star-rating">
  <?php for ($i = 5; $i >= 1; $i--): ?>
    <input type="radio" id="star<?= $i ?>" name="rating" value="<?= $i ?>" required>
    <label for="star<?= $i ?>">⭐</label>
  <?php endfor; ?>
</div>


          <label for="comment">Nội dung đánh giá:</label>
          <textarea name="comment" id="comment" rows="4" required></textarea>

          <button type="submit" name="submit_review" class="btn">Gửi đánh giá</button>
        </form>
      <?php else: ?>
        <p class="error-msg">Vui lòng <a href="login.php">đăng nhập</a> để viết đánh giá.</p>
      <?php endif; ?>
    </div>

    <!-- Sản phẩm tương tự -->
    <div class="similar-products">
      <h2 class="name">🛍️ Sản phẩm tương tự</h2>
      <div style="display:flex; gap:20px; flex-wrap:wrap;">
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
<script>
document.addEventListener('DOMContentLoaded', () => {
  const starRatings = document.querySelectorAll('.star-rating');
  
  starRatings.forEach(starRating => {
    const stars = Array.from(starRating.querySelectorAll('label')); // chuyển thành array
    const inputs = Array.from(starRating.querySelectorAll('input'));

    // vì CSS row-reverse, đảo ngược mảng để index khớp
    stars.reverse();
    inputs.reverse();

    stars.forEach((star, idx) => {
      // Hover effect: hiển thị số sao
      star.addEventListener('mouseenter', () => {
        sstars.forEach((s, i) => {
  s.style.color = i <= idx ? '#ffb400' : '#bbb';
});

      });

      // Reset hover khi mouse leave
      starRating.addEventListener('mouseleave', () => {
        const checked = starRating.querySelector('input:checked');
        let checkedIdx = -1;
        if (checked) {
          const val = parseInt(checked.value);
          checkedIdx = stars.length - val; // convert value sang index
        }
        stars.forEach((s, i) => {
          s.style.color = i <= checkedIdx ? 'gold' : '#ccc';
        });
      });

      // Click: chọn sao
      star.addEventListener('click', () => {
        inputs[idx].checked = true;
        // trigger change nếu cần
        inputs[idx].dispatchEvent(new Event('change'));
      });
    });

    // Khởi tạo hiển thị dựa trên checked
    const initChecked = starRating.querySelector('input:checked');
    if (initChecked) {
      const val = parseInt(initChecked.value);
      stars.forEach((s, i) => {
  s.style.color = i <= checkedIdx ? '#ffb400' : '#bbb';
});

    }
  });
});
</script>


<?php include __DIR__ . "/../app/Views/layouts/footer.php"; ?>
