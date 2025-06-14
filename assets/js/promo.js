// Configuración de animaciones
const animationConfig = {
  threshold: 0.1,
  rootMargin: "0px 0px -50px 0px",
}

// Intersection Observer para animaciones de scroll
const observerCallback = (entries, observer) => {
  entries.forEach((entry) => {
    if (entry.isIntersecting) {
      const element = entry.target
      const delay = element.dataset.delay || 0

      setTimeout(() => {
        element.classList.add("animate")

        // Animar números si tiene la clase animated-number
        if (element.classList.contains("animated-number")) {
          animateNumber(element)
        }

        // También buscar números animados dentro del elemento
        const numbersInside = element.querySelectorAll(".animated-number")
        numbersInside.forEach((numberEl) => {
          setTimeout(() => {
            animateNumber(numberEl)
          }, 500) // Pequeño delay adicional para números internos
        })
      }, Number.parseInt(delay))

      observer.unobserve(element)
    }
  })
}

const scrollObserver = new IntersectionObserver(observerCallback, animationConfig)

// Inicialización
document.addEventListener("DOMContentLoaded", () => {
  initializeScrollAnimations()
  initializeParallaxEffect()
  initializeCountdown()
  initializeFloatingElements()
  initializeInteractiveEffects()
})

// Inicializar animaciones de scroll
function initializeScrollAnimations() {
  const animatedElements = document.querySelectorAll(".scroll-animate")

  animatedElements.forEach((element) => {
    scrollObserver.observe(element)
  })
}

// Efecto parallax sutil
function initializeParallaxEffect() {
  const promoSection = document.getElementById("promoSection")

  window.addEventListener("scroll", () => {
    const scrolled = window.pageYOffset
    const parallaxElements = document.querySelectorAll(".floating-icon")

    parallaxElements.forEach((element) => {
      const speed = element.dataset.speed || 0.5
      const yPos = -(scrolled * speed)
      element.style.transform = `translateY(${yPos}px)`
    })

    // Efecto de desvanecimiento en el banner
    const banner = document.querySelector(".promo-banner-outer")
    if (banner) {
      const opacity = Math.max(0, 1 - scrolled / 300)
      banner.style.opacity = opacity
    }
  })
}

// Animación de números mejorada
function animateNumber(element) {
  const target = Number.parseInt(element.dataset.target)
  const duration = 2000
  const startTime = performance.now()

  function updateNumber(currentTime) {
    const elapsed = currentTime - startTime
    const progress = Math.min(elapsed / duration, 1)

    // Usar easing para una animación más suave
    const easeOutQuart = 1 - Math.pow(1 - progress, 4)
    const current = Math.floor(easeOutQuart * target)

    element.textContent = current

    if (progress < 1) {
      requestAnimationFrame(updateNumber)
    } else {
      element.textContent = target // Asegurar el valor final
    }
  }

  requestAnimationFrame(updateNumber)
}

// Countdown dinámico
function initializeCountdown() {
  const countdownElement = document.getElementById("countdown")
  if (!countdownElement) return

  // Fecha objetivo (3 días desde ahora)
  const targetDate = new Date()
  targetDate.setDate(targetDate.getDate() + 3)

  function updateCountdown() {
    const now = new Date()
    const difference = targetDate - now

    if (difference > 0) {
      const days = Math.floor(difference / (1000 * 60 * 60 * 24))
      const hours = Math.floor((difference % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60))
      const minutes = Math.floor((difference % (1000 * 60 * 60)) / (1000 * 60))

      if (days > 0) {
        countdownElement.textContent = `${days} día${days !== 1 ? "s" : ""}`
      } else if (hours > 0) {
        countdownElement.textContent = `${hours} hora${hours !== 1 ? "s" : ""}`
      } else {
        countdownElement.textContent = `${minutes} minuto${minutes !== 1 ? "s" : ""}`
      }
    } else {
      countdownElement.textContent = "¡Oferta expirada!"
      countdownElement.style.color = "#ff4757"
    }
  }

  updateCountdown()
  setInterval(updateCountdown, 60000) // Actualizar cada minuto
}

