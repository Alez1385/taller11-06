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
            'error' => 'Debes crear una cuenta o iniciar sesión para inscribirte a cursos.',
            'redirect' => '/login/login.php'
        ]);
        exit;
    }

    // Verificar que el usuario existe en la base de datos
    $user = getUserInfo($conn, $_SESSION['id_usuario']);
    if (!$user) {
        // Si el usuario no existe, limpiar la sesión
        session_destroy();
        echo json_encode([
            'error' => 'Usuario no encontrado. Por favor, inicia sesión nuevamente.',
            'redirect' => '/login/login.php'
        ]);
        exit;
    }

    // Verificar que la sesión no haya expirado
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 3600)) {
        // Sesión expirada (1 hora)
        session_destroy();
        echo json_encode([
            'error' => 'Tu sesión ha expirado. Por favor, inicia sesión nuevamente.',
            'redirect' => '/login/login.php'
        ]);
        exit;
    }

    // Actualizar timestamp de actividad
    $_SESSION['last_activity'] = time();

    // Get profile completion status
    $profileInfo = getProfileIncompleteInfo($user);
    
    // Check if user type is 'user' and profile is incomplete
    if ($user['tipo_nombre'] === 'user' && $profileInfo['incompleto']) {
        echo json_encode([
            'error' => 'No puedes inscribirte a cursos hasta completar tu perfil.',
            'redirect' => '/models/perfil/perfil.php',
            'missingFields' => $profileInfo['campos_faltantes']
        ]);
        exit;
    }

    echo json_encode([
        'success' => true,
        'canEnroll' => true,
        'user' => [
            'id' => $user['id_usuario'],
            'nombre' => $user['nombre'],
            'tipo_usuario' => $user['tipo_nombre']
        ]
    ]);

} catch (Exception $e) {
    error_log('Error validating enrollment: ' . $e->getMessage());
    // En caso de error, limpiar sesión
    session_destroy();
    echo json_encode([
        'error' => 'Error interno del servidor'
    ]);
}
?>
