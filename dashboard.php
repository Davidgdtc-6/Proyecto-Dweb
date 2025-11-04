<?php
// ¡La sesión siempre primero!
session_start();

// 1. ¡Control de Acceso! (El requisito de la rúbrica)
// Verificamos si la variable de sesión 'usuario_id' NO está seteada.
if (!isset($_SESSION['usuario_id'])) {
    
    // Si no hay sesión, lo redirigimos a la página de login
    header("Location: login.php");
    
    // Detenemos el script para que no cargue el resto del HTML
    exit();
}

// 2. Si el script llega hasta aquí, significa que el usuario SÍ está logueado.
// Podemos saludarlo por su nombre, que guardamos en la sesión.
$nombre_usuario = $_SESSION['usuario_nombre'];

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Dashboard</title>
    <style>
        body { font-family: sans-serif; margin: 20px; background-color: #f9f9f9; }
        .container { max-width: 800px; margin: auto; padding: 20px; background: #fff; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        h1 { color: #333; }
        a.logout { display: inline-block; margin-top: 20px; padding: 10px 15px; background-color: #dc3545; color: white; text-decoration: none; border-radius: 4px; }
        a.logout:hover { background-color: #c82333; }
    </style>
</head>
<body>
    <div class="container">
        <h1>¡Bienvenido, <?php echo htmlspecialchars($nombre_usuario); ?>!</h1>
        
        <p>Esta es tu página protegida. Solo los usuarios con una sesión activa pueden ver esto.</p>
        <p>Has completado el sistema de Login y Sesiones. 🚀</p>
        
        <a href="logout.php" class="logout">Cerrar Sesión</a>
    </div>
</body>
</html>