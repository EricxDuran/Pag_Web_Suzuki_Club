<?php
include 'includes/db.php';
include 'includes/header.php';

if (!isset($_SESSION['usuario_id'])) { echo "<script>window.location.href='login.php';</script>"; exit(); }

$usuario_id = $_SESSION['usuario_id'];
$msg = "";
$tipo_msg = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // 1. ACTUALIZAR DATOS USUARIO (NICK, NOMBRE REAL Y EMAIL)
    if (isset($_POST['actualizar_usuario'])) {
        $nombre = $conn->real_escape_string($_POST['nombre']); // Nick
        $nombre_real = $conn->real_escape_string($_POST['nombre_real']); 
        $email = $conn->real_escape_string($_POST['email']);
        
        $sql = "UPDATE usuarios SET nombre='$nombre', nombre_real='$nombre_real', email='$email' WHERE id=$usuario_id";
        if ($conn->query($sql)) {
            $_SESSION['usuario_nombre'] = $nombre;
            $msg = $lang['perfil_msg_usuario_ok']; $tipo_msg = "success";
        }
    }
    
    // 2. CAMBIAR CONTRASEÑA
    elseif (isset($_POST['cambiar_pass'])) {
        $pass_nueva = password_hash($_POST['pass_nueva'], PASSWORD_DEFAULT);
        if ($conn->query("UPDATE usuarios SET password='$pass_nueva' WHERE id=$usuario_id")) {
            $msg = $lang['perfil_msg_pass_ok']; $tipo_msg = "success";
        }
    }
    
    // 3. ACTUALIZAR DATOS DEL COCHE
    elseif (isset($_POST['actualizar_coche'])) {
        $modelo = $conn->real_escape_string($_POST['modelo']);
        $anyo = !empty($_POST['anyo']) ? intval($_POST['anyo']) : 0;
        $mods = $conn->real_escape_string($_POST['mods']);
        
        $foto_sql = ""; 
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
            $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
            $nombre_archivo = "coche_" . $usuario_id . "_" . time() . "." . $ext;
            $ruta_destino = "uploads/" . $nombre_archivo;
            if (move_uploaded_file($_FILES['foto']['tmp_name'], $ruta_destino)) {
                $foto_sql = ", foto='$nombre_archivo'";
            }
        }

        $check = $conn->query("SELECT id FROM coches WHERE usuario_id = $usuario_id");
        if ($check->num_rows > 0) {
            $sql = "UPDATE coches SET modelo='$modelo', anyo='$anyo', modificaciones='$mods' $foto_sql WHERE usuario_id=$usuario_id";
        } else {
            $foto_ini = ($foto_sql != "") ? $nombre_archivo : "default.jpg";
            $sql = "INSERT INTO coches (usuario_id, modelo, anyo, modificaciones, foto) VALUES ($usuario_id, '$modelo', '$anyo', '$mods', '$foto_ini')";
        }
        if ($conn->query($sql)) { $msg = $lang['garage_msg_exito']; $tipo_msg = "success"; }
    }

    // BORRAR FOTO COCHE
    elseif (isset($_POST['borrar_foto_coche'])) {
        $res_f = $conn->query("SELECT foto FROM coches WHERE usuario_id=$usuario_id");
        if($f = $res_f->fetch_assoc()) {
            if($f['foto'] != 'default.jpg' && file_exists("uploads/".$f['foto'])) unlink("uploads/".$f['foto']);
            $conn->query("UPDATE coches SET foto='default.jpg' WHERE usuario_id=$usuario_id");
            $msg = "Foto eliminada."; $tipo_msg = "success";
        }
    }
    
    // 4. BORRAR CUENTA
    elseif (isset($_POST['borrar_cuenta'])) {
        $conn->query("DELETE FROM coches WHERE usuario_id=$usuario_id");
        $conn->query("DELETE FROM usuarios WHERE id=$usuario_id");
        echo "<script>window.location.href='logout.php';</script>"; exit();
    }
}

$usuario = $conn->query("SELECT * FROM usuarios WHERE id = $usuario_id")->fetch_assoc();
$res_c = $conn->query("SELECT * FROM coches WHERE usuario_id = $usuario_id");
$coche = ($res_c->num_rows > 0) ? $res_c->fetch_assoc() : ['modelo'=>'', 'anyo'=>'', 'modificaciones'=>'', 'foto'=>'default.jpg'];
?>

