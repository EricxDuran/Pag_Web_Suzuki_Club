<?php
include 'includes/db.php';
session_start();

// SEGURIDAD: Solo Admin
if (!isset($_SESSION['usuario_rol']) || $_SESSION['usuario_rol'] != 'admin') {
    header("Location: index.php"); exit();
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    // 1. Obtener nombre de la foto para borrarla del disco
    $sql_foto = "SELECT foto FROM coches WHERE id = $id";
    $res = $conn->query($sql_foto);
    
    if ($res->num_rows > 0) {
        $fila = $res->fetch_assoc();
        $ruta_foto = "uploads/" . $fila['foto'];
        
        // Si existe y no es la por defecto, borrar archivo
        if ($fila['foto'] != 'default.jpg' && file_exists($ruta_foto)) {
            unlink($ruta_foto); // Esto borra el archivo físico
        }
    }

    // 2. Borrar de la Base de Datos
    $conn->query("DELETE FROM coches WHERE id = $id");
}

// Volver al garaje
header("Location: miembros.php");
?>