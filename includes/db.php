<?php
// includes/db.php

$host = "sql107.infinityfree.com";
$user = "if0_41013565";       // Usuario por defecto de XAMPP
$pass = "Amarillo2607";           // Contraseña vacía por defecto de XAMPP
$db   = "if0_41013565_suzukisamuraiteam"; // <--- Nombre corregido

$conn = new mysqli($host, $user, $pass, $db);
$conn->set_charset("utf8");

// Verificar conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}
?>