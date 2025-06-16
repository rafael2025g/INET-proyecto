<?php
include('../model/modelos.php');

// Verificar si el usuario es admin
if(!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración - TravelWorld</title>
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
            background: #007bff;
            color: white;
            padding: 1rem 2rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
        }
        button:hover {
            background: #0056b3;
        }
        .nav-link {
            display: inline-block;
            margin-bottom: 1rem;
            color: #007bff;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="../index.php" class="nav-link">← Volver al inicio</a>
        <h1>Panel de Administración</h1>
        <h2>Agregar Nuevo Paquete Turístico</h2>
        
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="titulo">Título del Paquete:</label>
                <input type="text" id="titulo" name="titulo" required>
            </div>
            
            <div class="form-group">
                <label for="descripcion">Descripción:</label>
                <textarea id="descripcion" name="descripcion" rows="4" required></textarea>
            </div>
            
            <div class="form-group">
                <label for="lugar">Lugar/Destino:</label>
                <input type="text" id="lugar" name="lugar" required>
            </div>
            
            <div class="form-group">
                <label for="precio">Precio:</label>
                <input type="number" id="precio" name="precio" required>
            </div>
            
            <div class="form-group">
                <label for="dias">Duración (días):</label>
                <input type="number" id="dias" name="dias" required>
            </div>
            
            <div class="form-group">
                <label for="cantidad">Stock disponible:</label>
                <input type="number" id="cantidad" name="cantidad" required>
            </div>
            
            <div class="form-group">
                <label for="descuento">Descuento (%):</label>
                <input type="number" id="descuento" name="descuento" min="0" max="100" value="0">
            </div>
            
            <div class="form-group">
                <label for="img">Imagen del paquete:</label>
                <input type="file" id="img" name="img" accept="image/*" required>
            </div>
            
            <button type="submit" name="ingresar">Agregar Paquete</button>
        </form>
    </div>
</body>
</html>
