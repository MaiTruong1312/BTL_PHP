document.addEventListener("DOMContentLoaded", function () {
  document.querySelectorAll(".wishlist-btn").forEach((btn) => {
    btn.addEventListener("click", () => {
      const productId = btn.dataset.id;
      const isAdded = btn.getAttribute("aria-pressed") === "true";

      fetch("wishlistaction.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `id=${encodeURIComponent(productId)}&action=${
          isAdded ? "remove" : "add"
        }`,
      })
        .then((res) => res.json())
        .then((data) => {
          if (data.success) {
            btn.setAttribute("aria-pressed", (!isAdded).toString());
            const heart = btn.querySelector(".heart");
            heart.classList.toggle("filled");
            heart.classList.toggle("empty");

            console.log("Wishlist hiện tại:", data.wishlist);
          } else {
            console.error("Lỗi khi cập nhật wishlist:", data.message);
          }
        })
        .catch((err) => {
          console.error("Fetch error:", err);
        });
    });
  });
});
