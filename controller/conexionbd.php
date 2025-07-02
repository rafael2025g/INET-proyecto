<?php
    $Ruta=new mysqli("localhost", "root", "", "database");
    if ($Ruta->connect_error) {
        die("Error de conexión: " . $Ruta->connect_error);
    }
    $Ruta -> set_charset("utf8");
?>
