<?php

// --- Registro de nuevo usuario ---
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $usuario = $_POST["usuario"];
    $email = $_POST["email"];
    $contraseña = $_POST["contraseña"];
    $telefono = $_POST["telefono"];
    $rol = "cliente"; // Por defecto todos son clientes
     
    $contraseña_encry = password_hash($contraseña, PASSWORD_BCRYPT);
    include('../controller/conexionbd.php');
    $stmt = $Ruta->prepare("INSERT INTO usuario(usuario, contraseña, email, telefono, rol) 
                            VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssis", $usuario, $contraseña_encry, $email, $telefono, $rol);

    if ($stmt->execute()) {
        header("Location: ../pages/login.php");
        exit();
    } else {
        echo "Error al registrar usuario.";
    }
    $stmt->close();
}

?>
