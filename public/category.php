<?php
$title = "Danh mục sản phẩm";
include __DIR__ . "/../app/Views/layouts/header.php";
require_once __DIR__ . "/../config/connect.php";

$categorySlug = $_GET['cat'] ?? 'all';

// Lấy danh mục (nếu có) theo slug
if ($categorySlug !== 'all') {
    $stmt = $conn->prepare("SELECT id, name FROM categories WHERE slug = ? LIMIT 1");
    $stmt->execute([$categorySlug]);
    $category = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($category) {
        $categoryId = $category['id'];
        $categoryName = $category['name'];
        
        // Lấy sản phẩm theo category_id
        $stmt = $conn->prepare("SELECT id, name, price, slug FROM products WHERE category_id = ?");
        $stmt->execute([$categoryId]);
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $categoryName = "Danh mục không tồn tại";
        $products = [];
    }
} else {
    // Lấy tất cả sản phẩm
    $stmt = $conn->query("SELECT id, name, price, slug FROM products");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $categoryName = "Tất cả sản phẩm";
}
?>

<div class="container">
  <h2 class="name"><?= htmlspecialchars($categoryName) ?></h2>
  <div class="products">
    <?php if (count($products) > 0): ?>
      <?php foreach ($products as $p): ?>
        <div class="product">
          <img src="../images/<?= htmlspecialchars($p['slug']) ?>.jpg" alt="<?= htmlspecialchars($p['name']) ?>">
          <h3><?= htmlspecialchars($p['name']) ?></h3>
          <p>Giá: <b><?= number_format($p['price'], 0, ',', '.') ?> VNĐ</b></p>
          <button class="btn wishlist-btn" data-id="<?= $p['id'] ?>" aria-pressed="<?= $isInWishlist ? 'true' : 'false' ?>">
    <span class="heart <?= $isInWishlist ? 'filled' : 'empty' ?>">&#10084;</span>
  </button>
          <a href="product_detail.php?id=<?= $p['id'] ?>" class="btn">Xem chi tiết</a>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <p>Không tìm thấy sản phẩm nào trong danh mục này.</p>
    <?php endif; ?>
  </div>
</div>

<?php include __DIR__ . "/../app/Views/layouts/footer.php"; ?>
