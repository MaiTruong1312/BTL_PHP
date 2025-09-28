document.addEventListener("DOMContentLoaded", () => {
  const container = document.querySelector(".BAer-container");
  const numBAer = 100;
  const BAerElements = [];

  for (let i = 0; i < numBAer; i++) {
    const span = document.createElement("span");
    span.classList.add("BAer");
    span.innerText = "DOI VAI L*N M OI!";
    span.style.position = "absolute";
    const x = Math.random() * window.innerWidth;
    const y = Math.random() * window.innerHeight;
    const size = Math.random() * 24 + 12;

    span.style.left = `${x}px`;
    span.style.top = `${y}px`;
    span.style.fontSize = `${size}px`;

    container.appendChild(span);

    BAerElements.push({
      el: span,
      x: x,
      y: y,
      offsetX: 0,
      offsetY: 0,
      repelX: 0,
      repelY: 0,
    });
  }

  let mouseX = 0;
  let mouseY = 0;

  document.addEventListener("mousemove", (e) => {
    mouseX = e.clientX;
    mouseY = e.clientY;
  });

  function floatEffect() {
    const time = Date.now() * 0.001;

    BAerElements.forEach((obj) => {
      obj.offsetX = Math.sin(time + obj.x) * 5;
      obj.offsetY = Math.cos(time + obj.y) * 5;

      const dx = mouseX - obj.x;
      const dy = mouseY - obj.y;
      const dist = Math.sqrt(dx * dx + dy * dy);

      if (dist < 100) {
        const angle = Math.atan2(dy, dx);
        obj.repelX = Math.cos(angle) * -50;
        obj.repelY = Math.sin(angle) * -50;
      } else {
        obj.repelX *= 0.9;
        obj.repelY *= 0.9;
      }

      const tx = obj.offsetX + obj.repelX;
      const ty = obj.offsetY + obj.repelY;

      obj.el.style.transform = `translate(${tx}px, ${ty}px)`;
    });

    requestAnimationFrame(floatEffect);
  }

  floatEffect();
});