// Elementos flotantes animados
function initializeFloatingElements() {
  const floatingElements = document.querySelectorAll(".floating-icon")

  floatingElements.forEach((element, index) => {
    // Posición aleatoria inicial
    const randomX = Math.random() * 100
    const randomY = Math.random() * 100

    element.style.left = `${randomX}%`
    element.style.top = `${randomY}%`

    // Animación continua
    setInterval(
      () => {
        const newX = Math.random() * 100
        const newY = Math.random() * 100

        element.style.transition = "all 10s ease-in-out"
        element.style.left = `${newX}%`
        element.style.top = `${newY}%`
      },
      10000 + index * 2000,
    )
  })
}

// Efectos interactivos adicionales
function initializeInteractiveEffects() {
  // Efecto de ondas en botones
  const buttons = document.querySelectorAll("button")

  buttons.forEach((button) => {
    button.addEventListener("click", function (e) {
      const ripple = document.createElement("span")
      const rect = this.getBoundingClientRect()
      const size = Math.max(rect.width, rect.height)
      const x = e.clientX - rect.left - size / 2
      const y = e.clientY - rect.top - size / 2

      ripple.style.width = ripple.style.height = size + "px"
      ripple.style.left = x + "px"
      ripple.style.top = y + "px"
      ripple.classList.add("ripple")

      this.appendChild(ripple)

      setTimeout(() => {
        ripple.remove()
      }, 600)
    })
  })

  // Efecto de inclinación en tarjetas
  const cards = document.querySelectorAll(".promo-main, .promo-small")

  cards.forEach((card) => {
    card.addEventListener("mousemove", function (e) {
      const rect = this.getBoundingClientRect()
      const x = e.clientX - rect.left
      const y = e.clientY - rect.top

      const centerX = rect.width / 2
      const centerY = rect.height / 2

      const rotateX = (y - centerY) / 10
      const rotateY = (centerX - x) / 10

      this.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) translateZ(10px)`
    })

    card.addEventListener("mouseleave", function () {
      this.style.transform = "perspective(1000px) rotateX(0) rotateY(0) translateZ(0)"
    })
  })
}

// Optimización de rendimiento
function throttle(func, limit) {
  let inThrottle
  return function () {
    const args = arguments

    if (!inThrottle) {
      func.apply(this, args)
      inThrottle = true
      setTimeout(() => (inThrottle = false), limit)
    }
  }
}

// Aplicar throttling al scroll
window.addEventListener(
  "scroll",
  throttle(() => {
    // Lógica de scroll optimizada
  }, 16),
)

// Preloader para mejor experiencia
window.addEventListener("load", () => {
  document.body.classList.add("loaded")

  // Iniciar animaciones después de la carga
  setTimeout(() => {
    const promoSection = document.getElementById("promoSection")
    if (promoSection) {
      promoSection.classList.add("section-loaded")
    }
  }, 100)
})

// Cleanup al salir de la página
window.addEventListener("beforeunload", () => {
  scrollObserver.disconnect()
})

// CSS adicional para efectos
const additionalStyles = `
  .ripple {
    position: absolute;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.6);
    transform: scale(0);
    animation: ripple-animation 0.6s linear;
    pointer-events: none;
  }
  
  @keyframes ripple-animation {
    to {
      transform: scale(4);
      opacity: 0;
    }
  }
  
  .section-loaded .floating-icon {
    opacity: 1;
    animation-play-state: running;
  }
  
  .loaded {
    overflow-x: hidden;
  }
`

// Inyectar estilos adicionales
const styleSheet = document.createElement("style")
styleSheet.textContent = additionalStyles
document.head.appendChild(styleSheet)
