<?php
// Script para limpiar sesiones y cookies para testing
session_start();

echo "<h1>Limpiando Sesión y Cookies</h1>";

// Destruir la sesión
session_destroy();

// Limpiar cookies
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
    echo "<p>Cookie de sesión eliminada</p>";
}

// Limpiar otras cookies relacionadas
$cookies_to_clear = ['remember_token', 'user_id', 'auth_token'];
foreach ($cookies_to_clear as $cookie) {
    if (isset($_COOKIE[$cookie])) {
        setcookie($cookie, '', time() - 3600, '/');
        echo "<p>Cookie $cookie eliminada</p>";
    }
}

echo "<p>Sesión y cookies limpiadas. Ahora puedes probar la validación sin estar logueado.</p>";
echo "<p><a href='index.php'>Volver al Index</a></p>";
echo "<p><a href='test_validation.php'>Probar Validación</a></p>";
?>








