<?php
session_start();
require_once 'scripts/conexion.php';
require_once 'scripts/functions.php';

// Simular un usuario para testing
$_SESSION['id_usuario'] = 1; // Cambiar por un ID válido

echo "<h1>Test de Mejoras del Sidebar</h1>";

// Test 1: Verificar función getProfileIncompleteInfo
echo "<h2>Test 1: Función getProfileIncompleteInfo</h2>";
$user = getUserInfo($conn, $_SESSION['id_usuario']);
if ($user) {
    $profileInfo = getProfileIncompleteInfo($user);
    echo "<p>Usuario: " . htmlspecialchars($user['nombre'] ?: $user['username']) . "</p>";
    echo "<p>Tipo: " . htmlspecialchars($user['tipo_nombre']) . "</p>";
    echo "<p>Perfil incompleto: " . ($profileInfo['incompleto'] ? 'Sí' : 'No') . "</p>";
    if ($profileInfo['incompleto']) {
        echo "<p>Campos faltantes: " . implode(', ', $profileInfo['campos_faltantes']) . "</p>";
    }
} else {
    echo "<p>Error: No se pudo obtener información del usuario</p>";
}

// Test 2: Verificar endpoint de validación
echo "<h2>Test 2: Endpoint de validación</h2>";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://localhost/scripts/validate_enrollment.php');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIE, session_name() . '=' . session_id());
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "<p>HTTP Code: " . $httpCode . "</p>";
echo "<p>Response: " . htmlspecialchars($response) . "</p>";

// Test 3: Verificar endpoint de autenticación
echo "<h2>Test 3: Endpoint de autenticación</h2>";
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








