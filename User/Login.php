<?php
session_start();

$base_url = dirname($_SERVER['PHP_SELF']);

if (isset($_SESSION['id_anggota'])) {
    header('Location: ' . $base_url . '/../Admin/CRUD/AnggotaLab/IndexAnggota.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Login</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link
            href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
            rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
        <link rel="icon" type="images/x-icon" href="../Assets/Image/Logo/Logo Without Text.png" />
        <link rel="stylesheet" href="../Assets/Css/Login.css">
    </head>
    <body>
        <div id="header"></div>

        <div class="heading">
            <h1>Selamat Datang</h1>
            <p>Silahkan isi form login berikut</p>
        </div>

        <div class="login-wrapper">
            <div class="login-card">
                <h2 class="login-title">Login</h2>

                <?php if (!empty($_GET['error'])): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($_GET['error']) ?></div>
                <?php endif; ?>

                <form action="Autentikasi.php" method="POST" autocomplete="on">
                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" id="username" name="username" required>
                    </div>

                    <div class="form-group">
                        <label>Password</label>
                        <div class="password-field">
                            <input type="password" id="password" name="password" required>
                        </div>
                    </div>

                    <div class="forgot">
                        <a href="#">&nbsp;</a>
                    </div>

                    <button type="submit" class="btn-login">Masuk</button>
                </form>
            </div>
        </div>
        <div id="footer"></div>

        <script src="../Assets/Javascript/HeaderFooter.js"></script>
    </body>
</html>