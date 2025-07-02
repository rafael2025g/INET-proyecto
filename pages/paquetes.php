<?php
include('../model/modelos.php');
$paquetes = obtenerPaquetes();
?>
<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Paquetes - TravelWorld</title>
    <link rel="stylesheet" href="../assets/css/paquetes/paquetes.css" />
    <link rel="stylesheet" href="../assets/css/historial/historial-button.css">
  </head>
  <body class="loaded">
    <!-- SECCIÓN 1: NAVEGACIÓN PRINCIPAL-->
    <header>
      <nav class="navbar">
        <!-- LOGO DE LA EMPRESA -->
        <div class="logo">TravelWorld</div>

        <!-- BOTÓN HAMBURGUESA PARA MÓVIL -->
        <button
          class="mobile-menu-toggle"
          id="mobileMenuToggle"
          aria-label="Abrir menú"
        >
          <span class="hamburger-line"></span>
          <span class="hamburger-line"></span>
          <span class="hamburger-line"></span>
        </button>

        <!-- MENÚ DE NAVEGACIÓN CON OVERLAY MÓVIL -->
        <div class="nav-menu" id="navMenu">
          <div class="nav-overlay" id="navOverlay"></div>
          
          <div class="nav-content">
            <div class="mobile-menu-header">
              <div class="logo">TravelWorld</div>
              <button
                class="mobile-menu-close"
                id="mobileMenuClose"
                aria-label="Cerrar menú"
              >
                <svg
                  width="24"
                  height="24"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2"
                >
                  <line x1="18" y1="6" x2="6" y2="18"></line>
                  <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
              </button>
            </div>

            <ul class="nav-links">
              <li><a href="../index.php" class="nav-link">inicio</a></li>
              <li><a href="../index.php#promoSection" class="nav-link">ofertas</a></li>
              <li><a href="./paquetes.php" class="nav-link active">paquetes</a></li>
              <li><a href="../index.php#servicios" class="nav-link">servicios</a></li>
              <li><a href="../index.php#contacto" class="nav-link">contacto</a></li>
            </ul>

            <div class="nav-buttons">
              <?php if(isset($_SESSION['usuario'])): ?>
                
                <span class='nav-user'><img src="../assets/images/user.png" alt=""></span>
                
                <?php if($_SESSION['rol'] === 'cliente'): ?>
                  <!-- Opciones para clientes -->
                  <button class="btn-cart">
                    <a href="cart.php"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-shopping-cart-icon lucide-shopping-cart"><circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/></svg><?php echo isset($_SESSION['carrito']) ? count($_SESSION['carrito']) : 0; ?>+</a>
                  </button>
                <?php elseif($_SESSION['rol'] === 'admin'): ?>
                  <!-- Opciones para administradores -->
                  <button><a href="./admin-panel.php">Agregar Producto</a>  </button>
                <?php endif; ?>
                
                <button class="btn-login">
                  <a href="./logout.php">Cerrar Sesión</a>
                </button>
              <?php else: ?>
                <button class="btn-login">
                  <a href="./login.php">Iniciar Sesion</a>
                </button>
                <button class="btn-register">
                  <a href="./register.php">Registrarse</a>
                </button>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </nav>
    </header>

    <main class="container">
      <div class="filters">
        <h2>Filtros</h2>
        <div class="filter-categories">
          <div class="filter-category">
            <h3>Tipos de viajes</h3>
            <div class="filter-group" data-filter="type">
              <button data-value="Individual">Individual</button>
              <button data-value="Familiar">Familiar</button>
              <button data-value="Grupal">Grupal</button>
            </div>
          </div>

          <div class="filter-category">
            <h3>Destino</h3>
            <div class="filter-group" data-filter="destination">
              <button data-value="Nacional">Nacional</button>
              <button data-value="Internacional">Internacional</button>
            </div>
          </div>
        </div>

        <div class="filter-group">
          <h3>Precio</h3>
          <input
            type="range"
            min="0"
            max="200000"
            step="5000"
            id="priceRange"
            value="0"
          />
          <div class="range-container">
            <div class="range-labels" id="priceMin">$0</div>
            <div class="range-labels">$200,000</div>
          </div>
        </div>

        <div class="filter-group">
          <h3>Duración</h3>
          <input
            type="range"
            min="1"
            max="15"
            step="1"
            id="durationRange"
            value="1"
          />
          <div class="range-container">
            <div class="range-labels" id="durationMin">1 día</div>
            <div class="range-labels">15 días</div>
          </div>
        </div>

        <div class="filter-buttons">
          <button class="btn-filter" id="apply-filters">Aplicar Filtros</button>
          <button class="btn-clear" id="clear-filters" style="display: none">
            Limpiar Filtros
          </button>
        </div>
      </div>

      <!-- SECTION: PAQUETES TURÍSTICOS DESDE BASE DE DATOS -->
      <section class="packages" id="packages-container">
        
        <?php if($paquetes && mysqli_num_rows($paquetes) > 0): ?>
          <?php while($paquete = mysqli_fetch_assoc($paquetes)): ?>
            <div
              class="package-card"
              data-type="Familiar"
              data-destination="Internacional"
              data-price="<?php echo $paquete['precio']; ?>"
              data-duration="<?php echo $paquete['dias']; ?>"
            >
              <?php if($paquete['descuento'] > 0): ?>
                <div class="discount-badge"><?php echo $paquete['descuento']; ?>% OFF</div>
              <?php endif; ?>
              
              <?php if($paquete['imagen']): ?>
                <img
                  src="data:image/jpeg;base64,<?php echo base64_encode($paquete['imagen']); ?>"
                  class="package-img"
                  alt="<?php echo htmlspecialchars($paquete['titulo']); ?>"
                />
              <?php else: ?>
                <img
                  src="https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=300&h=200&fit=crop"
                  class="package-img"
                  alt="<?php echo htmlspecialchars($paquete['titulo']); ?>"
                />
              <?php endif; ?>
              
              <div class="card-content">
                <h3 class="ellipsis"><?php echo htmlspecialchars($paquete['titulo']); ?></h3>
                <p class="ellipsis"><?php echo htmlspecialchars($paquete['descripcion']); ?></p>
                <div class="price-duration">
                  <div class="duration"><?php echo $paquete['dias']; ?> días</div>
                  <div class="price-row">
                    <span class="new-price">$<?php echo number_format($paquete['precio']); ?></span>
                    <?php if($paquete['descuento'] > 0): ?>
                      <?php $precio_original = $paquete['precio'] / (1 - $paquete['descuento']/100); ?>
                      <span class="old-price">$<?php echo number_format($precio_original); ?></span>
                    <?php endif; ?>
                  </div>
                </div>
                <div class="btn-row">
                  <button class="btn btn-secondary">
                    <a href="paquete-detail.php?id=<?php echo $paquete['ID_Producto']; ?>">Ver detalles</a>
                  </button>
                  
                  <?php if(!esAdmin()): ?>
                    <!-- Botón de agregar solo para no-admins -->
                    <?php if(puedeComprar()): ?>
                      <!-- Usuario logueado como cliente -->
                      <form method="POST" style="display: inline;">
                        <input type="hidden" name="id" value="<?php echo openssl_encrypt($paquete['ID_Producto'], COD, KEY); ?>">
                        <input type="hidden" name="titulo" value="<?php echo openssl_encrypt($paquete['titulo'], COD, KEY); ?>">
                        <input type="hidden" name="precio" value="<?php echo openssl_encrypt($paquete['precio'], COD, KEY); ?>">
                        <input type="hidden" name="cantidad" value="<?php echo openssl_encrypt(1, COD, KEY); ?>">
                        <input type="hidden" name="btnAccion" value="agregar">
                        <button type="submit" class="btn btn-primary">Agregar</button>
                      </form>
                    <?php else: ?>
                      <!-- Usuario no logueado - redirigir al login -->
                      <button class="btn btn-primary" onclick="window.location.href='login.php'">
                        Iniciar Sesión
                      </button>
                    <?php endif; ?>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          <?php endwhile; ?>
        <?php else: ?>
          <!-- Mensaje si no hay paquetes -->
          <div style="text-align: center; padding: 2rem; grid-column: 1/-1;">
            <h3>No hay paquetes disponibles</h3>
            <p>Vuelve pronto para ver nuestras ofertas.</p>
          </div>
        <?php endif; ?>
      </section>

      <div class="no-results" id="no-results" style="display: none">
        <h3>No se encontraron paquetes</h3>
        <p>No hay paquetes que coincidan con los filtros seleccionados.</p>
      </div>
    </main>

    <footer class="footer">
      <div class="footer-container">
        <div class="footer-brand">
          <h3>TravelWorld</h3>
          <p>Empresa líder en venta de paquetes turísticos nacionales e internacionales.</p>
        </div>
        <div class="footer-links">
          <h4>Enlaces</h4>
          <a href="#">Inicio</a>
          <a href="#">Ofertas</a>
          <a href="#">Paquetes</a>
          <a href="#">Servicios</a>
          <a href="#">Contacto</a>
        </div>
        <div class="footer-contact">
          <h4>Contacto</h4>
          <ul class="contact-content">
            <li class="contact-item">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"/>
                <circle cx="12" cy="10" r="3"/>
              </svg>
              Av. Principal 123, Ciudad
            </li>
            <li class="contact-item">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M13.832 16.568a1 1 0 0 0 1.213-.303l.355-.465A2 2 0 0 1 17 15h3a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2A18 18 0 0 1 2 4a2 2 0 0 1 2-2h3a2 2 0 0 1 2 2v3a2 2 0 0 1-.8 1.6l-.468.351a1 1 0 0 0-.292 1.233 14 14 0 0 0 6.392 6.384"/>
              </svg>
              +54 9 11 1234 5678
            </li>
            <li class="contact-item">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="m22 7-8.991 5.727a2 2 0 0 1-2.009 0L2 7"/>
                <rect x="2" y="4" width="20" height="16" rx="2"/>
              </svg>
              info@travelworld.com
            </li>
          </ul>
        </div>
      </div>
      <div class="footer-detail">
        <p>&copy; 2025 TravelWorld. Todos los derechos reservados.</p>
      </div>
    </footer>

    <!-- Botón flotante para historial -->
    <?php if(isset($_SESSION['usuario'])): ?>
    <a href="historial.php" class="historial-float-btn">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
            <polyline points="14,2 14,8 20,8"/>
            <line x1="16" y1="13" x2="8" y2="13"/>
            <line x1="16" y1="17" x2="8" y2="17"/>
            <polyline points="10,9 9,9 8,9"/>
        </svg>
        <?php echo $_SESSION['rol'] === 'admin' ? 'Historial' : 'Mi Historial'; ?>
    </a>
    <?php endif; ?>

    <script src="../assets/js/script.js"></script>
    <script src="../assets/js/mobile-menu.js"></script>
  </body>
</html>
