document.addEventListener("DOMContentLoaded", () => {
  // Variables para control de animaciones únicas
  let countAnimated = false;
  const counters = document.querySelectorAll(".count");
  const fadeElements = document.querySelectorAll(".animate-on-scroll");

  // Función para animar conteos numéricos
  function animateCount(el) {
    const target = +el.getAttribute("data-target");
    const duration = 2000;
    const stepTime = 20;
    const steps = Math.ceil(duration / stepTime);
    let current = 0;
    let stepCount = 0;
    const increment = target / steps;

    const counterInterval = setInterval(() => {
      stepCount++;
      current += increment;
      if (stepCount >= steps) {
        el.textContent = target.toLocaleString();
        clearInterval(counterInterval);
      } else {
        el.textContent = Math.floor(current).toLocaleString();
      }
    }, stepTime);
  }

  // Verifica visibilidad de sección estadísticas y dispara animación
  function checkCountAnimation() {
    if (countAnimated) return;

    const statsSection = document.querySelector(".stats");
    if (!statsSection) return;

    const rect = statsSection.getBoundingClientRect();
    const windowHeight = window.innerHeight;

    if (rect.top < windowHeight && rect.bottom > 0) {
      counters.forEach(animateCount);
      countAnimated = true;
    }
  }

  // Agrega clase in-view para fade-in a los elementos visibles
  function checkFadeIn() {
    fadeElements.forEach(el => {
      if (el.classList.contains("in-view")) return;

      const rect = el.getBoundingClientRect();
      const windowHeight = window.innerHeight;

      if (rect.top < windowHeight * 0.9) {
        el.classList.add("in-view");
      }
    });
  }

  // Evento scroll unificado
  function onScroll() {
    checkCountAnimation();
    checkFadeIn();
  }

  // Escuchar scroll y cargar
  window.addEventListener("scroll", onScroll);
  // Ejecutar inmediatamente por si ya están visibles
  onScroll();
});
