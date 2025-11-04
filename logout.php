<?php
// ¡Siempre iniciar la sesión, incluso para destruirla!
session_start();

// 1. session_unset()
// Borra todas las variables de sesión (como $_SESSION['usuario_id'])
session_unset();

// 2. session_destroy()
// Destruye la sesión activa en el servidor
session_destroy();

// 3. Redirigir al login
// Mandamos al usuario de vuelta a la puerta de entrada
header("Location: login.php");

// 4. exit()
// Nos aseguramos de que no se ejecute nada más
exit();

// Este archivo no necesita nada de HTML. Es pura lógica.
?>