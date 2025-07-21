document.addEventListener("DOMContentLoaded", function () {
  const toggle = document.querySelector(".dropdown-toggle");
  const menu = document.querySelector(".dropdown-menu");

  if (toggle && menu) {
    toggle.addEventListener("click", function (e) {
      e.stopPropagation(); // Prevent closing immediately
      menu.classList.toggle("show");
    });

    document.addEventListener("click", function () {
      menu.classList.remove("show");
    });
  }
});
