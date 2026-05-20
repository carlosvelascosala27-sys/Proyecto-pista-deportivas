<?php

// Las sesiones permiten guardar datos del usuario mientras navega por la web
// Es como una "memoria temporal" que dura hasta que cierra el navegador
session_start();

// Conectamos con la base de datos
require_once '../config/db.php';

// Comprobamos que el formulario se ha enviado por POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Recogemos los datos del formulario y eliminamos espacios accidentales
    $email    = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Comprobamos que no vengan vacíos
    if (empty($email) || empty($password)) {
        $error = "Por favor, rellena todos los campos.";
    } else {

        // Buscamos en la BD un usuario con ese email
        // Usamos consulta preparada (?) para evitar inyección SQL
        $sql  = "SELECT * FROM usuarios WHERE email = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$email]);
        $usuario = $stmt->fetch(); // Devuelve la fila como array o false si no existe

        // Comprobamos si el usuario existe Y si la contraseña es correcta
        // password_verify compara la contraseña escrita con el hash guardado en la BD
        if ($usuario && password_verify($password, $usuario['password'])) {

            // Login correcto: guardamos sus datos en la sesión
            $_SESSION['id']     = $usuario['id'];
            $_SESSION['nombre'] = $usuario['nombre'];
            $_SESSION['email']  = $usuario['email'];
            $_SESSION['rol']    = $usuario['rol'];

            // Redirigimos según el rol
            if ($usuario['rol'] === 'admin') {
                header("Location: ../Admin/admin.php");
            } else {
                header("Location: ../Principal/principal.php");
            }
            exit(); // Importante: detiene el PHP después de redirigir

        } else {
            // Email o contraseña incorrectos
            // No decimos cuál de los dos falla (por seguridad)
            $error = "Email o contraseña incorrectos.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Inicio Sesión</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://cdn.boxicons.com/3.0.8/fonts/basic/boxicons.min.css" rel="stylesheet">
    <link href="https://cdn.boxicons.com/3.0.8/fonts/filled/boxicons-filled.min.css" rel="stylesheet">
    <link href="https://cdn.boxicons.com/3.0.8/fonts/brands/boxicons-brands.min.css" rel="stylesheet">
</head>
<body>

    <div class="caja1">
        <form action="login.php" method="post">
            <h1>Inicio de Sesión</h1>

            <?php if (!empty($error)): ?>
                <p style="color:red; text-align:center;"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>

            <div class="box">
                <input type="email" name="email" placeholder="Correo electrónico" required
                       value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
                <i class="bx bx-user" style="color:#ffffff;"></i>
            </div>
            <div class="box">
                <input type="password" name="password" placeholder="Contraseña" required>
                <i class="bx bx-lock" style="color:#ffffff;"></i>
            </div>
            <div class="recordar">
                <label><input type="checkbox" name="recordar"> Recordar contraseña</label>
                <a href="../Recuperar/olvido.html">¿Olvidaste tu contraseña?</a>
            </div>
            <button type="submit" class="boton">Iniciar sesión</button>

            <div class="registro">
                <p>¿No tienes una cuenta? <a href="../registro/registro.html">Regístrate aquí</a></p>
            </div>
        </form>
    </div>

</body>
</html>
