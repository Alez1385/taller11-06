<?php
// Test de validación de inscripción
echo "<h1>Test de Validación de Inscripción</h1>";

// Simular diferentes estados de sesión
echo "<h2>1. Test sin sesión</h2>";
session_destroy();
$_SESSION = array();

// Simular llamada al endpoint
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://localhost/scripts/validate_enrollment.php');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIE, session_name() . '=' . session_id());
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "<p>HTTP Code: " . $httpCode . "</p>";
echo "<p>Response: " . htmlspecialchars($response) . "</p>";

// Test con sesión válida
echo "<h2>2. Test con sesión válida</h2>";
session_start();
$_SESSION['id_usuario'] = 1; // Cambiar por un ID válido

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://localhost/scripts/validate_enrollment.php');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIE, session_name() . '=' . session_id());
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "<p>HTTP Code: " . $httpCode . "</p>";
echo "<p>Response: " . htmlspecialchars($response) . "</p>";

// Test de check_auth
echo "<h2>3. Test de check_auth</h2>";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://localhost/scripts/check_auth.php');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIE, session_name() . '=' . session_id());
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "<p>HTTP Code: " . $httpCode . "</p>";
echo "<p>Response: " . htmlspecialchars($response) . "</p>";

echo "<h2>Test completado</h2>";
?>








