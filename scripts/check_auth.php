<?php
session_start();
require_once 'conexion.php';
require_once 'functions.php';

header('Content-Type: application/json');

try {
    // Verificar si la sesión está activa y válida
    if (!isset($_SESSION['id_usuario']) || empty($_SESSION['id_usuario'])) {
        // Limpiar sesión si no hay usuario
        session_destroy();
        echo json_encode([
            'authenticated' => false,
            'message' => 'Usuario no autenticado'
        ]);
        exit;
    }

    // Verificar que el usuario existe en la base de datos
    $user = getUserInfo($conn, $_SESSION['id_usuario']);
    if (!$user) {
        // Si el usuario no existe, limpiar la sesión
        session_destroy();
        echo json_encode([
            'authenticated' => false,
            'message' => 'Usuario no encontrado'
        ]);
        exit;
    }

    // Verificar que la sesión no haya expirado (opcional: agregar timestamp)
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 3600)) {
        // Sesión expirada (1 hora)
        session_destroy();
        echo json_encode([
            'authenticated' => false,
            'message' => 'Sesión expirada'
        ]);
        exit;
    }

    // Actualizar timestamp de actividad
    $_SESSION['last_activity'] = time();

    // Get profile completion status
    $profileInfo = getProfileIncompleteInfo($user);
    
    echo json_encode([
        'authenticated' => true,
        'user' => [
            'id' => $user['id_usuario'],
            'nombre' => $user['nombre'],
            'username' => $user['username'],
            'tipo_usuario' => $user['tipo_nombre'],
            'id_tipo_usuario' => $user['id_tipo_usuario'],
            'perfil_incompleto' => $profileInfo['incompleto'],
            'missingFields' => $profileInfo['campos_faltantes']
        ]
    ]);

} catch (Exception $e) {
    error_log('Error checking auth: ' . $e->getMessage());
    // En caso de error, limpiar sesión
    session_destroy();
    echo json_encode([
        'authenticated' => false,
        'message' => 'Error interno del servidor'
    ]);
}
?>
