<?php
include('../model/modelos.php');

$id = isset($_GET['id']) ? $_GET['id'] : 1;
$paquete = obtenerPaquetePorId($id);

if (!$paquete) {
    header("Location: paquetes.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="../assets/css/paquetes/paquetes-detail.css" />
    <title><?php echo htmlspecialchars($paquete['titulo']); ?> - TravelWorld</title>
  </head>
  <body>
    <div class="loading-overlay" id="loadingOverlay">
      <div class="loading-spinner">
        <div class="spinner"></div>
        <p>Cargando detalles del paquete...</p>
      </div>
    </div>

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
                   
                <?php endif; ?>
                
                <button class="btn-login">
                  <a href="./pages/logout.php">Cerrar Sesión</a>
                </button>
              <?php else: ?>
                <button class="btn-login">
                  <a href="./pages/login.php">Iniciar Sesion</a>
                </button>
                <button class="btn-register">
                  <a href="./pages/register.php">Registrarse</a>
                </button>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </nav>
    </header>
    <section class="package-detail" id="packageDetail">
      <div class="package-header">
        <?php if($paquete['descuento'] > 0): ?>
          <span class="discount"><?php echo $paquete['descuento']; ?>% OFF</span>
        <?php endif; ?>

        <div class="slider-container">
          <div class="slider" id="imageSlider">
            <button class="slider-arrow prev-arrow" id="prevBtn">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="15,18 9,12 15,6"></polyline>
              </svg>
            </button>

            <div class="slider-track" id="sliderTrack">
              <?php if($paquete['imagen']): ?>
                <img
                  src="data:image/jpeg;base64,<?php echo base64_encode($paquete['imagen']); ?>"
                  alt="<?php echo htmlspecialchars($paquete['titulo']); ?>"
                  class="slider-image active"
                  data-index="0"
                />
              <?php else: ?>
                <img
                  src="https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=800&h=400&fit=crop"
                  alt="<?php echo htmlspecialchars($paquete['titulo']); ?>"
                  class="slider-image active"
                  data-index="0"
                />
              <?php endif; ?>
              
              <!-- Imágenes adicionales de ejemplo -->
              <?php if($paquete['img2']): ?>
                <img
                  src="data:image/jpeg;base64,<?php echo base64_encode($paquete['img2']); ?>"
                  alt="<?php echo htmlspecialchars($paquete['titulo']); ?>"
                  class="slider-image"
                  data-index="1"
                />
              <?php else: ?>
              <img
                src="https://images.unsplash.com/photo-1544551763-46a013bb70d5?w=800&h=400&fit=crop"
                alt="Vista 2"
                class="slider-image"
                data-index="1"
              />
              <?php endif; ?>
              <?php if($paquete['img3']): ?>
                <img
                  src="data:image/jpeg;base64,<?php echo base64_encode($paquete['img3']); ?>"
                  alt="<?php echo htmlspecialchars($paquete['titulo']); ?>"
                  class="slider-image"
                  data-index="2"
                />
              <?php else: ?>
              <img
                src="https://images.unsplash.com/photo-1571896349842-33c89424de2d?w=800&h=400&fit=crop"
                alt="Vista 3"
                class="slider-image"
                data-index="2"
              />
              <?php endif; ?>
              <?php if($paquete['img4']): ?>
                <img
                  src="data:image/jpeg;base64,<?php echo base64_encode($paquete['img4']); ?>"
                  alt="<?php echo htmlspecialchars($paquete['titulo']); ?>"
                  class="slider-image"
                  data-index="3"
                />
              <?php else: ?>
              <img
                src="https://images.unsplash.com/photo-1571896349842-33c89424de2d?w=800&h=400&fit=crop"
                alt="Vista 3"
                class="slider-image"
                data-index="3"
              />
              <?php endif; ?>
              <?php if($paquete['img5']): ?>
                <img
                  src="data:image/jpeg;base64,<?php echo base64_encode($paquete['img5']); ?>"
                  alt="<?php echo htmlspecialchars($paquete['titulo']); ?>"
                  class="slider-image"
                  data-index="4"
                />
              <?php else: ?>
              <img
                src="https://images.unsplash.com/photo-1571896349842-33c89424de2d?w=800&h=400&fit=crop"
                alt="Vista 4"
                class="slider-image"
                data-index="4"
              />
              <?php endif; ?>
            </div>

            <button class="slider-arrow next-arrow" id="nextBtn">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="9,18 15,12 9,6"></polyline>
              </svg>
            </button>

            <div class="slider-indicator" id="sliderIndicator">
              <span id="currentSlide">1</span>/<span id="totalSlides">5</span>
            </div>
          </div>

          <div class="thumbnails" id="thumbnails">
            <div class="thumb active" data-index="0">
              <?php if($paquete['imagen']): ?>
                <img src="data:image/jpeg;base64,<?php echo base64_encode($paquete['imagen']); ?>" alt="Thumbnail 1" />
              <?php else: ?>
                <img src="https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=100&h=60&fit=crop" alt="Thumbnail 1" />
              <?php endif; ?>
            </div>
            <div class="thumb" data-index="1">
              <?php if($paquete['img2']): ?>
                <img src="data:image/jpeg;base64,<?php echo base64_encode($paquete['img2']); ?>" alt="Thumbnail 2" />
              <?php else: ?>
                <img src="https://images.unsplash.com/photo-1544551763-46a013bb70d5?w=100&h=60&fit=crop" alt="Thumbnail 2" />
              <?php endif; ?>
            </div>
            <div class="thumb" data-index="2">
              <?php if($paquete['img3']): ?>
                <img src="data:image/jpeg;base64,<?php echo base64_encode($paquete['img3']); ?>" alt="Thumbnail 3" />
              <?php else: ?>
                <img src="https://images.unsplash.com/photo-1571896349842-33c89424de2d?w=100&h=60&fit=crop" alt="Thumbnail 3" />
              <?php endif; ?>
            </div>
            <div class="thumb" data-index="3">
              <?php if($paquete['img4']): ?>
                <img src="data:image/jpeg;base64,<?php echo base64_encode($paquete['img4']); ?>" alt="Thumbnail 4" />
              <?php else: ?>
                <img src="https://images.unsplash.com/photo-1571896349842-33c89424de2d?w=100&h=60&fit=crop" alt="Thumbnail 4" />
              <?php endif; ?>
            </div>
            <div class="thumb" data-index="4">
              <?php if($paquete['img5']): ?>
                <img src="data:image/jpeg;base64,<?php echo base64_encode($paquete['img5']); ?>" alt="Thumbnail 5" />
              <?php else: ?>
                <img src="https://images.unsplash.com/photo-1571896349842-33c89424de2d?w=100&h=60&fit=crop" alt="Thumbnail 5" />
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>

      <div class="package-body">
        <div class="package-title">
          <h2><?php echo htmlspecialchars($paquete['titulo']); ?></h2>
          
          <div style="display: flex; gap: 1rem;">
            <?php if(esAdmin()): ?>
              <!-- Botones para administradores -->
              <button class="btn-agregar" onclick="window.location.href='edit-product.php?id=<?php echo $paquete['ID_Producto']; ?>'">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                  <path d="m18.5 2.5 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                </svg>
                Editar
              </button>
              
              <form method="POST" style="display: inline;" onsubmit="return confirm('¿Estás seguro de eliminar este paquete?')">
                <input type="hidden" name="id" value="<?php echo $paquete['ID_Producto']; ?>">
                <input type="hidden" name="eliminar" value="1">
                <button type="submit" class="btn-eliminar">
                  <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="3,6 5,6 21,6"/>
                    <path d="m19,6v14a2,2 0 0,1 -2,2H7a2,2 0 0,1 -2,-2V6m3,0V4a2,2 0 0,1 2,-2h4a2,2 0 0,1 2,2v2"/>
                  </svg>
                  Eliminar
                </button>
              </form>
            <?php elseif(puedeComprar()): ?>
              <!-- Botón para clientes -->
              <form method="POST" style="display: inline;">
                <input type="hidden" name="id" value="<?php echo openssl_encrypt($paquete['ID_Producto'], COD, KEY); ?>">
                <input type="hidden" name="titulo" value="<?php echo openssl_encrypt($paquete['titulo'], COD, KEY); ?>">
                <input type="hidden" name="precio" value="<?php echo openssl_encrypt($paquete['precio'], COD, KEY); ?>">
                <input type="hidden" name="cantidad" value="<?php echo openssl_encrypt(1, COD, KEY); ?>">
                <input type="hidden" name="btnAccion" value="agregar">
                <button type="submit" class="btn-agregar" id="addToCartBtn">
                  <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="9" cy="21" r="1"></circle>
                    <circle cx="20" cy="21" r="1"></circle>
                    <path d="m1 1 4 4 2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                  </svg>
                  Agregar al Carrito
                </button>
              </form>
            <?php elseif(!isset($_SESSION['usuario'])): ?>
              <!-- Botón para usuarios no logueados -->
              <button class="btn-agregar" onclick="window.location.href='login.php'">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/>
                  <polyline points="10,17 15,12 10,7"/>
                  <line x1="15" y1="12" x2="3" y2="12"/>
                </svg>
                Iniciar Sesión para Comprar
              </button>
            <?php endif; ?>
          </div>
        </div>
        
        <div class="package-top">
          <div class="description-section">
            <div class="description-content">
              <h3>Descripción del Viaje</h3>
              <p><?php echo htmlspecialchars($paquete['descripcion']); ?></p>
            </div>
            <div class="price-box">
              <strong>$<?php echo number_format($paquete['precio']); ?></strong>
              <?php if($paquete['descuento'] > 0): ?>
                <?php $precio_original = $paquete['precio'] / (1 - $paquete['descuento']/100); ?>
                <span class="original-price">$<?php echo number_format($precio_original); ?></span>
              <?php endif; ?>
            </div>
          </div>
        </div>

        <div class="package-icons">
          <div class="icon-box">
            <div class="icon one">
              <svg width="32" height="32" viewBox="0 0 24 24" style="color: #4180f3;" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"></circle>
                <polyline points="12,6 12,12 16,14"></polyline>
              </svg>
            </div>
            <div>
              <strong><?php echo $paquete['dias']; ?> días</strong><br />
              <span>Duración</span>
            </div>
          </div>
          
          <div class="icon-box">
            <div class="icon two">
              <svg width="32" height="32" viewBox="0 0 24 24" style="color: #41f37c;" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"/>
                <circle cx="12" cy="10" r="3"/>
              </svg>
            </div>
            <div>
              <strong><?php echo htmlspecialchars($paquete['lugar']); ?></strong><br />
              <span>Destino</span>
            </div>
          </div>
          
          <div class="icon-box">
            <div class="icon three">
              <svg width="32" height="32" viewBox="0 0 24 24" fill="none" style="color: #de41f3;" stroke="currentColor" stroke-width="2">
                <path d="M16 20V4a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>
                <rect width="20" height="14" x="2" y="6" rx="2"/>
              </svg>
            </div>
            <div>
              <strong><?php echo $paquete['stock']; ?> disponibles</strong><br />
              <span>Stock</span>
            </div>
          </div>
          
          <div class="icon-box">
            <div class="icon four">
              <svg width="32" height="32" viewBox="0 0 24 24" style="color: #f3a641;" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M9.937 15.5A2 2 0 0 0 8.5 14.063l-6.135-1.582a.5.5 0 0 1 0-.962L8.5 9.936A2 2 0 0 0 9.937 8.5l1.582-6.135a.5.5 0 0 1 .963 0L14.063 8.5A2 2 0 0 0 15.5 9.937l6.135 1.581a.5.5 0 0 1 0 .964L15.5 14.063a2 2 0 0 0-1.437 1.437l-1.582 6.135a.5.5 0 0 1-.963 0z"/>
              </svg>
            </div>
            <div>
              <strong>Premium</strong><br />
              <span>Calidad</span>
            </div>
          </div>
        </div>
      </div>
    </section>

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
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"/>
                <circle cx="12" cy="10" r="3"/>
              </svg>
              Av. Principal 123, Ciudad
            </li>
            <li class="contact-item">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M13.832 16.568a1 1 0 0 0 1.213-.303l.355-.465A2 2 0 0 1 17 15h3a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2A18 18 0 0 1 2 4a2 2 0 0 1 2-2h3a2 2 0 0 1 2 2v3a2 2 0 0 1-.8 1.6l-.468.351a1 1 0 0 0-.292 1.233 14 14 0 0 0 6.392 6.384"/>
              </svg>
              +54 9 11 1234 5678
            </li>
            <li class="contact-item">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
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

    <script src="../assets/js/slider.js"></script>
    <script src="../assets/js/mobile-menu.js"></script>
  </body>
</html>