<?php
require_once '../../scripts/conexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_profesor = $_POST['id_profesor'];
    $dia = $_POST['dia'];
    $hora_inicio = $_POST['hora_inicio'];
    $hora_fin = $_POST['hora_fin'];
    $id_horario = isset($_POST['id_horario']) ? intval($_POST['id_horario']) : 0; // Para edición

    // Validar que las horas estén en el rango permitido (6:00 AM - 2:00 PM)
    if (strtotime($hora_inicio) < strtotime('06:00') || strtotime($hora_fin) > strtotime('14:00')) {
        echo json_encode(['disponible' => false, 'error' => 'Las horas deben estar entre las 6:00 AM y las 2:00 PM']);
        exit;
    }

    // Validar que la hora de inicio sea menor que la hora de fin
    if (strtotime($hora_inicio) >= strtotime($hora_fin)) {
        echo json_encode(['disponible' => false, 'error' => 'La hora de inicio debe ser menor que la hora de fin']);
        exit;
    }

    // Consulta para verificar conflictos de horarios
    $sql = "SELECT id_horario, $dia FROM horarios 
            WHERE id_profesor = ? 
            AND $dia IS NOT NULL 
            AND $dia != ''";
    
    // Si estamos editando, excluir el horario actual
    if ($id_horario > 0) {
        $sql .= " AND id_horario != ?";
    }

    $stmt = $conn->prepare($sql);
    if ($id_horario > 0) {
        $stmt->bind_param("ii", $id_profesor, $id_horario);
    } else {
        $stmt->bind_param("i", $id_profesor);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();

    $conflicto = false;
    while ($row = $result->fetch_assoc()) {
        $horario_existente = $row[$dia];
        if (!empty($horario_existente)) {
            list($h_inicio_existente, $h_fin_existente) = explode(' - ', $horario_existente);
            
            // Verificar si hay superposición de horarios
            if ((strtotime($hora_inicio) < strtotime($h_fin_existente) && strtotime($hora_fin) > strtotime($h_inicio_existente))) {
                $conflicto = true;
                break;
            }
        }
    }

    $stmt->close();
    echo json_encode(['disponible' => !$conflicto]);
} else {
    echo json_encode(['error' => 'Método no permitido']);
}