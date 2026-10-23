<?php
include 'includes/db.php';
include 'includes/header.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];

    $sql = "SELECT * FROM usuarios WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            $_SESSION['usuario_id'] = $row['id'];
            $_SESSION['usuario_nombre'] = $row['nombre'];
            $_SESSION['usuario_rol'] = $row['rol'];
            echo "<script>window.location.href='index.php';</script>";
        } else {
            $error = "Contraseña incorrecta.";
        }
    } else {
        $error = "Usuario no encontrado.";
    }
}
?>

<div class="container" style="max-width: 400px; margin-top: 50px;">
    <div class="card">
        <div class="card-body">
            <h2 style="text-align: center; margin-bottom: 20px; color: var(--primary);">
                <?php echo $lang['login_titulo']; ?>
            </h2>
            
            <?php if($error): ?>
                <p style="color: red; text-align: center;"><?php echo $error; ?></p>
            <?php endif; ?>

            <form method="POST">
                <div style="margin-bottom: 15px;">
                    <label><?php echo $lang['login_email']; ?></label>
                    <input type="email" name="email" required style="width: 100%; padding: 10px; margin-top: 5px;">
                </div>
                <div style="margin-bottom: 20px;">
                    <label><?php echo $lang['login_pass']; ?></label>
                    <input type="password" name="password" required style="width: 100%; padding: 10px; margin-top: 5px;">
                </div>
                <button type="submit" class="btn" style="width: 100%;"><?php echo $lang['login_btn']; ?></button>
            </form>

            <p style="margin-top: 20px; text-align: center; font-size: 0.9rem;">
                <?php echo $lang['login_no_cuenta']; ?> <a href="registro.php" style="color: var(--accent); font-weight: bold;"><?php echo $lang['login_registrate']; ?></a>
            </p>

            <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee; text-align: center;">
                <p style="font-weight: bold; color: #555; margin-bottom: 10px;">
                    <i class="fab fa-whatsapp" style="color: #25D366; font-size: 1.2rem;"></i> <?php echo $lang['whatsapp_titulo']; ?>
                </p>
                <a href="https://instagram.com/suzukisamuraiteam" target="_blank" class="btn" style="background: linear-gradient(45deg, #f09433 0%, #e6683c 25%, #dc2743 50%, #cc2366 75%, #bc1888 100%); width: 100%; font-size: 0.9rem;">
                    <i class="fab fa-instagram"></i> <?php echo $lang['whatsapp_btn']; ?>
                </a>
            </div>

        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>