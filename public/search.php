<?php
$title = "Tìm kiếm sản phẩm";
include __DIR__ . "/../app/Views/layouts/header.php";
require_once __DIR__ . "/../config/connect.php";

// Lấy từ khóa tìm kiếm
$keyword = isset($_GET['q']) ? trim($_GET['q']) : '';

$results = [];
if ($keyword !== '') {
  $stmt = $conn->prepare("SELECT id, name, price, slug FROM products WHERE name LIKE :keyword");
  $stmt->execute(['keyword' => '%' . $keyword . '%']);
  $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<div class="container">
  <h2 class="name">Kết quả tìm kiếm cho: "<?= htmlspecialchars($keyword) ?>"</h2>

  <?php if (empty($results)): ?>
    <p>Không tìm thấy sản phẩm nào phù hợp.</p>
  <?php else: ?>
    <div class="products">
      <?php foreach ($results as $p): ?>
        <div class="product">
          <img src="../images/<?= htmlspecialchars($p['slug']) ?>.jpg" alt="<?= htmlspecialchars($p['name']) ?>">
          <h3><?= htmlspecialchars($p['name']) ?></h3>
          <p>Giá: <b><?= number_format($p['price'], 0, ',', '.') ?> VNĐ</b></p>
          <a href="product_detail.php?id=<?= $p['id'] ?>" class="btn">Xem chi tiết</a>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>

<?php include __DIR__ . "/../app/Views/layouts/footer.php"; ?>
