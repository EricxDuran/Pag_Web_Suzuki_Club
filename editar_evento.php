<?php
include 'includes/db.php';
include 'includes/header.php';

// Seguridad Admin
if (!isset($_SESSION['usuario_rol']) || $_SESSION['usuario_rol'] != 'admin') {
    echo "<script>window.location.href='index.php';</script>";
    exit();
}

// 1. OBTENER DATOS DEL EVENTO ACTUAL
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $sql = "SELECT * FROM eventos WHERE id = $id";
    $result = $conn->query($sql);
    
    if ($result->num_rows == 1) {
        $evento = $result->fetch_assoc();
    } else {
        echo "<script>window.location.href='admin.php';</script>"; exit();
    }
} else {
    echo "<script>window.location.href='admin.php';</script>"; exit();
}

// 2. PROCESAR EL FORMULARIO AL GUARDAR CAMBIOS
$msg = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = intval($_POST['id']);
    $titulo_es = $conn->real_escape_string($_POST['titulo']);
    $desc_es = $conn->real_escape_string($_POST['descripcion']);
    $titulo_ca = $conn->real_escape_string($_POST['titulo_ca']);
    $desc_ca = $conn->real_escape_string($_POST['descripcion_ca']);
    $fecha = $_POST['fecha'];

    // SQL UPDATE (Actualizar) en lugar de INSERT
    $sql = "UPDATE eventos SET 
            titulo='$titulo_es', 
            titulo_ca='$titulo_ca', 
            descripcion='$desc_es', 
            descripcion_ca='$desc_ca', 
            fecha_evento='$fecha' 
            WHERE id=$id";

    if ($conn->query($sql) === TRUE) {
        $msg = "✅ Cambios guardados correctamente.";
        // Actualizamos la variable $evento para que se vea reflejado en el formulario al instante
        $evento['titulo'] = $titulo_es; 
        $evento['titulo_ca'] = $titulo_ca;
        $evento['descripcion'] = $desc_es;
        $evento['descripcion_ca'] = $desc_ca;
        $evento['fecha_evento'] = $fecha;
    } else {
        $msg = "❌ Error: " . $conn->error;
    }
}
?>

<div class="container" style="max-width: 800px;">
    <a href="admin.php" style="color: #666;"><i class="fas fa-arrow-left"></i> Volver al Panel</a>
    
    <h2 style="margin: 20px 0; color: var(--primary);">Editar Evento</h2>

    <?php if($msg): ?>
        <div style="padding: 15px; background: #d4edda; color: #155724; margin-bottom: 20px; border-radius: 4px;">
            <?php echo $msg; ?>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="">
                <input type="hidden" name="id" value="<?php echo $evento['id']; ?>">

                <div style="margin-bottom: 20px;">
                    <label style="font-weight: bold;">Fecha del Evento</label>
                    <input type="date" name="fecha" value="<?php echo $evento['fecha_evento']; ?>" required style="width: 100%; padding: 10px; margin-top: 5px; border: 1px solid #ccc;">
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    
                    <div style="background: #fff5f5; padding: 15px; border-radius: 5px; border: 1px solid #ffcccc;">
                        <h4 style="color: var(--accent); margin-bottom: 10px;">🇪🇸 Español</h4>
                        <div style="margin-bottom: 10px;">
                            <label>Título</label>
                            <input type="text" name="titulo" value="<?php echo $evento['titulo']; ?>" required style="width: 100%; padding: 8px;">
                        </div>
                        <div>
                            <label>Descripción</label>
                            <textarea name="descripcion" required rows="4" style="width: 100%; padding: 8px;"><?php echo $evento['descripcion']; ?></textarea>
                        </div>
                    </div>

                    <div style="background: #fff9e6; padding: 15px; border-radius: 5px; border: 1px solid #ffeeba;">
                        <h4 style="color: #b8860b; margin-bottom: 10px;">🏴 Català</h4>
                        <div style="margin-bottom: 10px;">
                            <label>Títol</label>
                            <input type="text" name="titulo_ca" value="<?php echo $evento['titulo_ca']; ?>" required style="width: 100%; padding: 8px;">
                        </div>
                        <div>
                            <label>Descripció</label>
                            <textarea name="descripcion_ca" required rows="4" style="width: 100%; padding: 8px;"><?php echo $evento['descripcion_ca']; ?></textarea>
                        </div>
                    </div>

                </div>

                <button type="submit" class="btn" style="width: 100%; margin-top: 20px;">Guardar Cambios</button>
            </form>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>