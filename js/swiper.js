document.addEventListener("DOMContentLoaded", function () {
  var swiper = new Swiper(".hero-slider", {
    loop: true,
    grabCursor: true,
    effect: "flip",
    pagination: {
      el: ".swiper-pagination",
      clickable: true,
    },
  });
});
