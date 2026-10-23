<?php
include 'includes/db.php';
include 'includes/header.php';

if ($_SESSION['usuario_rol'] != 'admin') die("Acceso denegado");

$id_ruta = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Subir fotos
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES['fotos'])) {
    foreach ($_FILES['fotos']['tmp_name'] as $key => $tmp_name) {
        $nombre_foto = time() . "_" . $key . "_" . $_FILES['fotos']['name'][$key];
        if (move_uploaded_file($tmp_name, "uploads/" . $nombre_foto)) {
            $conn->query("INSERT INTO rutas_fotos (ruta_id, foto) VALUES ($id_ruta, '$nombre_foto')");
        }
    }
    header("Location: admin_fotos_ruta.php?id=$id_ruta");
}

// Borrar foto
if (isset($_GET['borrar_foto'])) {
    $id_foto = (int)$_GET['borrar_foto'];
    $conn->query("DELETE FROM rutas_fotos WHERE id = $id_foto");
    header("Location: admin_fotos_ruta.php?id=$id_ruta");
}
?>

<div class="container">
    <h2 class="section-title">Añadir Fotos a la Ruta</h2>
    
    <div class="card" style="padding: 20px; margin-bottom: 30px;">
        <form method="POST" enctype="multipart/form-data">
            <label>Selecciona varias fotos:</label>
            <input type="file" name="fotos[]" multiple accept="image/*" required style="margin-bottom: 15px;">
            <button type="submit" class="btn"><i class="fas fa-upload"></i> Subir Fotos</button>
        </form>
    </div>

    <div class="grid-3">
        <?php
        $res = $conn->query("SELECT * FROM rutas_fotos WHERE ruta_id = $id_ruta");
        while ($f = $res->fetch_assoc()) {
            echo "<div style='position:relative;'>";
            echo "<img src='uploads/".$f['foto']."' style='width:100%; height:200px; object-fit:cover; border-radius:5px;'>";
            echo "<a href='?id=$id_ruta&borrar_foto=".$f['id']."' class='btn' style='position:absolute; top:5px; right:5px; background:red; padding:5px;'><i class='fas fa-trash'></i></a>";
            echo "</div>";
        }
        ?>
    </div>
    
    <div style="margin-top: 30px; text-align: center;">
        <a href="admin.php" class="btn btn-secondary">&larr; Volver al Admin</a>
    </div>
</div>

<?php include 'includes/footer.php'; ?>