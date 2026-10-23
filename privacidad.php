<?php include 'includes/db.php'; include 'includes/header.php'; ?>
<div class="container" style="padding: 40px 15px; line-height: 1.6;">
    <h1 style="border-bottom: 3px solid var(--accent); display: inline-block; margin-bottom: 20px;">
        <?php echo $lang['legal_privacidad_tit']; ?>
    </h1>
    <div class="card" style="padding: 30px; background: #fff; color: #333;">
        <h3><?php echo $lang['priv_resp']; ?></h3>
        <p><?php echo $lang['priv_resp_desc']; ?></p>
        
        <h3><?php echo $lang['priv_fin']; ?></h3>
        <p><?php echo $lang['priv_fin_desc']; ?></p>
        
        <h3><?php echo $lang['priv_derechos']; ?></h3>
        <p><?php echo $lang['priv_derechos_desc']; ?></p>
    </div>
</div>
<?php include 'includes/footer.php'; ?>