<?php
$title = "Trang chủ - Computer Store";
include __DIR__ . "/../app/Views/layouts/header.php";
require_once __DIR__ . "/../config/connect.php";
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$wishlist = $_SESSION['wishlist'] ?? [];

// Lấy danh mục
$stmt = $conn->query("SELECT id, name, slug FROM categories WHERE parent_id IS NULL LIMIT 6");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Lấy sản phẩm nổi bật
$stmt = $conn->query("SELECT id, name, price, slug FROM products ORDER BY price DESC LIMIT 6");
$featured = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Lấy sản phẩm mới nhất
$stmt = $conn->query("SELECT id, name, price, slug FROM products ORDER BY created_at DESC LIMIT 6");
$newest = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Tất cả sản phẩm
$stmt = $conn->query("SELECT * FROM products ORDER BY created_at DESC");
$allProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- Thanh Tim kiem -->
<form id="main" action="search.php" method="GET" class="search-box" data-aos="fade-down">
  <input type="text" name="q" placeholder="Tìm sản phẩm..." required>
  <button type="submit">🔍</button>
</form>

<!-- Nút scroll to top -->
<button id="backToTop" title="Lên đầu trang">⬆</button>

<!-- Banner chính -->
<div class="banner-main" style="margin-bottom:20px; position:relative; overflow:hidden; border-radius:8px;" data-aos="zoom-in">
  <div class="slides" style="display:flex; transition: transform 0.5s ease;">
    <img src="../images/banner_main1.jpg" alt="Banner 1" style="width:1280px; height:720px; object-fit:cover;">
    <img src="../images/banner_main2.jpg" alt="Banner 2" style="width:1280px; height:720px; object-fit:cover;">
    <img src="../images/banner_main3.jpg" alt="Banner 3" style="width:1280px; height:720px; object-fit:cover;">
  </div>
</div>

