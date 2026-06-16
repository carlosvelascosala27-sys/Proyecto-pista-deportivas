<?php

// Las sesiones permiten guardar datos del usuario mientras navega por la web
// Es como una "memoria temporal" que dura hasta que cierra el navegador
session_start();

// Conectamos con la base de datos
require '../config/db.php';

// Comprobamos que el formulario se ha enviado por POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Recogemos los datos del formulario y eliminamos espacios accidentales
    $email    = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Comprobamos que no vengan vacÃ­os
    if (empty($email) || empty($password)) {
        $error = "Por favor, rellena todos los campos.";
    } else {

        // Buscamos en la BD un usuario con ese email
        // Usamos consulta preparada (?) para evitar inyecciÃ³n SQL
        $sql  = "SELECT * FROM usuarios WHERE email = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$email]);
        $usuario = $stmt->fetch(); // Devuelve la fila como array o false si no existe

        // Comprobamos si el usuario existe Y si la contraseÃ±a es correcta
        // password_verify compara la contraseÃ±a escrita con el hash guardado en la BD
        if ($usuario && password_verify($password, $usuario['password'])) {

            // Login correcto: guardamos sus datos en la sesiÃ³n
            $_SESSION['id'] = $usuario['id'];
            $_SESSION['nombre'] = $usuario['nombre'];
            $_SESSION['email'] = $usuario['email'];
            $_SESSION['rol'] = $usuario['rol'];
            $_SESSION['saldo_monedas'] = $usuario['saldo_monedas'];

            // Redirigimos segÃºn el rol
            if ($usuario['rol'] === 'admin') {
                header("Location: ../Admin/index.php");
            } else {
                header("Location: ../Principal/index.php");
            }
            exit(); // Importante: detiene el PHP despuÃ©s de redirigir

        } else {
            // Email o contraseÃ±a incorrectos
            // No decimos cuÃ¡l de los dos falla (por seguridad)
            $error = "Email o contraseÃ±a incorrectos.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Inicio SesiÃ³n</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://cdn.boxicons.com/3.0.8/fonts/basic/boxicons.min.css" rel="stylesheet">
    <link href="https://cdn.boxicons.com/3.0.8/fonts/filled/boxicons-filled.min.css" rel="stylesheet">
    <link href="https://cdn.boxicons.com/3.0.8/fonts/brands/boxicons-brands.min.css" rel="stylesheet">
</head>
<body>

    <div class="caja1">
        <form action="index.php" method="post">
            <h1>Inicio de SesiÃ³n</h1>

            <?php if (!empty($error)): ?>
                <p style="color:red; text-align:center;"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>

            <div class="box">
                <input type="email" name="email" placeholder="Correo electrÃ³nico" required
                       value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
                <i class="bx bx-user" style="color:#ffffff;"></i>
            </div>
            <div class="box">
                <input type="password" name="password" placeholder="ContraseÃ±a" required>
                <i class="bx bx-lock" style="color:#ffffff;"></i>
            </div>
            <div class="recordar">
                <label><input type="checkbox" name="recordar"> Recordar contraseÃ±a</label>
                <a href="../Recuperar/recuperar.php">Â¿Olvidaste tu contraseÃ±a?</a>
            </div>
            <button type="submit" class="boton">Iniciar sesiÃ³n</button>

            <div class="registro">
                <p>Â¿No tienes una cuenta? <a href="../registro/registro.html">RegÃ­strate aquÃ­</a></p>
            </div>
        </form>
    </div>

</body>
</html>
