<?php
session_start();
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nombre = trim($_POST['nombre']);
    $apellido = trim($_POST['apellido']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    if (empty($nombre) || empty($apellido) || empty($email) || empty($password)) {
        $error = "Por favor, rellena todos los campos.";

    } elseif ($password !== $confirm_password) {
        $error = "Las contraseÃ±as no coinciden.";

    } elseif (strlen($password) < 6) {
        $error = "La contraseÃ±a debe tener al menos 6 caracteres.";

    } else {

        $sql  = "SELECT id FROM usuarios WHERE email = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$email]);

        if ($stmt->fetch()) {
            $error = "Ese email ya estÃ¡ registrado.";
        } else {

            $hash = password_hash($password, PASSWORD_DEFAULT);

            $sql  = "INSERT INTO usuarios (nombre, apellidos, email, password) VALUES (?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$nombre, $apellido, $email, $hash]);

            $_SESSION['id'] = $pdo->lastInsertId();
            $_SESSION['nombre'] = $nombre;
            $_SESSION['email'] = $email;
            $_SESSION['rol'] = 'cliente';

            header("Location: ../Principal/index.php");
            exit();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <section class="registro">
        <h1>Registro</h1>

        <?php if (!empty($error)): ?>
            <p style="color:red; text-align:center;"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <form action="registro.php" method="post">
            <input class="controls" type="text" name="nombre" placeholder="Ingrese su nombre" required>
            <input class="controls" type="text" name="apellido" placeholder="Ingrese su apellido" required>
            <input class="controls" type="email" name="email" placeholder="Ingrese su correo electrÃ³nico" required>
            <input class="controls" type="password" name="password" placeholder="Ingrese su contraseÃ±a" required>
            <input class="controls" type="password" name="confirm_password" placeholder="Confirmar contraseÃ±a" required>
            <button class="boton" type="submit">Registrarse</button>
            <p><a href="../Login/login.html">Â¿Ya tienes una cuenta?</a></p>
        </form>
    </section>
</body>
</html>
