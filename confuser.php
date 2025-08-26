<?php
session_start();
include('conexion.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: Inicio Sesion.php");
    exit;
}

// Recuperar datos del usuario de la BD
$userId = $_SESSION['user_id'];
$sql = "SELECT Nombre_Completo, FotoPerfil, Banner FROM usuario WHERE CI = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

$fotoPerfil = $user['FotoPerfil'] ?: 'assets/media/perfil.png';
$banner = $user['Banner'] ?: 'linear-gradient(135deg, #3498db, #2ecc71)';

// Subir foto de perfil
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['foto'])) {
    $file = $_FILES['foto'];
    if ($file['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $nuevoNombre = $userId . "." . $ext;
        $ruta = "uploads/" . $nuevoNombre;

        if (move_uploaded_file($file['tmp_name'], $ruta)) {
            $sql = "UPDATE usuario SET FotoPerfil=? WHERE CI=?";
            $stmt = $conexion->prepare($sql);
            $stmt->bind_param("si", $ruta, $userId);
            $stmt->execute();

            $fotoPerfil = $ruta;
            $_SESSION['profile_pic'] = $ruta;
        } else {
            echo "<script>alert('No se pudo mover la foto.');</script>";
        }
    }
}

// Cambiar banner dinámico
if (isset($_POST['banner'])) {
    $nuevoBanner = $_POST['banner'];
    $sql = "UPDATE usuario SET Banner=? WHERE CI=?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("si", $nuevoBanner, $userId);
    $stmt->execute();

    $banner = $nuevoBanner;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Perfil de Usuario</title>
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<link rel="icon" href="assets/media/Isotipo.png" />
<style>
/* ... todo tu CSS anterior ... */
</style>
</head>
<body>
<div class="container">

<header>
    <a href="index.php" class="back-link"><i class="material-icons">arrow_back</i></a>
    <div class="logo-container">
        <img src="assets/media/Interfaces del software.png" alt="Logo MTC">
    </div>
</header>

<div class="profile-card">
    <div class="banner-dinamico" id="bannerDinamico" style="background: <?= htmlspecialchars($banner) ?>">
        <div class="color-menu">
            <div class="color-option" style="background:linear-gradient(135deg, #3498db, #2ecc71);" onclick="cambiarBanner('linear-gradient(135deg, #3498db, #2ecc71)')"></div>
            <div class="color-option" style="background:linear-gradient(135deg, #e74c3c, #f39c12);" onclick="cambiarBanner('linear-gradient(135deg, #e74c3c, #f39c12)')"></div>
            <div class="color-option" style="background:linear-gradient(135deg, #9b59b6, #1e8fff);" onclick="cambiarBanner('linear-gradient(135deg, #9b59b6, #1e8fff)')"></div>
            <div class="color-option" style="background:linear-gradient(135deg, #1abc9c, #84e22b);" onclick="cambiarBanner('linear-gradient(135deg, #1abc9c, #84e22b)')"></div>
        </div>
    </div>

    <form id="form-upload" action="" method="post" enctype="multipart/form-data">
        <label for="file-upload">
            <div class="profile-picture-container">
                <img src="<?= htmlspecialchars($fotoPerfil) ?>" alt="Foto de Perfil" class="profile-picture">
            </div>
        </label>
        <input id="file-upload" name="foto" type="file" onchange="document.getElementById('form-upload').submit();">
    </form>

    <div class="user-info">
        <p><strong>Usuario:</strong> <?= htmlspecialchars($user['Nombre_Completo']) ?></p>
    </div>

    <div class="action-buttons">
        <a href="cambiar_contrasena.php" class="btn">Cambiar contraseña</a>
        <a href="logout.php" class="btn">Cerrar sesión</a>
        <a href="eliminar_cuenta.php" class="btn btn-delete" onclick="return confirmarEliminacion()">Eliminar cuenta</a>
    </div>

</div>
</div>

<form id="formBanner" method="POST">
    <input type="hidden" name="banner" id="bannerInput">
</form>

<script>
function cambiarBanner(gradient) {
    document.getElementById('bannerDinamico').style.background = gradient;
    document.getElementById('bannerInput').value = gradient;
    document.getElementById('formBanner').submit();
}
function confirmarEliminacion() {
    return confirm('¿Estás seguro de que quieres eliminar tu cuenta? Esta acción es irreversible.');
}
</script>
</body>
</html>
