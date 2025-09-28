// btn up top
// Lấy phần tử nút
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