<div class="container">
  <!-- Categories -->
  <h2 class="name" data-aos="fade-up">Danh mục sản phẩm</h2>
  <div class="categories">
    <?php foreach ($categories as $cat): ?>
      <div class="cat-card" data-aos="zoom-in" data-aos-delay="100">
        <img src="../images/<?= htmlspecialchars($cat['slug']) ?>_icon.jpg" alt="<?= htmlspecialchars($cat['name']) ?>">
        <a href="category.php?cat=<?= urlencode($cat['slug']) ?>">
          <?= htmlspecialchars($cat['name']) ?>
        </a>
      </div>
    <?php endforeach; ?>
  </div>

  <!-- Featured Products -->
  <h2 class="name" data-aos="fade-right">Sản phẩm nổi bật</h2>
  <div class="products">
    <?php foreach ($featured as $p): ?>
      <?php $isInWishlist = in_array($p['id'], $wishlist); ?>
      <div class="product" data-aos="flip-left" data-aos-delay="200">
        <img src="../images/<?= htmlspecialchars($p['slug']) ?>.jpg" alt="<?= htmlspecialchars($p['name']) ?>">
        <h3><?= htmlspecialchars($p['name']) ?></h3>
        <p>Giá: <b><?= number_format($p['price'], 0, ',', '.') ?> VNĐ</b></p>
        <button class="btn wishlist-btn" 
              data-id="<?= $p['id'] ?>" 
              aria-pressed="<?= $isInWishlist ? 'true' : 'false' ?>">
        <span class="heart <?= $isInWishlist ? 'filled' : 'empty' ?>">&#10084;</span>
      </button>
        <a href="product_detail.php?id=<?= $p['id'] ?>" class="btn">Xem chi tiết</a>
      </div>
    <?php endforeach; ?>
  </div>

  <!-- Latest Products -->
  <h2 id="products" class="name" data-aos="fade-left">Sản phẩm mới nhất</h2>
  <div class="products">
    <?php foreach ($newest as $p): ?>
      <?php $isInWishlist = in_array($p['id'], $wishlist); ?>
      <div class="product" data-aos="fade-up" data-aos-delay="150">
        <img src="../images/<?= htmlspecialchars($p['slug']) ?>.jpg" alt="<?= htmlspecialchars($p['name']) ?>">
        <h3><?= htmlspecialchars($p['name']) ?></h3>
        <p>Giá: <b><?= number_format($p['price'], 0, ',', '.') ?> VNĐ</b></p>
        <button class="btn wishlist-btn" 
              data-id="<?= $p['id'] ?>" 
              aria-pressed="<?= $isInWishlist ? 'true' : 'false' ?>">
        <span class="heart <?= $isInWishlist ? 'filled' : 'empty' ?>">&#10084;</span>
      </button>
        <a href="product_detail.php?id=<?= $p['id'] ?>" class="btn">Xem chi tiết</a>
      </div>
    <?php endforeach; ?>
  </div>

  <!-- All Products -->
  <h2 class="name" data-aos="fade-up">Tất cả sản phẩm</h2>
  <div class="products">
    <?php foreach ($allProducts as $p): ?>
      <?php $isInWishlist = in_array($p['id'], $wishlist); ?>
      <div class="product" data-aos="zoom-in-up" data-aos-delay="100">
        <img src="../images/<?= htmlspecialchars($p['slug']) ?>.jpg" alt="<?= htmlspecialchars($p['name']) ?>">
        <h3><?= htmlspecialchars($p['name']) ?></h3>
        <p>Giá: <b><?= number_format($p['price'], 0, ',', '.') ?> VNĐ</b></p>
        <button class="btn wishlist-btn" 
              data-id="<?= $p['id'] ?>" 
              aria-pressed="<?= $isInWishlist ? 'true' : 'false' ?>">
        <span class="heart <?= $isInWishlist ? 'filled' : 'empty' ?>">&#10084;</span>
      </button>
        <a href="product_detail.php?id=<?= $p['id'] ?>" class="btn">Xem chi tiết</a>
      </div>
    <?php endforeach; ?>
  </div>

  <!-- About Us Section -->
  <section class="menu" style="margin-top: 50px; width: 90%;" data-aos="fade-up">
    <div class="container" style="width: 90%;">
      <h2 class="name" data-aos="zoom-in">🌟 Về Chúng Tôi</h2>
      <p style="line-height:1.8; margin-bottom:20px; text-align:justify;" data-aos="fade-right">
        Trong bối cảnh công nghệ đang phát triển vượt bậc, máy tính và các thiết bị điện tử đã trở thành một phần không thể thiếu trong cuộc sống hàng ngày của mỗi người. 
      Từ việc học tập, làm việc, giải trí cho đến kết nối bạn bè, công nghệ luôn hiện diện và hỗ trợ chúng ta đạt được những mục tiêu lớn lao. 
      <b>TechCorePC</b> ra đời với sứ mệnh mang đến cho khách hàng những sản phẩm công nghệ chất lượng, chính hãng cùng dịch vụ tận tâm, 
      trở thành người bạn đồng hành đáng tin cậy trên hành trình chinh phục tri thức và sáng tạo của bạn.
      </p>
      <p style="line-height:1.8; margin-bottom:20px; text-align:justify;" data-aos="fade-up">
      Chúng tôi hiểu rằng, một chiếc máy tính hay laptop không đơn thuần chỉ là thiết bị, mà nó còn là công cụ để bạn theo đuổi đam mê, 
      xây dựng sự nghiệp và tận hưởng cuộc sống. Chính vì vậy, Computer Store không chỉ tập trung vào việc phân phối sản phẩm 
      mà còn chú trọng đến trải nghiệm mua sắm, dịch vụ hậu mãi cũng như tư vấn giải pháp phù hợp nhất cho từng khách hàng.
    </p>
      <h3 style="margin-top:30px; color:#ffff;" data-aos="fade-left">🎯 Sứ mệnh</h3>
      <p data-aos="fade-up">Sứ mệnh của chúng tôi là <b>mang công nghệ đến gần hơn với mọi người</b>, tạo ra một không gian mua sắm nơi khách hàng có thể tìm thấy mọi thứ mình cần: 
      từ máy tính để bàn mạnh mẽ, laptop gọn nhẹ phục vụ công việc, đến linh kiện phần cứng và phụ kiện hỗ trợ tối đa hiệu quả sử dụng. 
      Việc sở hữu thiết bị phù hợp sẽ giúp mỗi cá nhân phát huy tối đa khả năng và sáng tạo trong công việc cũng như học tập.</p>

      <h3 style="margin-top:30px; color:#ffff;" data-aos="fade-left">👁️ Tầm nhìn</h3>
      <p data-aos="fade-up">Computer Store hướng đến mục tiêu trở thành một trong những thương hiệu uy tín hàng đầu trong lĩnh vực phân phối sản phẩm công nghệ tại Việt Nam. 
      Chúng tôi không chỉ đơn thuần là một cửa hàng, mà muốn trở thành một <b>hệ sinh thái công nghệ</b>, nơi khách hàng có thể trải nghiệm, 
      cập nhật xu hướng mới nhất và nhận được sự hỗ trợ từ đội ngũ chuyên gia giàu kinh nghiệm.</p>

      <h3 style="margin-top:30px; color:#ffff;" data-aos="fade-left">💡 Giá trị cốt lõi</h3>
      <ul style="line-height:1.8; margin-left:20px;" data-aos="fade-up">
        <li><b>Chất lượng chính hãng:</b> Tất cả sản phẩm đều được nhập khẩu và phân phối từ các thương hiệu nổi tiếng như Dell, HP, Asus, Lenovo, Apple…</li>
      <li><b>Khách hàng là trung tâm:</b> Chúng tôi luôn lắng nghe và đặt nhu cầu của khách hàng lên hàng đầu.</li>
      <li><b>Đổi mới không ngừng:</b> Công nghệ thay đổi mỗi ngày, vì vậy chúng tôi luôn cập nhật nhanh nhất để mang đến sản phẩm tiên tiến nhất.</li>
      <li><b>Uy tín – Tin cậy:</b> Niềm tin của khách hàng là tài sản quý giá nhất, mọi cam kết bảo hành, giao hàng, giá cả luôn minh bạch.</li>
      </ul>

      <h3 style="margin-top:30px; color:#ffff;" data-aos="fade-left">👨‍👩‍👧‍👦 Đội ngũ phát triển</h3>
      <div style="display:flex; justify-content:center; gap:30px; flex-wrap:wrap; margin-top:20px;">
        <div style="text-align:center; width:220px;" data-aos="zoom-in">
          <img src="../images/member1.jpg" alt="Nguyễn Văn A" style="width:120px; height:120px; border-radius:50%; object-fit:cover; margin-bottom:10px;">
          <h4>Nguyễn Văn A</h4>
          <p>Founder & CEO</p>
        </div>
        <div style="text-align:center; width:220px;" data-aos="zoom-in" data-aos-delay="100">
          <img src="../images/member2.jpg" alt="Trần Thị B" style="width:120px; height:120px; border-radius:50%; object-fit:cover; margin-bottom:10px;">
          <h4>Trần Thị B</h4>
          <p>Designer</p>
        </div>
        <div style="text-align:center; width:220px;" data-aos="zoom-in" data-aos-delay="200">
          <img src="../images/member3.jpg" alt="Lê Văn C" style="width:120px; height:120px; border-radius:50%; object-fit:cover; margin-bottom:10px;">
          <h4>Lê Văn C</h4>
          <p>Developer</p>
        </div>
        <div style="text-align:center; width:220px;" data-aos="zoom-in" data-aos-delay="300">
          <img src="../images/member4.jpg" alt="Phạm Thị D" style="width:120px; height:120px; border-radius:50%; object-fit:cover; margin-bottom:10px;">
          <h4>Phạm Thị D</h4>
          <p>Customer Care</p>
        </div>
      </div>
      <h3 style="margin-top:30px; color:#ffff;">🚀 Cam kết với khách hàng</h3>
    <p style="line-height:1.8; text-align:justify;">
      Chúng tôi cam kết mang lại <b>sản phẩm chất lượng – dịch vụ tận tâm – giá cả hợp lý</b>. 
      Đến với Computer Store, bạn không chỉ mua được thiết bị, mà còn nhận được <b>giải pháp công nghệ toàn diện</b> cùng sự đồng hành lâu dài. 
      Sự hài lòng của khách hàng chính là động lực để chúng tôi hoàn thiện và phát triển từng ngày.
    </p>

    <h3 style="margin-top:30px; color:#ffff;">❤️ Lời kết</h3>
    <p style="line-height:1.8; text-align:justify;">
      Computer Store không chỉ là một cửa hàng bán máy tính, mà còn là nơi hội tụ của đam mê công nghệ, 
      là cộng đồng những người yêu thích sáng tạo và đổi mới. 
      Chúng tôi hy vọng rằng, với nỗ lực và tâm huyết của mình, Computer Store sẽ trở thành lựa chọn hàng đầu của bạn trong mọi nhu cầu về thiết bị công nghệ.  
      <br><br>
      <b>Cảm ơn bạn đã tin tưởng và đồng hành cùng chúng tôi!</b>
    </p>
    </div>
  </section>
</div>

<?php include __DIR__ . "/../app/Views/layouts/footer.php"; ?>

<!-- AOS -->
<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
  AOS.init({
    duration: 1000,
    once: true
  });
</script>
