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
<form id="main" action="search.php" method="GET" class="search-box" >
  <input type="text" name="q" placeholder="Tìm sản phẩm..." required>
  <button type="submit">🔍</button>
</form>

<!-- Nút scroll to top -->
<button id="backToTop" title="Lên đầu trang">⬆</button>

<!-- Banner chính -->
<div class="banner-main" style="margin-bottom:20px; position:relative; overflow:hidden; border-radius:8px;">
  <div class="slides" style="display:flex; transition: transform 0.5s ease;">
    <img src="../images/banner_main1.jpg" alt="Banner 1" style="width:1280px; height:720px; object-fit:cover;">
    <img src="../images/banner_main2.jpg" alt="Banner 2" style="width:1280px; height:720px; object-fit:cover;">
    <img src="../images/banner_main3.jpg" alt="Banner 3" style="width:1280px; height:720px; object-fit:cover;">
  </div>
</div>

<div class="container">
  <!-- Categories -->
  <h2 class="name focus-in-expand-fwd" >Danh mục sản phẩm</h2>
  <div class="categories">
    <?php foreach ($categories as $cat): ?>
      <div class="cat-card rotate-vert-center" >
        <img src="../images/<?= htmlspecialchars($cat['slug']) ?>_icon.jpg" alt="<?= htmlspecialchars($cat['name']) ?>">
        <a href="category.php?cat=<?= urlencode($cat['slug']) ?>">
          <?= htmlspecialchars($cat['name']) ?>
        </a>
      </div>
    <?php endforeach; ?>
  </div>

  <!-- Featured Products -->
  <h2 class="name focus-in-expand-fwd" >Sản phẩm nổi bật</h2>
  <div class="products">
    <?php foreach ($featured as $p): ?>
      <?php $isInWishlist = in_array($p['id'], $wishlist); ?>
      <div class="product" >
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
  <h2 class="name focus-in-expand-fwd" >Sản phẩm mới nhất</h2>
  <div class="products">
    <?php foreach ($newest as $p): ?>
      <?php $isInWishlist = in_array($p['id'], $wishlist); ?>
      <div class="product" >
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
  <h2 class="name focus-in-expand-fwd" >Tất cả sản phẩm</h2>
  <div class="products">
    <?php foreach ($allProducts as $p): ?>
      <?php $isInWishlist = in_array($p['id'], $wishlist); ?>
      <div class="product" >
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
  <section class="menu" style="margin-top: 50px; width: 90%;" >
    <div class="container" style="width: 90%;">
      <h2 class="name" >🌟 Về Chúng Tôi</h2>
      <p style="line-height:1.8; margin-bottom:20px; text-align:justify;" >
        Trong bối cảnh công nghệ đang phát triển vượt bậc, máy tính và các thiết bị điện tử đã trở thành một phần không thể thiếu trong cuộc sống hàng ngày của mỗi người. 
      Từ việc học tập, làm việc, giải trí cho đến kết nối bạn bè, công nghệ luôn hiện diện và hỗ trợ chúng ta đạt được những mục tiêu lớn lao. 
      <b>TechCorePC</b> ra đời với sứ mệnh mang đến cho khách hàng những sản phẩm công nghệ chất lượng, chính hãng cùng dịch vụ tận tâm, 
      trở thành người bạn đồng hành đáng tin cậy trên hành trình chinh phục tri thức và sáng tạo của bạn.
      </p>
      <p style="line-height:1.8; margin-bottom:20px; text-align:justify;" >
      Chúng tôi hiểu rằng, một chiếc máy tính hay laptop không đơn thuần chỉ là thiết bị, mà nó còn là công cụ để bạn theo đuổi đam mê, 
      xây dựng sự nghiệp và tận hưởng cuộc sống. Chính vì vậy, Computer Store không chỉ tập trung vào việc phân phối sản phẩm 
      mà còn chú trọng đến trải nghiệm mua sắm, dịch vụ hậu mãi cũng như tư vấn giải pháp phù hợp nhất cho từng khách hàng.
    </p>
      <h3 style="margin-top:30px; color:#ffff;" >🎯 Sứ mệnh</h3>
      <p >Sứ mệnh của chúng tôi là <b>mang công nghệ đến gần hơn với mọi người</b>, tạo ra một không gian mua sắm nơi khách hàng có thể tìm thấy mọi thứ mình cần: 
      từ máy tính để bàn mạnh mẽ, laptop gọn nhẹ phục vụ công việc, đến linh kiện phần cứng và phụ kiện hỗ trợ tối đa hiệu quả sử dụng. 
      Việc sở hữu thiết bị phù hợp sẽ giúp mỗi cá nhân phát huy tối đa khả năng và sáng tạo trong công việc cũng như học tập.</p>

      <h3 style="margin-top:30px; color:#ffff;" >👁️ Tầm nhìn</h3>
      <p >Computer Store hướng đến mục tiêu trở thành một trong những thương hiệu uy tín hàng đầu trong lĩnh vực phân phối sản phẩm công nghệ tại Việt Nam. 
      Chúng tôi không chỉ đơn thuần là một cửa hàng, mà muốn trở thành một <b>hệ sinh thái công nghệ</b>, nơi khách hàng có thể trải nghiệm, 
      cập nhật xu hướng mới nhất và nhận được sự hỗ trợ từ đội ngũ chuyên gia giàu kinh nghiệm.</p>

      <h3 style="margin-top:30px; color:#ffff;" >💡 Giá trị cốt lõi</h3>
      <ul style="line-height:1.8; margin-left:20px;" >
        <li><b>Chất lượng chính hãng:</b> Tất cả sản phẩm đều được nhập khẩu và phân phối từ các thương hiệu nổi tiếng như Dell, HP, Asus, Lenovo, Apple…</li>
      <li><b>Khách hàng là trung tâm:</b> Chúng tôi luôn lắng nghe và đặt nhu cầu của khách hàng lên hàng đầu.</li>
      <li><b>Đổi mới không ngừng:</b> Công nghệ thay đổi mỗi ngày, vì vậy chúng tôi luôn cập nhật nhanh nhất để mang đến sản phẩm tiên tiến nhất.</li>
      <li><b>Uy tín – Tin cậy:</b> Niềm tin của khách hàng là tài sản quý giá nhất, mọi cam kết bảo hành, giao hàng, giá cả luôn minh bạch.</li>
      </ul>

      <h3 style="margin-top:30px; color:#ffff;" >👨‍👩‍👧‍👦 Đội ngũ phát triển</h3>
      <div style="display:flex; justify-content:center; gap:30px; flex-wrap:wrap; margin-top:20px;">
        <div style="text-align:center; width:220px;" >
          <img src="../images/member1.jpg" alt="Nguyễn Văn A" style="width:120px; height:120px; border-radius:50%; object-fit:cover; margin-bottom:10px;">
          <h4>Nguyễn Văn A</h4>
          <p>Founder & CEO</p>
        </div>
        <div style="text-align:center; width:220px;" >
          <img src="../images/member2.jpg" alt="Trần Thị B" style="width:120px; height:120px; border-radius:50%; object-fit:cover; margin-bottom:10px;">
          <h4>Trần Thị B</h4>
          <p>Designer</p>
        </div>
        <div style="text-align:center; width:220px;" >
          <img src="../images/member3.jpg" alt="Lê Văn C" style="width:120px; height:120px; border-radius:50%; object-fit:cover; margin-bottom:10px;">
          <h4>Lê Văn C</h4>
          <p>Developer</p>
        </div>
        <div style="text-align:center; width:220px;" >
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
      Chúng tôi hy vọng rằng, với nỗ lực và tâm huyết của mình, TechCorePC sẽ trở thành lựa chọn hàng đầu của bạn trong mọi nhu cầu về thiết bị công nghệ.  
      <br><br>
      <b>Cảm ơn bạn đã tin tưởng và đồng hành cùng chúng tôi!</b>
    </p>
    </div>
  </section>
