<?php
session_start();
include 'includes/db.php';

if ($_SESSION['usuario_rol'] != 'admin') die("Acceso denegado");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titulo = $conn->real_escape_string($_POST['titulo']);
    
    $foto_nombre = time() . "_" . $_FILES['foto_portada']['name'];
    $ruta_destino = "uploads/" . $foto_nombre;
    
    if (move_uploaded_file($_FILES['foto_portada']['tmp_name'], $ruta_destino)) {
        $conn->query("INSERT INTO rutas (titulo, foto_portada) VALUES ('$titulo', '$foto_nombre')");
    }
    header("Location: admin.php");
}
?>