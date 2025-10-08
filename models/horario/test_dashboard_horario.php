<?php
require_once '../../scripts/auth.php';
require_once '../../scripts/conexion.php';
require_once '../../scripts/config.php';
requireLogin();

echo "<h2>Test de Dashboard - Horario del Profesor</h2>";

// Obtener el ID del profesor del usuario actual
$id_usuario = $_SESSION['id_usuario'];
echo "<p><strong>ID Usuario:</strong> " . $id_usuario . "</p>";

// 1. Verificar si el usuario es profesor
$sql_profesor = "SELECT p.id_profesor, u.nombre, u.apellido
                 FROM profesor p 
                 JOIN usuario u ON p.id_usuario = u.id_usuario 
                 WHERE p.id_usuario = ?";
$stmt_profesor = $conn->prepare($sql_profesor);
$stmt_profesor->bind_param("i", $id_usuario);
$stmt_profesor->execute();
$result_profesor = $stmt_profesor->get_result();
$profesor = $result_profesor->fetch_assoc();

if ($profesor) {
    echo "<p style='color: green;'>✅ Usuario es profesor: " . $profesor['nombre'] . " " . $profesor['apellido'] . "</p>";
    echo "<p><strong>ID Profesor:</strong> " . $profesor['id_profesor'] . "</p>";
    
    // 2. Simular la llamada AJAX que hace el dashboard
    echo "<h3>Simulación de AJAX get_profesor_id_by_user.php</h3>";
    
    $sql_test = "SELECT p.id_profesor FROM profesor p WHERE p.id_usuario = ?";
    $stmt_test = $conn->prepare($sql_test);
    $stmt_test->bind_param("i", $id_usuario);
    $stmt_test->execute();
    $result_test = $stmt_test->get_result();
    $profesor_test = $result_test->fetch_assoc();
    
    if ($profesor_test) {
        echo "<p style='color: green;'>✅ AJAX get_profesor_id_by_user.php funcionaría correctamente</p>";
        echo "<p><strong>Respuesta AJAX:</strong> " . json_encode(['success' => true, 'id_profesor' => $profesor_test['id_profesor']]) . "</p>";
        
        // 3. Simular la llamada AJAX get_horario_preview.php
        echo "<h3>Simulación de AJAX get_horario_preview.php</h3>";
        
        $sql_horarios = "SELECT h.id_horario, c.nombre_curso, c.descripcion, c.duracion,
                                h.lunes, h.martes, h.miercoles, h.jueves, h.viernes, h.sabado
                         FROM horarios h
                         JOIN cursos c ON h.id_curso = c.id_curso
                         WHERE h.id_profesor = ?
                         ORDER BY h.fecha_creacion DESC";
        $stmt_horarios = $conn->prepare($sql_horarios);
        $stmt_horarios->bind_param("i", $profesor_test['id_profesor']);
        $stmt_horarios->execute();
        $result_horarios = $stmt_horarios->get_result();
        $horarios = $result_horarios->fetch_all(MYSQLI_ASSOC);
        
        if (count($horarios) > 0) {
            echo "<p style='color: green;'>✅ AJAX get_horario_preview.php funcionaría correctamente</p>";
            echo "<p><strong>Horarios encontrados:</strong> " . count($horarios) . "</p>";
            echo "<p><strong>Respuesta AJAX:</strong> " . json_encode(['success' => true, 'horarios' => $horarios]) . "</p>";
            
            echo "<h4>Detalles de Horarios para Dashboard:</h4>";
            foreach ($horarios as $index => $horario) {
                echo "<div style='border: 1px solid #ddd; padding: 10px; margin: 10px 0; background: #f9f9f9;'>";
                echo "<h5>Curso " . ($index + 1) . ": " . htmlspecialchars($horario['nombre_curso']) . "</h5>";
                
                $dias = ['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado'];
                $nombres_dias = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
                
                foreach ($dias as $i => $dia) {
                    $horario_dia = $horario[$dia];
                    if (!empty($horario_dia)) {
                        echo "<p><strong>" . $nombres_dias[$i] . ":</strong> " . htmlspecialchars($horario_dia) . "</p>";
                    }
                }
                echo "</div>";
            }
            
            echo "<h4>Enlaces de Prueba:</h4>";
            echo "<p><a href='../../dashboard/dashboard_profesor.php' target='_blank'>Dashboard del Profesor</a></p>";
            echo "<p><a href='horario_profesor.php?id_profesor=" . $profesor_test['id_profesor'] . "' target='_blank'>Horario Completo</a></p>";
            
        } else {
            echo "<p style='color: red;'>❌ No se encontraron horarios para el profesor</p>";
            echo "<p>Necesitas asignar horarios desde la administración.</p>";
        }
        
    } else {
        echo "<p style='color: red;'>❌ Error en AJAX get_profesor_id_by_user.php</p>";
    }
    
} else {
    echo "<p style='color: red;'>❌ El usuario no es un profesor</p>";
}

$conn->close();
?>
