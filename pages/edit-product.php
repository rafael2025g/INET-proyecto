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
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .form-group {
            margin-bottom: 1rem;
        }
        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: bold;
        }
        input, textarea, select {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
        }
        button {
            padding: 1rem 2rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
            margin-right: 1rem;
        }
        .btn-primary {
            background: #007bff;
            color: white;
        }
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        .btn-primary:hover {
            background: #0056b3;
        }
        .nav-link {
            display: inline-block;
            margin-bottom: 1rem;
            color: #007bff;
            text-decoration: none;
        }
        .current-image {
            max-width: 200px;
            height: auto;
            margin: 1rem 0;
            border-radius: 8px;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="paquetes.php" class="nav-link">← Volver a paquetes</a>
        <h1>Editar Paquete Turístico</h1>
        
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?php echo $paquete['ID_Producto']; ?>">
            
            <div class="form-group">
                <label for="titulo">Título del Paquete:</label>
                <input type="text" id="titulo" name="titulo" value="<?php echo htmlspecialchars($paquete['titulo']); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="descripcion">Descripción:</label>
                <textarea id="descripcion" name="descripcion" rows="4" required><?php echo htmlspecialchars($paquete['descripcion']); ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="lugar">Lugar/Destino:</label>
                <input type="text" id="lugar" name="lugar" value="<?php echo htmlspecialchars($paquete['lugar']); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="precio">Precio:</label>
                <input type="number" id="precio" name="precio" value="<?php echo $paquete['precio']; ?>" required>
            </div>
            
            <div class="form-group">
                <label for="dias">Duración (días):</label>
                <input type="number" id="dias" name="dias" value="<?php echo $paquete['dias']; ?>" required>
            </div>
            
            <div class="form-group">
                <label for="cantidad">Stock disponible:</label>
                <input type="number" id="cantidad" name="cantidad" value="<?php echo $paquete['stock']; ?>" required>
            </div>
            
            <div class="form-group">
                <label for="descuento">Descuento (%):</label>
                <input type="number" id="descuento" name="descuento" min="0" max="100" value="<?php echo $paquete['descuento']; ?>">
            </div>
            
            <div class="form-group">
                <label>Imagen actual:</label>
                <?php if($paquete['imagen']): ?>
                    <img src="data:image/jpeg;base64,<?php echo base64_encode($paquete['imagen']); ?>" alt="Imagen actual" class="current-image">
                <?php else: ?>
                    <p>No hay imagen actual</p>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="img">Nueva imagen (opcional):</label>
                <input type="file" id="img" name="img" accept="image/*">
                <small>Deja vacío si no quieres cambiar la imagen</small>
            </div>
            
            <button type="submit" name="actualizar" class="btn-primary">Actualizar Paquete</button>
            <button type="button" class="btn-secondary" onclick="window.location.href='paquetes.php'">Cancelar</button>
        </form>
    </div>
</body>
</html>
