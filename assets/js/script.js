// Estado global de filtros
let activeFilters = {
  types: [],
  destinations: [],
  minPrice: 0,
  minDuration: 1,
}

// Elementos del DOM
const filterButtons = document.querySelectorAll(".filter-group button")
const priceRange = document.getElementById("priceRange")
const durationRange = document.getElementById("durationRange")
const clearButton = document.getElementById("clear-filters")
const applyButton = document.getElementById("apply-filters")
const packagesContainer = document.getElementById("packages-container")
const noResults = document.getElementById("no-results")
const filteredCount = document.getElementById("filtered-count")
const totalCount = document.getElementById("total-count")
const priceMin = document.getElementById("priceMin")
const durationMin = document.getElementById("durationMin")

// Inicialización
document.addEventListener("DOMContentLoaded", () => {
  initializeFilters()
  updateRangeLabels()
  filterPackages()

  // Animación de entrada para las tarjetas
  setTimeout(() => {
    const cards = document.querySelectorAll(".package-card")
    cards.forEach((card, index) => {
      setTimeout(() => {
        card.classList.add("visible")
      }, index * 100)
    })
  }, 100)
})

// Inicializar event listeners
function initializeFilters() {
  // Botones de filtro
  filterButtons.forEach((button) => {
    button.addEventListener("click", function () {
      toggleFilterButton(this)
    })
  })

  // Sliders
  priceRange.addEventListener("input", function () {
    activeFilters.minPrice = Number.parseInt(this.value)
    updateRangeLabels()
    filterPackages()
    checkClearButton()
  })

  durationRange.addEventListener("input", function () {
    activeFilters.minDuration = Number.parseInt(this.value)
    updateRangeLabels()
    filterPackages()
    checkClearButton()
  })

  // Botones de acción
  applyButton.addEventListener("click", filterPackages)
  clearButton.addEventListener("click", clearAllFilters)
}

// Toggle de botones de filtro
function toggleFilterButton(button) {
  const filterGroup = button.closest(".filter-group")
  const filterType = filterGroup.dataset.filter
  const value = button.dataset.value

  if (button.classList.contains("active")) {
    // Deseleccionar
    button.classList.remove("active")
    if (filterType === "type") {
      activeFilters.types = activeFilters.types.filter((t) => t !== value)
    } else if (filterType === "destination") {
      activeFilters.destinations = activeFilters.destinations.filter((d) => d !== value)
    }
  } else {
    // Seleccionar
    button.classList.add("active")
    if (filterType === "type") {
      if (!activeFilters.types.includes(value)) {
        activeFilters.types.push(value)
      }
    } else if (filterType === "destination") {
      if (!activeFilters.destinations.includes(value)) {
        activeFilters.destinations.push(value)
      }
    }
  }

  filterPackages()
  checkClearButton()
}

// Actualizar etiquetas de los sliders
function updateRangeLabels() {
  priceMin.textContent = `$${Number.parseInt(priceRange.value).toLocaleString()}`
  durationMin.textContent = `${durationRange.value} día${durationRange.value !== "1" ? "s" : ""}`
}

// Filtrar paquetes
function filterPackages() {
  const packages = document.querySelectorAll(".package-card")
  let visibleCount = 0

  packages.forEach((packageCard) => {
    const type = packageCard.dataset.type
    const destination = packageCard.dataset.destination
    const price = Number.parseInt(packageCard.dataset.price)
    const duration = Number.parseInt(packageCard.dataset.duration)

    let shouldShow = true

    // Filtro por tipo
    if (activeFilters.types.length > 0 && !activeFilters.types.includes(type)) {
      shouldShow = false
    }

    // Filtro por destino
    if (activeFilters.destinations.length > 0 && !activeFilters.destinations.includes(destination)) {
      shouldShow = false
    }

    // Filtro por precio mínimo
    if (activeFilters.minPrice > 0 && price < activeFilters.minPrice) {
      shouldShow = false
    }

    // Filtro por duración mínima
    if (activeFilters.minDuration > 1 && duration < activeFilters.minDuration) {
      shouldShow = false
    }

    // Mostrar/ocultar paquete
    if (shouldShow) {
      packageCard.style.display = "block"
      visibleCount++
    } else {
      packageCard.style.display = "none"
    }
  })

  // Actualizar contador
  filteredCount.textContent = visibleCount
  totalCount.textContent = packages.length

  // Mostrar/ocultar mensaje de "sin resultados"
  if (visibleCount === 0) {
    packagesContainer.style.display = "none"
    noResults.style.display = "block"
  } else {
    packagesContainer.style.display = "grid"
    noResults.style.display = "none"
  }
}

// Verificar si mostrar botón de limpiar
function checkClearButton() {
  const hasActiveFilters =
    activeFilters.types.length > 0 ||
    activeFilters.destinations.length > 0 ||
    activeFilters.minPrice > 0 ||
    activeFilters.minDuration > 1

  clearButton.style.display = hasActiveFilters ? "block" : "none"
}

// Limpiar todos los filtros
function clearAllFilters() {
  // Resetear estado
  activeFilters = {
    types: [],
    destinations: [],
    minPrice: 0,
    minDuration: 1,
  }

  // Resetear botones
  filterButtons.forEach((button) => {
    button.classList.remove("active")
  })

  // Resetear sliders
  priceRange.value = 0
  durationRange.value = 1

  // Actualizar UI
  updateRangeLabels()
  filterPackages()
  checkClearButton()
}

// Función global para el botón en "sin resultados"
window.clearAllFilters = clearAllFilters

// Animaciones adicionales
function animateCards() {
  const observer = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.classList.add("visible")
        }
      })
    },
    {
      threshold: 0.1,
    },
  )

  document.querySelectorAll(".package-card").forEach((card) => {
    observer.observe(card)
  })
}

// Inicializar animaciones cuando se carga la página
document.addEventListener("DOMContentLoaded", animateCards)
