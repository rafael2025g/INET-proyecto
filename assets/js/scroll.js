/* SISTEMA DE ANIMACIONES DE SCROLL*/

/**
 * 1. Esperar a que el DOM esté completamente cargado
 * 2. Crear un Intersection Observer con threshold del 10%
 * 3. Para cada elemento que entra en viewport:
 *    - Agregar clase 'in-view' para activar animación CSS
 *    - Dejar de observar el elemento (una sola animación)
 * 4. Observar todos los elementos con clase 'animate-on-scroll'
 */

document.addEventListener("DOMContentLoaded", () => {
  console.log(" Sistema de animaciones iniciado")

  // CONFIGURACIÓN DEL OBSERVER
  const observerConfig = {
    threshold: 0.1, // Activar cuando 10% del elemento sea visible
    rootMargin: "0px 0px -50px 0px", // Margen adicional para activación temprana
  }

  // CALLBACK DEL OBSERVER
  const handleIntersection = (entries, observer) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting) {
        console.log(` Animando elemento:`, entry.target.className)

        // ACTIVAR ANIMACIÓN
        entry.target.classList.add("in-view")

        // DEJAR DE OBSERVAR (optimización)
        observer.unobserve(entry.target)
      }
    })
  }

  // CREAR OBSERVER
  const scrollObserver = new IntersectionObserver(handleIntersection, observerConfig)

  // OBSERVAR ELEMENTOS ANIMABLES
  const animatedElements = document.querySelectorAll(".animate-on-scroll")
  animatedElements.forEach((element) => {
    scrollObserver.observe(element)
  })

  console.log(` Observando ${animatedElements.length} elementos para animación`)
})

/* SISTEMA DE CONTADORES ANIMADOS*/

/**
 * 1. Buscar elementos con clase 'count' y atributo 'data-target'
 * 2. Para cada contador:
 *    - Obtener valor objetivo del data-target
 *    - Animar desde 0 hasta el objetivo en 2 segundos
 *    - Usar easing para animación más natural
 *    - Actualizar el texto del elemento en cada frame
 */

function initializeCounters() {
  const counters = document.querySelectorAll(".count[data-target]")

  counters.forEach((counter) => {
    const target = Number.parseInt(counter.getAttribute("data-target"))
    const duration = 2000 // 2 segundos
    let startTime = null

    function animateCounter(currentTime) {
      if (!startTime) startTime = currentTime

      const elapsed = currentTime - startTime
      const progress = Math.min(elapsed / duration, 1)

      // EASING FUNCTION (ease-out-quart)
      const easeOutQuart = 1 - Math.pow(1 - progress, 4)

      // CALCULAR VALOR ACTUAL
      const currentValue = Math.floor(easeOutQuart * target)
      counter.textContent = currentValue

      // CONTINUAR ANIMACIÓN O FINALIZAR
      if (progress < 1) {
        requestAnimationFrame(animateCounter)
      } else {
        counter.textContent = target // Asegurar valor final exacto
        console.log(`Contador completado: ${target}`)
      }
    }

    // INICIAR ANIMACIÓN CUANDO EL ELEMENTO SEA VISIBLE
    const counterObserver = new IntersectionObserver((entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          requestAnimationFrame(animateCounter)
          counterObserver.unobserve(entry.target)
        }
      })
    })

    counterObserver.observe(counter)
  })
}

/* SISTEMA DE PROMOCIONES DINÁMICAS*/

/*
 * 1. Configurar fecha objetivo para countdown (3 días desde ahora)
 * 2. Actualizar countdown cada minuto
 * 3. Mostrar días, horas o minutos según tiempo restante
 * 4. Cambiar color cuando la oferta expire
 */

function initializePromoCountdown() {
  const countdownElement = document.getElementById("countdown")
  if (!countdownElement) return

  // FECHA OBJETIVO (3 días desde ahora)
  const targetDate = new Date()
  targetDate.setDate(targetDate.getDate() + 3)

  function updateCountdown() {
    const now = new Date()
    const timeLeft = targetDate - now

    if (timeLeft > 0) {
      const days = Math.floor(timeLeft / (1000 * 60 * 60 * 24))
      const hours = Math.floor((timeLeft % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60))
      const minutes = Math.floor((timeLeft % (1000 * 60 * 60)) / (1000 * 60))

      // MOSTRAR UNIDAD MÁS RELEVANTE
      if (days > 0) {
        countdownElement.textContent = `${days} día${days !== 1 ? "s" : ""}`
      } else if (hours > 0) {
        countdownElement.textContent = `${hours} hora${hours !== 1 ? "s" : ""}`
      } else {
        countdownElement.textContent = `${minutes} minuto${minutes !== 1 ? "s" : ""}`
      }
    } else {
      // OFERTA EXPIRADA
      countdownElement.textContent = "¡Oferta expirada!"
      countdownElement.style.color = "#ff4757"
      console.log(" Oferta expirada")
    }
  }

  // ACTUALIZAR INMEDIATAMENTE Y LUEGO CADA MINUTO
  updateCountdown()
  setInterval(updateCountdown, 60000)

  console.log(" Countdown de promociones iniciado")
}

