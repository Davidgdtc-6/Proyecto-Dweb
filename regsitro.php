<?php
// ¡Iniciamos sesión siempre al principio!
session_start();

// 1. Incluimos la conexión a la BD
require 'db-connect.php';

// Variable para guardar mensajes de error o éxito
$mensaje = "";

// 2. Verificamos si el formulario fue enviado (si el método es POST)
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 3. Recibir y limpiar (validación básica) los datos
    // trim() quita espacios en blanco al inicio y al final
    $nombre = trim($_POST['nombre']);
    $correo = trim($_POST['correo']);
    $contrasena = trim($_POST['contrasena']);

    // 4. Validaciones de servidor (¡Requisito de Seguridad!)
    if (empty($nombre) || empty($correo) || empty($contrasena)) {
        $mensaje = "Error: Todos los campos son obligatorios.";
    } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $mensaje = "Error: El formato del correo no es válido.";
    } else {
        
        // 5. ¡Seguridad! Verificar si el correo ya existe
        // Usamos "consultas preparadas" (prepared statements) para evitar Inyección SQL
        $stmt = $conn->prepare("SELECT id FROM usuarios WHERE correo = ?");
        $stmt->bind_param("s", $correo); // "s" significa que es un string
        $stmt->execute();
        $stmt->store_result(); // Necesario para poder contar las filas

        if ($stmt->num_rows > 0) {
            $mensaje = "Error: Este correo electrónico ya está registrado.";
        } else {
            // 6. ¡Seguridad CLAVE! Hashear la contraseña
            // Esto cumple el requisito de "Uso correcto de hash" de la rúbrica.
            // NUNCA guardes contraseñas en texto plano.
            $hash_contrasena = password_hash($contrasena, PASSWORD_DEFAULT);

            // 7. Insertar el nuevo usuario en la BD (con la contraseña hasheada)
            $stmt_insert = $conn->prepare("INSERT INTO usuarios (nombre, correo, contrasena) VALUES (?, ?, ?)");
            // "sss" = tres strings (nombre, correo, hash)
            $stmt_insert->bind_param("sss", $nombre, $correo, $hash_contrasena);

            if ($stmt_insert->execute()) {
                $mensaje = "¡Registro exitoso! Ya puedes iniciar sesión.";
            } else {
                $mensaje = "Error en el registro. Inténtalo de nuevo.";
            }
            $stmt_insert->close();
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
    <title>Registro de Usuario</title>
    <style>
        body { font-family: sans-serif; display: grid; place-items: center; min-height: 90vh; background-color: #f4f4f4; }
        form { background: #fff; border: 1px solid #ccc; padding: 25px; border-radius: 8px; }
        div { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input { width: 300px; padding: 8px; border: 1px solid #ddd; border-radius: 4px; }
        button { width: 100%; padding: 10px; background-color: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background-color: #0056b3; }
        .mensaje { padding: 10px; margin-bottom: 15px; border-radius: 4px; text-align: center; }
        .error { background-color: #f8d7da; color: #721c24; }
        .exito { background-color: #d4edda; color: #155724; }
    </style>
</head>
<body>

    <div>
        <h2>Regístrate</h2>

        <?php if (!empty($mensaje)): ?>
            <p class="mensaje <?php echo (strpos($mensaje, 'Error') !== false) ? 'error' : 'exito'; ?>">
                <?php echo $mensaje; ?>
            </p>
        <?php endif; ?>

        <form action="registro.php" method="POST">
            <div>
                <label for="nombre">Nombre:</label>
                <input type="text" id="nombre" name="nombre" required>
            </div>
            <div>
                <label for="correo">Correo Electrónico:</label>
                <input type="email" id="correo" name="correo" required>
            </div>
            <div>
                <label for="contrasena">Contraseña:</label>
                <input type="password" id="contrasena" name="contrasena" required>
            </div>
            <button type="submit">Registrarse</button>
        </form>
        
        <p>¿Ya tienes cuenta? <a href="login.php">Inicia sesión aquí</a></p>
    </div>

</body>
</html>