<?php
session_start();
require_once 'conexion.php';
require_once 'functions.php';

header('Content-Type: application/json');

try {
    if (!isset($_SESSION['id_usuario'])) {
        echo json_encode(['error' => 'Usuario no autenticado']);
        exit;
    }

    $user = getUserInfo($conn, $_SESSION['id_usuario']);
    if (!$user) {
        echo json_encode(['error' => 'Usuario no encontrado']);
        exit;
    }

    // Get profile completion status
    $profileInfo = getProfileIncompleteInfo($user);
    
    echo json_encode([
        'success' => true,
        'isProfileIncomplete' => $profileInfo['incompleto'],
        'missingFields' => $profileInfo['campos_faltantes'],
        'userType' => $user['tipo_nombre'],
        'userId' => $user['id_usuario']
    ]);

} catch (Exception $e) {
    error_log('Error validating profile: ' . $e->getMessage());
    echo json_encode(['error' => 'Error interno del servidor']);
}
?>








