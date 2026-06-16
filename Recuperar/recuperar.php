<?php
session_start();
require_once '../config/db.php';

$mensaje = '';
$email_encontrado = '';

// Paso 1: comprobar si el email existe en la base de datos
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['email'])) {
    $email = $_POST['email'];
    $consulta = $pdo->query("SELECT id FROM usuarios WHERE email = '$email'");
    $usuario = $consulta->fetch();

    if ($usuario) {
        // El email existe, guardamos el email para el siguiente paso
        $email_encontrado = $email;
    } else {
        $mensaje = 'No existe ninguna cuenta con ese email.';
    }
}

// Paso 2: cambiar la contraseña si se ha enviado el formulario con nueva contraseña
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['nueva_password'])) {
    $email = $_POST['email_hidden'];
    $nueva_password = $_POST['nueva_password'];
    $repetir_password = $_POST['repetir_password'];

    if ($nueva_password != $repetir_password) {
        $mensaje = 'Las contraseñas no coinciden.';
        $email_encontrado = $email;
    } else if (strlen($nueva_password) < 6) {
        $mensaje = 'La contraseña debe tener al menos 6 caracteres.';
        $email_encontrado = $email;
    } else {
        // Actualizamos la contraseña en la base de datos
        $password_hash = password_hash($nueva_password, PASSWORD_DEFAULT);
        $pdo->query("UPDATE usuarios SET password = '$password_hash' WHERE email = '$email'");
        $mensaje = 'Contraseña cambiada correctamente. Ya puedes iniciar sesión.';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Recuperar Contraseña</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playwrite+NZ+Basic:wght@100..400&display=swap" rel="stylesheet">
</head>
<body>
    <section class="recuperar">
        <h1>Recuperar Contraseña</h1>

        <?php
        // Si hay un mensaje lo mostramos
        if ($mensaje != '') {
            echo '<p class="mensaje">' . $mensaje . '</p>';
        }

        // Si el email fue encontrado mostramos el formulario para cambiar la contraseña
        if ($email_encontrado != '') {
            echo '<form action="recuperar.php" method="post">';
            echo '<input type="hidden" name="email_hidden" value="' . $email_encontrado . '">';
            echo '<input class="controls" type="password" name="nueva_password" placeholder="Nueva contraseña" required>';
            echo '<input class="controls" type="password" name="repetir_password" placeholder="Repetir contraseña" required>';
            echo '<button class="boton" type="submit">Cambiar contraseña</button>';
            echo '</form>';
        } else if ($mensaje == '' || $mensaje == 'No existe ninguna cuenta con ese email.') {
            // Mostramos el formulario para introducir el email
            echo '<form action="recuperar.php" method="post">';
            echo '<input class="controls" type="email" name="email" placeholder="Ingrese su correo electrónico" required>';
            echo '<button class="boton" type="submit">Buscar cuenta</button>';
            echo '</form>';
        }

        // Si la contraseña se cambio correctamente mostramos el enlace al login
        if ($mensaje == 'Contraseña cambiada correctamente. Ya puedes iniciar sesión.') {
            echo '<p><a href="../Login/login.php" class="boton">Ir al login</a></p>';
        } else {
            echo '<p><a href="../Login/login.php">¿Volver al inicio de sesión?</a></p>';
        }
        ?>

    </section>
</body>
</html>
