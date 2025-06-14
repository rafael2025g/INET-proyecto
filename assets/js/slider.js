// Estado del slider
      let currentSlide = 0;
      const totalSlides = 5;
      let isTransitioning = false;

      // Elementos del DOM
      const sliderImages = document.querySelectorAll(".slider-image");
      const thumbnails = document.querySelectorAll(".thumb");
      const prevBtn = document.getElementById("prevBtn");
      const nextBtn = document.getElementById("nextBtn");
      const currentSlideSpan = document.getElementById("currentSlide");
      const totalSlidesSpan = document.getElementById("totalSlides");
      const loadingOverlay = document.getElementById("loadingOverlay");
      const packageDetail = document.getElementById("packageDetail");
      const addToCartBtn = document.getElementById("addToCartBtn");

      // Inicialización
      document.addEventListener("DOMContentLoaded", () => {
        initializeSlider();
        initializeLoadingAnimation();
        initializeInteractions();
      });

      function initializeSlider() {
        currentSlideSpan.textContent = currentSlide + 1;
        totalSlidesSpan.textContent = totalSlides;

        prevBtn.addEventListener("click", () => previousSlide());
        nextBtn.addEventListener("click", () => nextSlide());

        thumbnails.forEach((thumb, index) => {
          thumb.addEventListener("click", () => goToSlide(index));
        });

        startAutoPlay();

        const sliderContainer = document.querySelector(".slider-container");
        sliderContainer.addEventListener("mouseenter", stopAutoPlay);
        sliderContainer.addEventListener("mouseleave", startAutoPlay);

        document.addEventListener("keydown", handleKeyNavigation);
      }

      function initializeLoadingAnimation() {
        setTimeout(() => {
          loadingOverlay.classList.add("hidden");

          setTimeout(() => {
            packageDetail.classList.add("loaded");
          }, 100);
        }, 1500);
      }

      function initializeInteractions() {
        addToCartBtn.addEventListener("click", () => {
          addToCartBtn.style.transform = "scale(0.95)";
          addToCartBtn.innerHTML = `
                  <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <path d="M20 6L9 17l-5-5"/>
                  </svg>
                  ¡Agregado!
              `;

          setTimeout(() => {
            addToCartBtn.style.transform = "scale(1)";
            addToCartBtn.innerHTML = `
                      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                          <circle cx="9" cy="21" r="1"></circle>
                          <circle cx="20" cy="21" r="1"></circle>
                          <path d="m1 1 4 4 2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                      </svg>
                      Agregar al Carrito
                  `;
          }, 2000);
        });

        observeElements();
      }

      function nextSlide() {
        if (isTransitioning) return;
        currentSlide = (currentSlide + 1) % totalSlides;
        updateSlider();
      }

      function previousSlide() {
        if (isTransitioning) return;
        currentSlide = (currentSlide - 1 + totalSlides) % totalSlides;
        updateSlider();
      }

      function goToSlide(index) {
        if (isTransitioning || index === currentSlide) return;
        currentSlide = index;
        updateSlider();
      }

      // Actualizar slider
      function updateSlider() {
        isTransitioning = true;

        // Actualizar imágenes
        sliderImages.forEach((img, index) => {
          img.classList.toggle("active", index === currentSlide);
        });

        // Actualizar thumbnails
        thumbnails.forEach((thumb, index) => {
          thumb.classList.toggle("active", index === currentSlide);
        });

        // Actualizar indicador
        currentSlideSpan.textContent = currentSlide + 1;

        // Animación de las flechas
        animateArrows();

        setTimeout(() => {
          isTransitioning = false;
        }, 500);
      }

      // Animación de las flechas
      function animateArrows() {
        [prevBtn, nextBtn].forEach((btn) => {
          btn.style.transform = "translateY(-50%) scale(1.2)";
          setTimeout(() => {
            btn.style.transform = "translateY(-50%) scale(1)";
          }, 150);
        });
      }

      // Auto-play
      let autoPlayInterval;

      function startAutoPlay() {
        stopAutoPlay(); // Limpiar intervalo existente
        autoPlayInterval = setInterval(() => {
          nextSlide();
        }, 4000);
      }

      function stopAutoPlay() {
        if (autoPlayInterval) {
          clearInterval(autoPlayInterval);
          autoPlayInterval = null;
        }
      }

      // Navegación con teclado
      function handleKeyNavigation(event) {
        switch (event.key) {
          case "ArrowLeft":
            event.preventDefault();
            previousSlide();
            break;
          case "ArrowRight":
            event.preventDefault();
            nextSlide();
            break;
          case "Escape":
            stopAutoPlay();
            break;
        }
      }

      // Observer para animaciones de entrada
      function observeElements() {
        const observer = new IntersectionObserver(
          (entries) => {
            entries.forEach((entry) => {
              if (entry.isIntersecting) {
                entry.target.style.animationPlayState = "running";
              }
            });
          },
          {
            threshold: 0.1,
          }
        );

        // Observar elementos con animación
        document.querySelectorAll(".icon-box").forEach((el) => {
          observer.observe(el);
        });
      }

      // Funciones de utilidad
      function preloadImages() {
        const imageUrls = [
          "https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=800&h=400&fit=crop",
          "https://images.unsplash.com/photo-1544551763-46a013bb70d5?w=800&h=400&fit=crop",
          "https://images.unsplash.com/photo-1571896349842-33c89424de2d?w=800&h=400&fit=crop",
          "https://images.unsplash.com/photo-1544551763-77ef2d0cfc6c?w=800&h=400&fit=crop",
          "https://images.unsplash.com/photo-1571003123894-1f0594d2b5d9?w=800&h=400&fit=crop",
        ];

        imageUrls.forEach((url) => {
          const img = new Image();
          img.src = url;
        });
      }

      // Precargar imágenes al cargar la página
      document.addEventListener("DOMContentLoaded", preloadImages);

      // Manejo de errores de imágenes
      document.querySelectorAll("img").forEach((img) => {
        img.addEventListener("error", function () {
          this.src =
            "https://via.placeholder.com/800x400/007bff/ffffff?text=Imagen+no+disponible";
        });
      });

      // Optimización para dispositivos táctiles
      let touchStartX = 0;
      let touchEndX = 0;

      const sliderContainer = document.querySelector(".slider-container");

      sliderContainer.addEventListener("touchstart", (e) => {
        touchStartX = e.changedTouches[0].screenX;
      });

      sliderContainer.addEventListener("touchend", (e) => {
        touchEndX = e.changedTouches[0].screenX;
        handleSwipe();
      });

      function handleSwipe() {
        const swipeThreshold = 50;
        const diff = touchStartX - touchEndX;

        if (Math.abs(diff) > swipeThreshold) {
          if (diff > 0) {
            nextSlide(); // Swipe left - next slide
          } else {
            previousSlide(); // Swipe right - previous slide
          }
        }
      }

      // Cleanup al salir de la página
      window.addEventListener("beforeunload", () => {
        stopAutoPlay();
      });