/* SISTEMA DE NÚMEROS ANIMADOS EN PROMOCIONES*/

/**
 * 1. Buscar elementos con clase 'animated-number'
 * 2. Obtener valor objetivo del data-target
 * 3. Animar desde 0 hasta el objetivo cuando sea visible
 * 4. Usar animación más rápida para impacto visual
 */

function initializeAnimatedNumbers() {
  const animatedNumbers = document.querySelectorAll(".animated-number[data-target]")

  animatedNumbers.forEach((numberElement) => {
    const target = Number.parseInt(numberElement.getAttribute("data-target"))
    const duration = 1500 // 1.5 segundos para números promocionales

    function animateNumber() {
      let startTime = null

      function updateNumber(currentTime) {
        if (!startTime) startTime = currentTime

        const elapsed = currentTime - startTime
        const progress = Math.min(elapsed / duration, 1)

        // EASING MÁS DRAMÁTICO PARA PROMOCIONES
        const easeOutBack = 1 + 2.7 * Math.pow(progress - 1, 3) + 1.7 * Math.pow(progress - 1, 2)
        const currentValue = Math.floor(Math.min(easeOutBack * target, target))

        numberElement.textContent = currentValue

        if (progress < 1) {
          requestAnimationFrame(updateNumber)
        } else {
          numberElement.textContent = target
          console.log(` Número promocional animado: ${target}%`)
        }
      }

      requestAnimationFrame(updateNumber)
    }

    // OBSERVAR PARA ACTIVAR ANIMACIÓN
    const numberObserver = new IntersectionObserver((entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          animateNumber()
          numberObserver.unobserve(entry.target)
        }
      })
    })

    numberObserver.observe(numberElement)
  })
}

/* INICIALIZACIÓN PRINCIPAL*/

/**
 * PSEUDOCÓDIGO:
 * 1. Esperar a que el DOM esté listo
 * 2. Inicializar sistemas en orden de prioridad:
 *    - Animaciones de scroll (base)
 *    - Contadores de estadísticas
 *    - Countdown de promociones
 *    - Números animados promocionales
 * 3. Registrar estado de inicialización
 */

document.addEventListener("DOMContentLoaded", () => {
  console.log(" Inicializando TravelWorld...")

  // INICIALIZAR SISTEMAS
  try {
    initializeCounters()
    console.log(" Contadores inicializados")

    initializePromoCountdown()
    console.log(" Countdown de promociones inicializado")

    initializeAnimatedNumbers()
    console.log(" Números animados inicializados")

    console.log(" TravelWorld completamente inicializado")
  } catch (error) {
    console.error(" Error durante la inicialización:", error)
  }
})

/* UTILIDADES DE RENDIMIENTO*/

/*FUNCIÓN THROTTLE*/
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

/*FUNCIÓN DEBOUNCE*/
function debounce(func, wait) {
  let timeout
  return function () {
    
    const args = arguments

    clearTimeout(timeout)
    timeout = setTimeout(() => func.apply(this, args), wait)
  }
}

/* MANEJO DE ERRORES Y LOGGING*/

/*SISTEMA DE LOGGING*/
const Logger = {
  info: (message, data = null) => {
    console.log(` [INFO] ${message}`, data || "")
  },

  success: (message, data = null) => {
    console.log(` [SUCCESS] ${message}`, data || "")
  },

  warning: (message, data = null) => {
    console.warn(` [WARNING] ${message}`, data || "")
  },

  error: (message, error = null) => {
    console.error(`[ERROR] ${message}`, error || "")
  },
}

// MANEJO GLOBAL DE ERRORES
window.addEventListener("error", (event) => {
  Logger.error("Error global capturado:", {
    message: event.message,
    filename: event.filename,
    line: event.lineno,
    column: event.colno,
  })
})