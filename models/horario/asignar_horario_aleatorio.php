<?php
require_once '../../scripts/auth.php';
require_once '../../scripts/conexion.php';
require_once '../../scripts/config.php';
requireLogin();
checkPermission('admin');

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_curso = intval($_POST['id_curso']);
    $id_profesor = intval($_POST['id_profesor']);
    
    // Limpiar cualquier horario previo del curso antes de crear uno nuevo
    $stmt_cleanup = $conn->prepare("DELETE FROM horarios WHERE id_curso = ?");
    $stmt_cleanup->bind_param("i", $id_curso);
    if (!$stmt_cleanup->execute()) {
        echo json_encode(['success' => false, 'message' => 'No se pudo limpiar horarios previos del curso: ' . $conn->error]);
        exit;
    }
    $stmt_cleanup->close();
    
    // Obtener horarios existentes del profesor
    $sql_profesor = "SELECT lunes, martes, miercoles, jueves, viernes, sabado FROM horarios WHERE id_profesor = ?";
    $stmt_profesor = $conn->prepare($sql_profesor);
    $stmt_profesor->bind_param("i", $id_profesor);
    $stmt_profesor->execute();
    $result_profesor = $stmt_profesor->get_result();
    
    $horarios_ocupados = [];
    $horas_asignadas_actuales = 0;
    while ($row = $result_profesor->fetch_assoc()) {
        foreach (['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado'] as $dia) {
            if (!empty($row[$dia])) {
                $horarios_ocupados[$dia][] = $row[$dia];
                list($hi, $hf) = explode(' - ', $row[$dia]);
                $horas_asignadas_actuales += max(0, (strtotime($hf) - strtotime($hi)) / 3600);
            }
        }
    }
    
    // Generar horario aleatorio
    $dias = ['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado'];
    $horarios_disponibles = [
        '06:00 - 08:00',
        '08:00 - 10:00', 
        '10:00 - 12:00',
        '12:00 - 14:00'
    ];
    
    $nuevo_horario = array_fill(0, 6, null);
    $dias_seleccionados = array_rand($dias, rand(2, 4)); // 2-4 días aleatorios
    
    if (!is_array($dias_seleccionados)) {
        $dias_seleccionados = [$dias_seleccionados];
    }
    
    $horas_semanales_nuevas = 0;
    foreach ($dias_seleccionados as $index) {
        $dia = $dias[$index];
        $horarios_dia_disponibles = $horarios_disponibles;
        
        // Filtrar horarios que no entren en conflicto
        if (isset($horarios_ocupados[$dia])) {
            foreach ($horarios_ocupados[$dia] as $horario_ocupado) {
                list($h_inicio_ocupado, $h_fin_ocupado) = explode(' - ', $horario_ocupado);
                
                $horarios_dia_disponibles = array_filter($horarios_dia_disponibles, function($horario) use ($h_inicio_ocupado, $h_fin_ocupado) {
                    list($h_inicio, $h_fin) = explode(' - ', $horario);
                    return !(strtotime($h_inicio) < strtotime($h_fin_ocupado) && strtotime($h_fin) > strtotime($h_inicio_ocupado));
                });
            }
        }
        
        // Si hay horarios disponibles, seleccionar uno aleatorio
        if (!empty($horarios_dia_disponibles)) {
            $slot = $horarios_dia_disponibles[array_rand($horarios_dia_disponibles)];
            list($hi, $hf) = explode(' - ', $slot);
            $dur = max(0, (strtotime($hf) - strtotime($hi)) / 3600);
            if (($horas_asignadas_actuales + $horas_semanales_nuevas + $dur) <= 8) {
                $nuevo_horario[$index] = $slot;
                $horas_semanales_nuevas += $dur;
            }
        }
    }
    
    // Insertar el horario
    $sql = "INSERT INTO horarios (id_curso, id_profesor, lunes, martes, miercoles, jueves, viernes, sabado) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iissssss", $id_curso, $id_profesor, $nuevo_horario[0], $nuevo_horario[1], $nuevo_horario[2], $nuevo_horario[3], $nuevo_horario[4], $nuevo_horario[5]);
    
    if ($stmt->execute()) {
        if ($horas_semanales_nuevas === 0) {
            echo json_encode(['success' => false, 'message' => 'El profesor ya tiene 8 horas semanales asignadas.']);
        } else {
            echo json_encode(['success' => true, 'message' => 'Horario aleatorio asignado exitosamente.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al asignar el horario: ' . $conn->error]);
    }
    
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
}

$conn->close();
?>