</div>

<?php include __DIR__ . "/../app/Views/layouts/footer.php"; ?>

<script src="https://unpkg.com/scrollreveal"></script>
<script>
  const backToTop = document.getElementById("backToTop");

// Khi scroll xuống 200px thì hiện nút
window.onscroll = function () {
  if (
    document.body.scrollTop > 200 ||
    document.documentElement.scrollTop > 200
  ) {
    backToTop.style.display = "block";
  } else {
    backToTop.style.display = "none";
  }
};
backToTop.addEventListener("click", () => {
  window.scrollTo({
    top: 0,
    behavior: "smooth",
    
  });
});

  ScrollReveal().reveal('.product', {
    duration: 800,
    distance: '40px',
    origin: 'bottom',
    easing: 'ease-in-out',
    interval: 150,   
    scale: 0.9,      
    reset: true
  });


  ScrollReveal().reveal('.cat-card', {
    duration: 500,
    distance: '60px',
    origin: 'left',
    easing: 'ease-out',
    interval: 200,
    reset: true
  });

  // Banner & các section chính: fade + trượt từ trên xuống
  ScrollReveal().reveal('#main, #products, .menu, .banner-main', {
    duration: 500,
    distance: '70px',
    origin: 'top',
    opacity: 0,
    easing: 'ease-in-out',
    reset: true
  });
</script>

