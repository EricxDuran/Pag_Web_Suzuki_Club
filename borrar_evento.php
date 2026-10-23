<?php
include 'includes/db.php';
session_start();

// Seguridad Admin
if (!isset($_SESSION['usuario_rol']) || $_SESSION['usuario_rol'] != 'admin') {
    header("Location: index.php"); exit();
}

// Verificar que nos pasan un ID
if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // intval por seguridad (evita inyección SQL básica)
    
    // Borramos
    $sql = "DELETE FROM eventos WHERE id = $id";
    
    if ($conn->query($sql) === TRUE) {
        // Redirigir al admin
        header("Location: admin.php");
    } else {
        echo "Error al borrar: " . $conn->error;
    }
} else {
    header("Location: admin.php");
}
?>