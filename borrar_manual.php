<?php
include 'includes/db.php';
session_start();

if (!isset($_SESSION['usuario_rol']) || $_SESSION['usuario_rol'] != 'admin') {
    header("Location: index.php"); exit();
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    // 1. Obtener nombre del archivo
    $sql = "SELECT archivo FROM manuales WHERE id = $id";
    $res = $conn->query($sql);
    
    if ($res->num_rows > 0) {
        $fila = $res->fetch_assoc();
        $ruta = "descargas/" . $fila['archivo'];
        
        // 2. Borrar archivo físico
        if (file_exists($ruta)) { unlink($ruta); }
        
        // 3. Borrar de la BD
        $conn->query("DELETE FROM manuales WHERE id = $id");
    }
}

header("Location: admin.php");
?>