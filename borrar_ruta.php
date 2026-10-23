<?php
session_start();
include 'includes/db.php';

if (!isset($_SESSION['usuario_rol']) || $_SESSION['usuario_rol'] != 'admin') {
    header("Location: index.php");
    exit();
}

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];

    // 1. Borrar archivos de fotos
    $res_fotos = $conn->query("SELECT foto FROM rutas_fotos WHERE ruta_id = $id");
    while ($f = $res_fotos->fetch_assoc()) {
        $ruta_foto = "uploads/" . $f['foto'];
        if (file_exists($ruta_foto)) {
            unlink($ruta_foto);
        }
    }

    // 2. Borrar archivo de portada
    $res_ruta = $conn->query("SELECT foto_portada FROM rutas WHERE id = $id");
    if ($r = $res_ruta->fetch_assoc()) {
        $portada = "uploads/" . $r['foto_portada'];
        if (file_exists($portada)) {
            unlink($portada);
        }
    }

    // 3. Borrar de la base de datos
    $conn->query("DELETE FROM rutas_fotos WHERE ruta_id = $id");
    $conn->query("DELETE FROM rutas WHERE id = $id");
}

header("Location: admin.php");
exit();
?>