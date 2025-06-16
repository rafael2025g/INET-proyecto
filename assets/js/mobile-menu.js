document.addEventListener("DOMContentLoaded", function () {
  const toggleButton = document.getElementById("mobileMenuToggle");
  const navMenu = document.getElementById("navMenu");
  const closeButton = document.getElementById("mobileMenuClose");
  const navOverlay = document.getElementById("navOverlay");

  // Abrir menú
  toggleButton.addEventListener("click", function () {
    navMenu.classList.add("active");
    toggleButton.classList.add("active");
  });

  // Cerrar menú desde botón cerrar
  closeButton.addEventListener("click", function () {
    navMenu.classList.remove("active");
    toggleButton.classList.remove("active");
  });

  // Cerrar menú desde overlay
  navOverlay.addEventListener("click", function () {
    navMenu.classList.remove("active");
    toggleButton.classList.remove("active");
  });
});
