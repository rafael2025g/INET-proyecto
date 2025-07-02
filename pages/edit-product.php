<?php
include('../model/modelos.php');

// Verificar si el usuario es admin
if(!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$id = isset($_GET['id']) ? $_GET['id'] : 0;
$paquete = obtenerPaquetePorId($id);

if (!$paquete) {
    header("Location: paquetes.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Editar Paquete - TravelWorld</title>
  <link rel="stylesheet" href="../assets/css/admin/paquetes-edit.css">
</head>
<body>
<main class="panel-container">
    <div class="panel-header"><a href="../pages/paquetes.php" class="nav-link">← Volver al inicio</a></div>
    <section class="panel-grid">
      <div class="images-card">
        <label class="upload-label">Imágenes del paquete:</label>
        <div class="image-upload-wrapper">
          <!-- Slots para 5 imágenes -->
          <div class="image-slot-soft">
            <img src="data:image/jpeg;base64,<?php echo base64_encode($paquete['imagen']); ?>" alt="Imagen actual" class="current-image">
            <div class="preview-soft no-image">+</div>
            <input type="file" name="img" accept="image/*" onchange="previewImageSoft(this, 0)">
          </div>
          <div class="image-slot-soft">
            <img src="data:image/jpeg;base64,<?php echo base64_encode($paquete['img2']); ?>" alt="Imagen 2 actual" class="current-image">
            <div class="preview-soft no-image">+</div>
            <input type="file" name="img2" accept="image/*" onchange="previewImageSoft(this, 1)">
          </div>

          <div class="image-slot-soft">
            <img src="data:image/jpeg;base64,<?php echo base64_encode($paquete['img3']); ?>" alt="Imagen 3 actual" class="current-image">
            <input type="file" name="img3" accept="image/*" required onchange="previewImageSoft(this, 2)">
            <div class="preview-soft" id="previewSoft2">+</div>
          </div>
          <div class="image-slot-soft">
            <img src="data:image/jpeg;base64,<?php echo base64_encode($paquete['img4']); ?>" alt="Imagen 4 actual" class="current-image">
            <input type="file" name="img4" accept="image/*" required onchange="previewImageSoft(this, 3)">
            <div class="preview-soft" id="previewSoft3">+</div>
          </div>
          <div class="image-slot-soft">
            <img src="data:image/jpeg;base64,<?php echo base64_encode($paquete['img5']); ?>" alt="Imagen 5 actual" class="current-image">
            <input type="file" name="img5" accept="image/*" required onchange="previewImageSoft(this, 4)">
            <div class="preview-soft" id="previewSoft4">+</div>
          </div>
        </div>
      </div>
      <form method="POST" enctype="multipart/form-data" class="edit-form" action='../model/modelos.php'>
          <div class="input-container">
            <label for="titulo">Título del Paquete</label>
            <div class="input-group">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-captions-icon lucide-captions"><rect width="18" height="14" x="3" y="5" rx="2" ry="2"/><path d="M7 15h4M15 15h2M7 11h2M13 11h4"/></svg>
              <input type="text" id="titulo" name="titulo" value="<?php echo htmlspecialchars($paquete['titulo']); ?>" required>
            </div>
          </div>
          <div class="input-container">
            <label for="descripcion">Descripción</label>
            <div class="input-group">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-message-square-text-icon lucide-message-square-text"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/><path d="M13 8H7"/><path d="M17 12H7"/></svg>
              <textarea id="descripcion" name="descripcion" rows="4" required><?php echo htmlspecialchars($paquete['descripcion']); ?></textarea>
            </div>
          </div>

          <div class="input-container">
            <label for="lugar">Lugar / Destino</label>
            <div class="input-group">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-map-pin-icon lucide-map-pin"><path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"/><circle cx="12" cy="10" r="3"/></svg>
              <input type="text" id="lugar" name="lugar" value="<?php echo htmlspecialchars($paquete['lugar']); ?>" required>
            </div>
          </div>

          <div class="input-merge">
            <div class="input-container">
              <label for="precio">Precio</label>
              <div class="input-group">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-dollar-sign-icon lucide-circle-dollar-sign"><circle cx="12" cy="12" r="10"/><path d="M16 8h-6a2 2 0 1 0 0 4h4a2 2 0 1 1 0 4H8"/><path d="M12 18V6"/></svg>
                <input type="number" id="precio" name="precio" value="<?php echo $paquete['precio']; ?>" required>
              </div>
            </div>
            <div class="input-container">
              <label for="dias">Duración (días)</label>
              <div class="input-group">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-calendar-fold-icon lucide-calendar-fold"><path d="M8 2v4"/><path d="M16 2v4"/><path d="M21 17V6a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h11Z"/><path d="M3 10h18"/><path d="M15 22v-4a2 2 0 0 1 2-2h4"/></svg>
                <input type="number" id="dias" name="dias" value="<?php echo $paquete['dias']; ?>" required>
              </div>
            </div>
          </div>

          <div class="input-merge">
            <div class="input-container">
              <label for="cantidad">Stock disponible</label>
              <div class="input-group">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-file-box-icon lucide-file-box"><path d="M14.5 22H18a2 2 0 0 0 2-2V7l-5-5H6a2 2 0 0 0-2 2v4"/><path d="M14 2v4a2 2 0 0 0 2 2h4"/><path d="M3 13.1a2 2 0 0 0-1 1.76v3.24a2 2 0 0 0 .97 1.78L6 21.7a2 2 0 0 0 2.03.01L11 19.9a2 2 0 0 0 1-1.76V14.9a2 2 0 0 0-.97-1.78L8 11.3a2 2 0 0 0-2.03-.01Z"/><path d="M7 17v5"/><path d="M11.7 14.2 7 17l-4.7-2.8"/></svg>
                <input type="number" id="cantidad" name="cantidad" value="<?php echo $paquete['stock']; ?>" required>
              </div>
            </div>
            <div class="input-container">
              <label for="descuento">Descuento (%)</label>
              <div class="input-group">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-badge-percent-icon lucide-badge-percent"><path d="M3.85 8.62a4 4 0 0 1 4.78-4.77 4 4 0 0 1 6.74 0 4 4 0 0 1 4.78 4.78 4 4 0 0 1 0 6.74 4 4 0 0 1-4.77 4.78 4 4 0 0 1-6.75 0 4 4 0 0 1-4.78-4.77 4 4 0 0 1 0-6.76Z"/><path d="m15 9-6 6"/><path d="M9 9h.01"/><path d="M15 15h.01"/></svg>
                <input type="number" id="descuento" name="descuento" value="<?php echo $paquete['descuento']; ?>" min="0" max="100">
              </div>
            </div>
          </div>


          <input type="hidden" name="id" value="<?= $paquete['ID_Producto'] ?>">

          <div class="actions">
              <button type="submit" name="actualizar" class="btn-primary">Actualizar Paquete</button>
              <button type="button" class="btn-secondary" onclick="window.location.href='paquetes.php'">Cancelar</button>
          </div>
      </form>
    </section>
  </main>
  <script>
function previewImageSoft(input, index) {
  const file = input.files[0];
  if (!file) return;

  const reader = new FileReader();
  reader.onload = function (e) {
    const slot = input.closest('.image-slot-soft');

    // Ver si ya existe una imagen previa y actualizarla
    let img = slot.querySelector('img');
    if (!img) {
      img = document.createElement('img');
      img.className = 'current-image';
      slot.prepend(img);
    }

    img.src = e.target.result;

    // Ocultar el div con "+"
    const preview = slot.querySelector('.preview-soft');
    if (preview) {
      preview.style.display = 'none';
    }
  };
  reader.readAsDataURL(file);
}

</script>

</body>
</html>

