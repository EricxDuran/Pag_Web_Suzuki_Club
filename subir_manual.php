<?php
include 'includes/db.php';
include 'includes/header.php';

// SEGURIDAD ADMIN
if (!isset($_SESSION['usuario_rol']) || $_SESSION['usuario_rol'] != 'admin') {
    echo "<script>window.location.href='index.php';</script>"; exit();
}

$msg = "";
$carpeta_descargas = 'descargas/';

// 1. ESCANEAR LA CARPETA PARA BUSCAR ARCHIVOS
$archivos_encontrados = array();
if (is_dir($carpeta_descargas)) {
    $archivos = scandir($carpeta_descargas);
    foreach ($archivos as $archivo) {
        // Filtramos: que no sean carpetas ('.' o '..') y que no sean archivos ocultos
        if ($archivo !== '.' && $archivo !== '..' && $archivo[0] !== '.') {
            $archivos_encontrados[] = $archivo;
        }
    }
} else {
    // Si la carpeta no existe, intentamos crearla
    mkdir($carpeta_descargas, 0755, true);
}

// 2. PROCESAR EL FORMULARIO
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titulo = $conn->real_escape_string($_POST['titulo']);
    $titulo_ca = $conn->real_escape_string($_POST['titulo_ca']);
    $desc = $conn->real_escape_string($_POST['descripcion']);
    $desc_ca = $conn->real_escape_string($_POST['descripcion_ca']);
    $archivo_seleccionado = $conn->real_escape_string($_POST['archivo_seleccionado']);

    if (!empty($archivo_seleccionado)) {
        // Verificar que el archivo sigue existiendo
        if (file_exists($carpeta_descargas . $archivo_seleccionado)) {
            $sql = "INSERT INTO manuales (titulo, titulo_ca, descripcion, descripcion_ca, archivo) 
                    VALUES ('$titulo', '$titulo_ca', '$desc', '$desc_ca', '$archivo_seleccionado')";
            
            if ($conn->query($sql)) {
                $msg = "✅ Manual registrado correctamente en la web.";
            } else {
                $msg = "❌ Error SQL: " . $conn->error;
            }
        } else {
            $msg = "❌ Error: El archivo '$archivo_seleccionado' ya no está en la carpeta.";
        }
    } else {
        $msg = "❌ Debes seleccionar un archivo de la lista.";
    }
}
?>

<div class="container" style="max-width: 800px;">
    <a href="admin.php" style="color: #666;"><i class="fas fa-arrow-left"></i> Volver al Panel</a>
    <h2 style="margin: 20px 0; color: var(--primary);">Registrar Manual (FTP)</h2>

    <?php if($msg): ?>
        <div style="padding: 15px; background: #eee; margin-bottom: 20px; border-left: 5px solid var(--primary); font-weight: bold;">
            <?php echo $msg; ?>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            
            <div style="background: #e8f4fd; padding: 15px; border-radius: 5px; border: 1px solid #b6d4fe; margin-bottom: 25px;">
                <h4 style="color: #0d6efd; margin-bottom: 5px;"><i class="fas fa-info-circle"></i> Cómo funciona esto:</h4>
                <ol style="margin-left: 20px; font-size: 0.9rem; color: #444;">
                    <li>Sube tus PDFs a la carpeta <b>/descargas/</b> usando tu File Manager o FTP.</li>
                    <li>Recarga esta página.</li>
                    <li>Selecciona el archivo en la lista de abajo y ponle título.</li>
                </ol>
            </div>

            <form method="POST">
                
                <div style="margin-bottom: 30px;">
                    <label style="font-weight: bold; display: block; margin-bottom: 10px; font-size: 1.1rem;">
                        <i class="fas fa-folder-open"></i> Selecciona el archivo del servidor:
                    </label>
                    
                    <?php if (count($archivos_encontrados) > 0): ?>
                        <select name="archivo_seleccionado" required style="width: 100%; padding: 12px; font-size: 1rem; border: 2px solid var(--primary); background: #fff;">
                            <option value="">-- Elige un archivo --</option>
                            <?php foreach ($archivos_encontrados as $f): ?>
                                <option value="<?php echo $f; ?>"><?php echo $f; ?></option>
                            <?php endforeach; ?>
                        </select>
                    <?php else: ?>
                        <div style="padding: 15px; background: #ffe6e6; color: #cc0000; border: 1px solid #cc0000; text-align: center;">
                            ❌ No se han encontrado archivos en la carpeta <b>/descargas/</b>.<br>
                            Súbelos por FTP primero.
                        </div>
                    <?php endif; ?>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div style="background: #fff5f5; padding: 15px; border-radius: 5px; border: 1px solid #ffcccc;">
                        <h4 style="color: var(--accent); margin-bottom: 10px;">🇪🇸 Español</h4>
                        <div style="margin-bottom: 10px;">
                            <label>Título</label>
                            <input type="text" name="titulo" required placeholder="Ej: Manual Taller SJ410" style="width: 100%; padding: 8px;">
                        </div>
                        <div>
                            <label>Descripción</label>
                            <textarea name="descripcion" rows="3" style="width: 100%; padding: 8px;"></textarea>
                        </div>
                    </div>

                    <div style="background: #fff9e6; padding: 15px; border-radius: 5px; border: 1px solid #ffeeba;">
                        <h4 style="color: #b8860b; margin-bottom: 10px;">🏴 Català</h4>
                        <div style="margin-bottom: 10px;">
                            <label>Títol</label>
                            <input type="text" name="titulo_ca" required placeholder="Ex: Manual Taller SJ410" style="width: 100%; padding: 8px;">
                        </div>
                        <div>
                            <label>Descripció</label>
                            <textarea name="descripcion_ca" rows="3" style="width: 100%; padding: 8px;"></textarea>
                        </div>
                    </div>
                </div>

                <?php if (count($archivos_encontrados) > 0): ?>
                    <button type="submit" class="btn" style="width: 100%; margin-top: 20px;">
                        <i class="fas fa-save"></i> Registrar en la Biblioteca
                    </button>
                <?php endif; ?>
            </form>
        </div>
    </div>
</div>
<?php include 'includes/footer.php'; ?>