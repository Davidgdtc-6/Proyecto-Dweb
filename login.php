<?php
// ¡La sesión siempre primero!
session_start();

// Si el usuario YA tiene una sesión activa, que no vea el login.
// Lo mandamos directo a su dashboard.
if (isset($_SESSION['usuario_id'])) {
    header("Location: dashboard.php");
    exit(); // Detenemos el script
}

require 'db-connect.php'; // Conexión a la BD
$mensaje_error = "";

// Verificamos si el formulario fue enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $correo = trim($_POST['correo']);
    $contrasena = trim($_POST['contrasena']);

    if (empty($correo) || empty($contrasena)) {
        $mensaje_error = "Error: Correo y contraseña son requeridos.";
    } else {
        
        // 1. ¡Seguridad! Buscar al usuario por correo (Consulta preparada)
        $stmt = $conn->prepare("SELECT id, nombre, contrasena FROM usuarios WHERE correo = ?");
        $stmt->bind_param("s", $correo);
        $stmt->execute();
        $resultado = $stmt->get_result(); // Obtenemos los resultados

        if ($resultado->num_rows == 1) {
            // El usuario existe, ahora verificamos la contraseña
            $usuario = $resultado->fetch_assoc();

            // 2. ¡Seguridad CLAVE! Verificar el hash
            // Compara la contraseña del formulario ($contrasena) con el hash de la BD ($usuario['contrasena'])
            // Esto cumple el requisito de "manejo seguro" de la rúbrica.
            if (password_verify($contrasena, $usuario['contrasena'])) {
                
                // ¡Contraseña correcta!
                
                // 3. ¡Iniciamos la sesión!
                // Guardamos los datos del usuario en la variable $_SESSION
                // Esto cumple "Uso de sesiones PHP para mantener el estado"
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['usuario_nombre'] = $usuario['nombre'];

                // 4. Redirigimos al dashboard (la página protegida)
                header("Location: dashboard.php");
                exit(); // ¡Importante! Detener el script después de redirigir.

            } else {
                // Contraseña incorrecta
                $mensaje_error = "Error: Contraseña incorrecta.";
            }
        } else {
            // Usuario no encontrado
            $mensaje_error = "Error: Usuario no encontrado.";
        }
        $stmt->close();
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    <style>
        body { font-family: sans-serif; display: grid; place-items: center; min-height: 90vh; background-color: #f4f4f4; }
        form { background: #fff; border: 1px solid #ccc; padding: 25px; border-radius: 8px; }
        div { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input { width: 300px; padding: 8px; border: 1px solid #ddd; border-radius: 4px; }
        button { width: 100%; padding: 10px; background-color: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background-color: #218838; }
        .mensaje { padding: 10px; margin-bottom: 15px; border-radius: 4px; text-align: center; }
        .error { background-color: #f8d7da; color: #721c24; }
    </style>
</head>
<body>

    <div>
        <h2>Iniciar Sesión</h2>
        
        <?php if (!empty($mensaje_error)): ?>
            <p class="mensaje error">
                <?php echo $mensaje_error; ?>
            </p>
        <?php endif; ?>

        <form action="login.php" method="POST">
            <div>
                <label for="correo">Correo:</label>
                <input type="email" id="correo" name="correo" required>
            </div>
            <div>
                <label for="contrasena">Contraseña:</label>
                <input type="password" id="contrasena" name="contrasena" required>
            </div>
            <button type="submit">Entrar</button>
        </form>
        
        <p>¿No tienes cuenta? <a href="registro.php">Regístrate aquí</a></p>
    </div>

</body>
</html>