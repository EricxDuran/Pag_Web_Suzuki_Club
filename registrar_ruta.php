<?php
include 'includes/db.php';
include 'includes/header.php';

if (!isset($_SESSION['usuario_id'])) {
    echo "<script>window.location.href='login.php';</script>";
    exit();
}

$msg = "";
$tipo_msg = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $usuario_id = $_SESSION['usuario_id'];
    $titulo = $conn->real_escape_string($_POST['titulo']);
    $desc = $conn->real_escape_string($_POST['descripcion']);
    $fecha = $_POST['fecha_evento'];

    $sql = "INSERT INTO eventos (usuario_id, titulo, titulo_ca, descripcion, descripcion_ca, fecha_evento) 
            VALUES ($usuario_id, '$titulo', '$titulo', '$desc', '$desc', '$fecha')";

    if ($conn->query($sql)) {
        $msg = ($idioma == 'ca') ? "Ruta registrada correctament!" : "¡Ruta registrada correctamente!";
        $tipo_msg = "success";
    } else {
        $msg = "Error: " . $conn->error;
        $tipo_msg = "error";
    }
}
?>

<div class="container" style="max-width: 600px; padding: 40px 15px;">
    <h2 style="border-bottom: 2px solid var(--accent); display: inline-block; margin-bottom: 25px;">
        <i class="fas fa-map-plus"></i> <?php echo $lang['reg_ruta_titulo']; ?>
    </h2>

    <?php if($msg): ?>
        <div style="padding: 15px; margin-bottom: 20px; border-radius: 4px; color: white; background: <?php echo ($tipo_msg=='success')?'#28a745':'#dc3545'; ?>; text-align: center; font-weight: bold;">
            <?php echo $msg; ?>
        </div>
    <?php endif; ?>

    <div class="card" style="padding: 25px;">
        <form method="POST">
            <div style="margin-bottom: 15px;">
                <label style="font-weight: bold; display: block; margin-bottom: 5px;"><?php echo $lang['reg_ruta_fecha']; ?></label>
                <input type="date" name="fecha_evento" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px;">
            </div>

            <div style="margin-bottom: 15px;">
                <label style="font-weight: bold; display: block; margin-bottom: 5px;"><?php echo $lang['reg_ruta_label_tit']; ?></label>
                <input type="text" name="titulo" placeholder="<?php echo $lang['reg_ruta_placeholder_tit']; ?>" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px;">
            </div>

            <div style="margin-bottom: 20px;">
                <label style="font-weight: bold; display: block; margin-bottom: 5px;"><?php echo $lang['reg_ruta_label_desc']; ?></label>
                <textarea name="descripcion" rows="6" placeholder="<?php echo $lang['reg_ruta_placeholder_desc']; ?>" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px;"></textarea>
            </div>

            <button type="submit" class="btn" style="width: 100%; font-size: 1.1rem; background-color: var(--primary);">
                <i class="fas fa-save"></i> <?php echo $lang['reg_ruta_btn_guardar']; ?>
            </button>
            
            <a href="index.php" style="display: block; text-align: center; margin-top: 15px; color: #666; text-decoration: none;">
                <i class="fas fa-arrow-left"></i> <?php echo $lang['volver']; ?>
            </a>
        </form>
    </div>
</div>

<?php include 'includes/footer.php'; ?>