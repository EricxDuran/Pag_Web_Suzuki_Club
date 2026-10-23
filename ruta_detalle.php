<?php
session_start();
include 'includes/db.php';
include 'includes/header.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$uid_actual = $_SESSION['usuario_id'] ?? 0;
$is_admin = ($_SESSION['usuario_rol'] ?? '') === 'admin';

// Borrar foto
if (isset($_GET['borrar_foto']) && $uid_actual > 0) {
    $id_foto = (int)$_GET['borrar_foto'];
    $sql_check = "SELECT foto FROM rutas_fotos WHERE id = $id_foto AND (usuario_id = $uid_actual OR '$is_admin' = '1')";
    $res_check = $conn->query($sql_check);
    
    if ($res_check && $res_check->num_rows > 0) {
        $f = $res_check->fetch_assoc();
        if (file_exists("uploads/" . $f['foto'])) unlink("uploads/" . $f['foto']);
        $conn->query("DELETE FROM rutas_fotos WHERE id = $id_foto");
    }
    header("Location: ruta_detalle.php?id=$id");
    exit;
}

// Subir fotos
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES['fotos']) && $uid_actual > 0) {
    foreach ($_FILES['fotos']['tmp_name'] as $key => $tmp_name) {
        if ($_FILES['fotos']['error'][$key] == 0) {
            $nombre_foto = time() . "_" . $key . "_" . basename($_FILES['fotos']['name'][$key]);
            if (move_uploaded_file($tmp_name, "uploads/" . $nombre_foto)) {
                $conn->query("INSERT INTO rutas_fotos (ruta_id, foto, usuario_id) VALUES ($id, '$nombre_foto', $uid_actual)");
            }
        }
    }
    header("Location: ruta_detalle.php?id=$id");
    exit;
}

$res_ruta = $conn->query("SELECT * FROM rutas WHERE id = $id");
$ruta = $res_ruta->fetch_assoc();

if (!$ruta) {
    echo "<div class='container'><p>" . $lang['ruta_no_encontrada'] . "</p></div>";
    include 'includes/footer.php';
    exit;
}
?>
<div class="container">
    <h2 class="section-title" style="text-align: center; margin-bottom: 30px;"><?php echo htmlspecialchars($ruta['titulo']); ?></h2>
    
    <?php if($uid_actual > 0): ?>
    <div class="card" style="padding: 15px; margin-bottom: 30px; text-align: center; background: #f9f9f9;">
        <form method="POST" enctype="multipart/form-data">
            <label style="font-weight: bold; display: block; margin-bottom: 10px;"><?php echo $lang['ruta_subir_fotos']; ?></label>
            <input type="file" name="fotos[]" multiple accept="image/*" required style="margin-bottom: 10px;">
            <button type="submit" class="btn"><i class="fas fa-upload"></i> <?php echo $lang['ruta_btn_subir']; ?></button>
        </form>
    </div>
    <?php else: ?>
    <div class="card" style="padding: 15px; margin-bottom: 30px; text-align: center; background: #fee;">
        <p style="color: #c0392b; font-weight: bold; margin: 0;">
            <i class="fas fa-lock"></i> <?php echo $lang['ruta_iniciar_sesion']; ?>
        </p>
    </div>
    <?php endif; ?>

    <div class="grid-3">
        <?php
        $res_fotos = $conn->query("SELECT * FROM rutas_fotos WHERE ruta_id = $id ORDER BY id DESC");
        if ($res_fotos && $res_fotos->num_rows > 0) {
            while ($foto = $res_fotos->fetch_assoc()) {
                $img = "uploads/" . $foto['foto'];
                $es_propietario = ($uid_actual > 0 && $foto['usuario_id'] == $uid_actual) || $is_admin;
                
                echo "<div style='position:relative; border-radius: 8px; overflow: hidden; box-shadow: var(--shadow); height: 250px;'>";
                echo "<a href='$img' target='_blank'><img src='$img' style='width: 100%; height: 100%; object-fit: cover;'></a>";
                
                if ($es_propietario) {
                    echo "<a href='?id=$id&borrar_foto=".$foto['id']."' onclick=\"return confirm('¿Borrar foto?');\" style='position:absolute; top:10px; right:10px; background:rgba(231,76,60,0.9); color:white; padding:8px 12px; border-radius:5px;'><i class='fas fa-trash'></i></a>";
                }
                echo "</div>";
            }
        } else {
            echo "<p style='text-align:center; grid-column: 1/-1;'>" . $lang['ruta_sin_fotos'] . "</p>";
        }
        ?>
    </div>

    <div style="text-align: center; margin-top: 40px;">
        <a href="rutas.php" class="btn btn-secondary">&larr; <?php echo $lang['ruta_volver']; ?></a>
    </div>
</div>
<?php include 'includes/footer.php'; ?>