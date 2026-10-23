<?php 
include 'includes/db.php';      
include 'includes/header.php'; 

$mensaje = "";
$tipo_mensaje = ""; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nick = $conn->real_escape_string($_POST['nick']); 
    $nombre_real = $conn->real_escape_string($_POST['nombre_real']); 
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];

    // 0. DOBLE CHECK CONTRASEÑA
    if ($password !== $password_confirm) {
        $mensaje = $lang['reg_error_pass_match'];
        $tipo_mensaje = "error";
    } else {
        $check = $conn->query("SELECT id FROM usuarios WHERE email = '$email'");
        $check_nick = $conn->query("SELECT id FROM usuarios WHERE nombre = '$nick'");
        
        if ($check->num_rows > 0) {
            $mensaje = $lang['reg_error_email'];
            $tipo_mensaje = "error";
        } elseif ($check_nick->num_rows > 0) {
            $mensaje = ($idioma == 'ca') ? "❌ Aquest Nick ja està ocupat." : "❌ Ese Nick ya está ocupado. Elige otro.";
            $tipo_mensaje = "error";
        } else {
            // --- LÓGICA DE LA FOTO ---
            $nombre_foto = "default_user.jpg"; // Foto por defecto
            if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] == 0) {
                $ext = pathinfo($_FILES['foto_perfil']['name'], PATHINFO_EXTENSION);
                $nombre_foto = "perfil_" . time() . "_" . $nick . "." . $ext;
                $ruta_destino = "uploads/" . $nombre_foto;
                
                if (!move_uploaded_file($_FILES['foto_perfil']['tmp_name'], $ruta_destino)) {
                    $nombre_foto = "default_user.jpg"; // Si falla la subida, vuelve a la default
                }
            }

            $pass_hash = password_hash($password, PASSWORD_DEFAULT);
            $sql = "INSERT INTO usuarios (nombre, nombre_real, email, password, rol, foto_perfil) 
                    VALUES ('$nick', '$nombre_real', '$email', '$pass_hash', 'user', '$nombre_foto')";
            
            if ($conn->query($sql) === TRUE) {
                $mensaje = $lang['reg_ok'];
                $tipo_mensaje = "success";
                echo "<script>setTimeout(function(){ window.location.href='login.php'; }, 2000);</script>";
            } else {
                $mensaje = "Error: " . $conn->error;
                $tipo_mensaje = "error";
            }
        }
    }
}
?>

<div class="container" style="max-width: 500px; padding-top: 40px;">
    <div class="card" style="border-top: 5px solid var(--accent);">
        <div class="card-body">
            <h2 style="text-align: center; color: var(--primary); margin-bottom: 20px;">
                <i class="fas fa-user-plus"></i> <?php echo $lang['reg_titulo']; ?>
            </h2>
            
            <?php if($mensaje): ?>
                <div style="padding: 10px; margin-bottom: 15px; border-radius: 4px; text-align:center;
                    background: <?php echo ($tipo_mensaje=='error') ? '#ffcccc' : '#d4edda'; ?>;
                    color: <?php echo ($tipo_mensaje=='error') ? '#cc0000' : '#155724'; ?>;">
                    <?php echo $mensaje; ?>
                </div>
            <?php endif; ?>

            <form action="registro.php" method="POST" enctype="multipart/form-data">
                
                <div style="background: #f9f9f9; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
                    <div style="margin-bottom: 15px;">
                        <label style="font-weight: bold; color: var(--accent);"><i class="fas fa-id-badge"></i> <?php echo $lang['reg_nick']; ?></label>
                        <input type="text" name="nick" required placeholder="Ej: Samurai_King" style="width: 100%; padding: 10px; border: 1px solid #ccc; margin-top: 5px;">
                        <small style="color: #666; font-size: 0.8rem;"><?php echo $lang['reg_expl_nick']; ?></small>
                    </div>

                    <div style="margin-bottom: 5px;">
                        <label style="font-weight: bold;"><i class="fas fa-user"></i> <?php echo $lang['reg_nombre_real']; ?></label>
                        <input type="text" name="nombre_real" required placeholder="Ej: Juan" style="width: 100%; padding: 10px; border: 1px solid #ccc; margin-top: 5px;">
                        <small style="color: #666; font-size: 0.8rem;"><?php echo $lang['reg_expl_real']; ?></small>
                    </div>
                </div>

                <div style="margin-bottom: 15px;">
                    <label style="font-weight: bold;"><i class="fas fa-camera"></i> <?php echo $lang['reg_foto_perfil']; ?></label>
                    <input type="file" name="foto_perfil" accept="image/*" style="width: 100%; padding: 10px; border: 1px solid #ddd; margin-top: 5px; background: white;">
                    <small style="color: #666; font-size: 0.8rem;"><?php echo $lang['reg_expl_foto']; ?></small>
                </div>

                <div style="margin-bottom: 15px;">
                    <label style="font-weight: bold;"><i class="fas fa-envelope"></i> <?php echo $lang['login_email']; ?></label>
                    <input type="email" name="email" required style="width: 100%; padding: 10px; border: 1px solid #ddd; margin-top: 5px;">
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 25px;">
                    <div>
                        <label style="font-weight: bold;"><i class="fas fa-lock"></i> <?php echo $lang['login_pass']; ?></label>
                        <input type="password" name="password" required style="width: 100%; padding: 10px; border: 1px solid #ddd; margin-top: 5px;">
                    </div>
                    <div>
                        <label style="font-weight: bold;"><i class="fas fa-lock"></i> <?php echo $lang['reg_pass_confirm']; ?></label>
                        <input type="password" name="password_confirm" required style="width: 100%; padding: 10px; border: 1px solid #ddd; margin-top: 5px;">
                    </div>
                </div>

                <button type="submit" class="btn" style="width: 100%; font-size: 1.1rem;"><?php echo $lang['reg_btn']; ?></button>
            </form>

            <div style="text-align: center; margin-top: 20px; font-size: 0.9rem;">
                <?php echo $lang['reg_ya_cuenta']; ?> <a href="login.php" style="color: var(--accent); font-weight: bold;"><?php echo $lang['login_btn']; ?></a>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>