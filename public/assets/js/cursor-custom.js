document.addEventListener("click", function (e) {
  const particles = 25; // số tia vừa phải
  for (let i = 0; i < particles; i++) {
    const particle = document.createElement("div");
    particle.className = "particle";

    // vị trí tại chuột
    particle.style.left = e.pageX + "px";
    particle.style.top = e.pageY + "px";

    // màu ngẫu nhiên
    const colors = ["#ff0", "#ff4500", "#0ff", "#0f0", "#f0f", "#fff"];
    particle.style.background =
      colors[Math.floor(Math.random() * colors.length)];

    // góc + bán kính ngẫu nhiên
    const angle = Math.random() * 2 * Math.PI;
    const radius = Math.random() * 80 + 80; // 80–160px

    // random thời gian bay
    const duration = (Math.random() * 0.6 + 0.8).toFixed(2); // 0.8–1.4s
    particle.style.animationDuration = duration + "s";

    // random chiều dài tia
    particle.style.height = Math.floor(Math.random() * 8 + 12) + "px"; // 12–20px

    particle.style.setProperty("--dx", Math.cos(angle) * radius + "px");
    particle.style.setProperty("--dy", Math.sin(angle) * radius + "px");

    document.body.appendChild(particle);

    setTimeout(() => {
      particle.remove();
    }, duration * 1000);
  }

  // thêm tàn lửa rơi
  const embers = 8;
  for (let i = 0; i < embers; i++) {
    const ember = document.createElement("div");
    ember.className = "ember";
    ember.style.left = e.pageX + "px";
    ember.style.top = e.pageY + "px";

    // random thời gian rơi
    const duration = (Math.random() * 0.7 + 1.3).toFixed(2); // 1.3–2s
    ember.style.animationDuration = duration + "s";

    document.body.appendChild(ember);

    setTimeout(() => {
      ember.remove();
    }, duration * 1000);
  }
  particle.style.offsetPath = `path("M0,0 C${Math.random() * 50},${
    Math.random() * 50
  } ${Math.random() * 100},${Math.random() * -100} ${Math.random() * 150},${
    Math.random() * -150
  }")`;
});

let lastScrollTop = 0;
const header = document.querySelector("header"); // nhớ là thẻ header của bạn

window.addEventListener("scroll", function () {
  let scrollTop = window.pageYOffset || document.documentElement.scrollTop;

  if (scrollTop > lastScrollTop) {
    // cuộn xuống -> ẩn header
    header.style.transform = "translateY(-100%)";
  } else {
    // cuộn lên -> hiện header
    header.style.transform = "translateY(0)";
  }

  lastScrollTop = scrollTop <= 0 ? 0 : scrollTop; // tránh giá trị âm
});
