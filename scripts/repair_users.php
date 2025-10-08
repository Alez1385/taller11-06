<?php
// Script de reparación masiva de usuarios
// Ejecutar: http://[TU_DOMINIO_O_LOCALHOST]/scripts/repair_users.php

require_once __DIR__ . '/conexion.php';

header('Content-Type: text/html; charset=utf-8');

$sql = "SELECT u.id_usuario, u.username, u.id_tipo_usuario
        FROM usuario u
        JOIN estudiante e ON u.id_usuario = e.id_usuario
        JOIN inscripciones i ON e.id_estudiante = i.id_estudiante
        WHERE u.id_tipo_usuario = 4
          AND e.estado = 'activo'
          AND i.estado = 'aprobada'
        GROUP BY u.id_usuario";
$res = $conn->query($sql);
$cambiados = 0;
$usuarios = [];
while ($row = $res->fetch_assoc()) {
    $upd = $conn->prepare("UPDATE usuario SET id_tipo_usuario = 3 WHERE id_usuario = ?");
    $upd->bind_param("i", $row['id_usuario']);
    if ($upd->execute()) {
        $cambiados++;
        $usuarios[] = $row['username'] . ' (id: ' . $row['id_usuario'] . ')';
    }
    $upd->close();
}
if ($cambiados > 0) {
    echo "<h2>Usuarios reparados: $cambiados</h2>";
    echo "<ul>";
    foreach ($usuarios as $u) {
        echo "<li>$u</li>";
    }
    echo "</ul>";
} else {
    echo "<h2>No se encontraron usuarios para reparar.</h2>";
} 