<?php
// Test simple de validación
echo "<h1>Test Simple de Validación</h1>";

// Test 1: Sin sesión
echo "<h2>1. Test sin sesión</h2>";
session_destroy();
$_SESSION = array();

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://localhost/scripts/obtener_datos_usuario.php');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIE, session_name() . '=' . session_id());
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "<p>HTTP Code: " . $httpCode . "</p>";
echo "<p>Response: " . htmlspecialchars($response) . "</p>";

// Test 2: Con sesión
echo "<h2>2. Test con sesión</h2>";
session_start();
$_SESSION['id_usuario'] = 1; // Cambiar por un ID válido

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://localhost/scripts/obtener_datos_usuario.php');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIE, session_name() . '=' . session_id());
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "<p>HTTP Code: " . $httpCode . "</p>";
echo "<p>Response: " . htmlspecialchars($response) . "</p>";

echo "<h2>Test completado</h2>";
echo "<p><a href='index.php'>Probar en el Index</a></p>";
?>








