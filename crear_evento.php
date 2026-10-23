<?php
include 'includes/db.php';
include 'includes/header.php';

// Seguridad Admin
if (!isset($_SESSION['usuario_rol']) || $_SESSION['usuario_rol'] != 'admin') {
    echo "<script>window.location.href='index.php';</script>";
    exit();
}

$msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Datos en Español
    $titulo_es = $conn->real_escape_string($_POST['titulo']);
    $desc_es = $conn->real_escape_string($_POST['descripcion']);
    
    // Datos en Catalán
    $titulo_ca = $conn->real_escape_string($_POST['titulo_ca']);
    $desc_ca = $conn->real_escape_string($_POST['descripcion_ca']);
    
    $fecha = $_POST['fecha'];

    // Insertamos todo junto
    $sql = "INSERT INTO eventos (titulo, titulo_ca, descripcion, descripcion_ca, fecha_evento) 
            VALUES ('$titulo_es', '$titulo_ca', '$desc_es', '$desc_ca', '$fecha')";

    if ($conn->query($sql) === TRUE) {
        $msg = "✅ Evento publicado en ES y CA correctamente.";
    } else {
        $msg = "❌ Error: " . $conn->error;
    }
}
?>

<div class="container" style="max-width: 800px;">
    <a href="admin.php" style="color: #666;"><i class="fas fa-arrow-left"></i> Volver al Panel</a>
    
    <h2 style="margin: 20px 0; color: var(--accent);">Publicar Nueva Ruta (Bilingüe)</h2>

    <?php if($msg): ?>
        <div style="padding: 15px; background: #eee; margin-bottom: 20px; border-left: 5px solid var(--primary);">
            <?php echo $msg; ?>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="">
                
                <div style="margin-bottom: 20px;">
                    <label style="font-weight: bold;">Fecha del Evento</label>
                    <input type="date" name="fecha" required style="width: 100%; padding: 10px; margin-top: 5px; border: 1px solid #ccc;">
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    
                    <div style="background: #fff5f5; padding: 15px; border-radius: 5px; border: 1px solid #ffcccc;">
                        <h4 style="color: var(--accent); margin-bottom: 10px;">🇪🇸 Versión Español</h4>
                        
                        <div style="margin-bottom: 10px;">
                            <label>Título</label>
                            <input type="text" name="titulo" required placeholder="Ej: Ruta Pirineos" style="width: 100%; padding: 8px;">
                        </div>
                        
                        <div>
                            <label>Descripción</label>
                            <textarea name="descripcion" required rows="4" style="width: 100%; padding: 8px;"></textarea>
                        </div>
                    </div>

                    <div style="background: #fff9e6; padding: 15px; border-radius: 5px; border: 1px solid #ffeeba;">
                        <h4 style="color: #b8860b; margin-bottom: 10px;">🏴 Versió Catalana</h4>
                        
                        <div style="margin-bottom: 10px;">
                            <label>Títol</label>
                            <input type="text" name="titulo_ca" required placeholder="Ex: Ruta Pirineus" style="width: 100%; padding: 8px;">
                        </div>
                        
                        <div>
                            <label>Descripció</label>
                            <textarea name="descripcion_ca" required rows="4" style="width: 100%; padding: 8px;"></textarea>
                        </div>
                    </div>

                </div>

                <button type="submit" class="btn" style="width: 100%; margin-top: 20px;">Publicar Evento / Publicar Esdeveniment</button>
            </form>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>