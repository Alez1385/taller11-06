<?php
require_once '../../scripts/auth.php';
require_once '../../scripts/conexion.php';
require_once '../../scripts/config.php';
requireLogin();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id_profesor'])) {
    $id_profesor = intval($_POST['id_profesor']);

    // Obtener todos los horarios del profesor
    $sql = "SELECT h.id_horario, c.nombre_curso, c.descripcion, c.duracion, 
                   h.lunes, h.martes, h.miercoles, h.jueves, h.viernes, h.sabado
            FROM horarios h
            JOIN cursos c ON h.id_curso = c.id_curso
            WHERE h.id_profesor = ?
            ORDER BY h.fecha_creacion DESC";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_profesor);
    $stmt->execute();
    $result = $stmt->get_result();
    $horarios = $result->fetch_all(MYSQLI_ASSOC);
    
    if (count($horarios) > 0) {
        echo json_encode(['success' => true, 'horarios' => $horarios]);
    } else {
        echo json_encode(['success' => false, 'message' => 'No se encontraron horarios asignados']);
    }
    
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Solicitud inválida']);
}

$conn->close();
?>
