const slides = document.querySelector('.banner-main .slides');
const slideImages = document.querySelectorAll('.banner-main .slides img');
const prev = document.querySelector('.banner-main .prev');
const next = document.querySelector('.banner-main .next');
const dots = document.querySelectorAll('.banner-main .dot');

let currentIndex = 0;

function showSlide(index) {
  if(index >= slideImages.length) currentIndex = 0;
  else if(index < 0) currentIndex = slideImages.length - 1;
  else currentIndex = index;

  const slideWidth = slideImages[0].clientWidth;
  slides.style.transform = `translateX(${-slideWidth * currentIndex}px)`;

  dots.forEach(dot => dot.style.background = '#bbb');
  dots[currentIndex].style.background = '#717171';
}

// Arrow click
next.addEventListener('click', () => showSlide(currentIndex + 1));
prev.addEventListener('click', () => showSlide(currentIndex - 1));

// Dot click
dots.forEach((dot, i) => dot.addEventListener('click', () => showSlide(i)));

// Auto slide every 5s
setInterval(() => showSlide(currentIndex + 1), 5000);

// Show first slide
showSlide(0);