<div class="container" style="max-width: 1100px;">
    <h2 style="color: var(--primary); margin-bottom: 25px; border-bottom: 2px solid var(--accent); display:inline-block;">
        <i class="fas fa-user-circle"></i> <?php echo $lang['perfil_titulo']; ?>
    </h2>

    <?php if($msg): ?>
        <div style="padding: 15px; margin-bottom: 25px; border-radius: 4px; color: white; background: <?php echo ($tipo_msg=='success')?'#28a745':'#dc3545'; ?>; text-align: center; font-weight: bold;">
            <?php echo $msg; ?>
        </div>
    <?php endif; ?>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 30px;">
        
        <div>
            <div class="card" style="margin-bottom: 25px;">
                <div class="card-body">
                    <h3 style="margin-bottom: 20px;"><i class="fas fa-id-card"></i> <?php echo $lang['perfil_datos_cuenta']; ?></h3>
                    <form method="POST">
                        <label style="font-weight: bold; display: block; margin-bottom: 5px;"><?php echo $lang['perfil_label_nick']; ?></label>
                        <input type="text" name="nombre" value="<?php echo htmlspecialchars($usuario['nombre']); ?>" required style="width: 100%; padding: 12px; margin-bottom: 15px; border: 1px solid #ccc; border-radius: 4px;">
                        
                        <label style="font-weight: bold; display: block; margin-bottom: 5px;"><?php echo $lang['perfil_label_nombre_real']; ?></label>
                        <input type="text" name="nombre_real" value="<?php echo htmlspecialchars($usuario['nombre_real']); ?>" required style="width: 100%; padding: 12px; margin-bottom: 15px; border: 1px solid #ccc; border-radius: 4px;">

                        <label style="font-weight: bold; display: block; margin-bottom: 5px;"><?php echo $lang['perfil_label_email']; ?></label>
                        <input type="email" name="email" value="<?php echo htmlspecialchars($usuario['email']); ?>" required style="width: 100%; padding: 12px; margin-bottom: 15px; border: 1px solid #ccc; border-radius: 4px;">
                        
                        <button type="submit" name="actualizar_usuario" class="btn" style="width: 100%;"><?php echo $lang['perfil_btn_actualizar']; ?></button>
                    </form>
                </div>
            </div>

            <div class="card" style="margin-bottom: 25px;">
                <div class="card-body">
                    <h3 style="margin-bottom: 20px;"><i class="fas fa-lock"></i> <?php echo $lang['perfil_seguridad']; ?></h3>
                    <form method="POST">
                        <label style="font-weight: bold; display: block; margin-bottom: 5px;"><?php echo $lang['perfil_pass_nueva']; ?></label>
                        <input type="password" name="pass_nueva" required style="width: 100%; padding: 12px; margin-bottom: 15px; border: 1px solid #ccc; border-radius: 4px;">
                        <button type="submit" name="cambiar_pass" class="btn btn-secondary" style="width: 100%;"><?php echo $lang['perfil_btn_pass']; ?></button>
                    </form>
                </div>
            </div>

            <div class="card" style="border: 2px solid #e74c3c;">
                <div class="card-body" style="background: #fff5f5;">
                    <h3 style="color: #c0392b; margin-bottom: 10px;"><i class="fas fa-trash-alt"></i> <?php echo $lang['perfil_zona_peligro']; ?></h3>
                    <p style="font-size: 0.9rem; margin-bottom: 15px; color: #666;"><?php echo $lang['perfil_borrar_desc']; ?></p>
                    <form method="POST" onsubmit="return confirm('<?php echo $lang['perfil_confirm_borrar']; ?>');">
                        <button type="submit" name="borrar_cuenta" class="btn" style="background: #e74c3c; width: 100%; color: white; border: none;">
                            <?php echo $lang['perfil_btn_borrar']; ?>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div>
            <div class="card" style="height: 100%;">
                <div class="card-body">
                    <h3 style="margin-bottom: 20px;"><i class="fas fa-car"></i> <?php echo $lang['garage_titulo_editar']; ?></h3>
                    
                    <div style="text-align: center; margin-bottom: 25px; background: #eee; padding: 15px; border-radius: 8px;">
                        <?php 
                            $ruta_foto = "uploads/" . $coche['foto'];
                            if (!file_exists($ruta_foto) || empty($coche['foto'])) { $ruta_foto = "img/default_samurai.jpg"; }
                        ?>
                        <img src="<?php echo $ruta_foto; ?>" style="max-width: 100%; max-height: 280px; border-radius: 5px;">
                        
                        <?php if($coche['foto'] != 'default.jpg'): ?>
                        <form method="POST" style="margin-top: 10px;">
                            <button type="submit" name="borrar_foto_coche" class="btn" style="background: #e74c3c; padding: 5px 10px; font-size: 0.8rem;">
                                <i class="fas fa-trash"></i> <?php echo $lang['perfil_btn_borrar_foto']; ?>
                            </button>
                        </form>
                        <?php endif; ?>
                    </div>

                    <form method="POST" enctype="multipart/form-data">
                        <div style="margin-bottom: 15px;">
                            <label style="font-weight: bold; display: block; margin-bottom: 5px;"><?php echo $lang['garage_label_modelo']; ?></label>
                            <input type="text" name="modelo" value="<?php echo htmlspecialchars($coche['modelo']); ?>" placeholder="<?php echo $lang['garage_placeholder_modelo']; ?>" style="width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 4px;">
                        </div>

                        <div style="margin-bottom: 15px;">
                            <label style="font-weight: bold; display: block; margin-bottom: 5px;"><?php echo $lang['garage_label_anyo']; ?></label>
                            <input type="number" name="anyo" value="<?php echo ($coche['anyo'] > 0) ? htmlspecialchars($coche['anyo']) : ''; ?>" placeholder="<?php echo $lang['garage_placeholder_anyo']; ?>" style="width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 4px;">
                        </div>

                        <div style="margin-bottom: 15px;">
                            <label style="font-weight: bold; display: block; margin-bottom: 5px;"><?php echo $lang['garage_label_mods']; ?></label>
                            <textarea name="mods" rows="5" placeholder="<?php echo $lang['garage_placeholder_mods']; ?>" style="width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 4px;"><?php echo htmlspecialchars($coche['modificaciones']); ?></textarea>
                        </div>

                        <div style="margin-bottom: 25px;">
                            <label style="font-weight: bold; display: block; margin-bottom: 5px;"><?php echo $lang['garage_label_foto']; ?></label>
                            <input type="file" name="foto" accept="image/*" style="width: 100%; padding: 5px;">
                        </div>

                        <button type="submit" name="actualizar_coche" class="btn" style="width: 100%; font-size: 1.1rem; background: var(--primary);">
                            <i class="fas fa-save"></i> <?php echo $lang['garage_btn_guardar']; ?>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>