<?php
require_once '../../scripts/auth.php';
require_once '../../scripts/conexion.php';
require_once '../../scripts/config.php';
requireLogin();

echo "<h2>Verificación Completa del Profesor</h2>";

// Obtener el ID del profesor del usuario actual
$id_usuario = $_SESSION['user_id'];
echo "<p><strong>ID Usuario:</strong> " . $id_usuario . "</p>";

// 1. Verificar si el usuario es profesor
echo "<h3>1. Verificación de Profesor</h3>";
$sql_profesor = "SELECT p.id_profesor, u.nombre, u.apellido, u.telefono
                 FROM profesor p 
                 JOIN usuario u ON p.id_usuario = u.id_usuario 
                 WHERE p.id_usuario = ?";
$stmt_profesor = $conn->prepare($sql_profesor);
$stmt_profesor->bind_param("i", $id_usuario);
$stmt_profesor->execute();
$result_profesor = $stmt_profesor->get_result();
$profesor = $result_profesor->fetch_assoc();

if ($profesor) {
    echo "<p style='color: green;'>✅ Usuario es profesor</p>";
    echo "<p><strong>Nombre:</strong> " . $profesor['nombre'] . " " . $profesor['apellido'] . "</p>";
    echo "<p><strong>ID Profesor:</strong> " . $profesor['id_profesor'] . "</p>";
    echo "<p><strong>Teléfono:</strong> " . ($profesor['telefono'] ?? 'No disponible') . "</p>";
} else {
    echo "<p style='color: red;'>❌ El usuario no es un profesor</p>";
    exit;
}

// 2. Verificar horarios asignados
echo "<h3>2. Verificación de Horarios</h3>";
$sql_horarios = "SELECT h.id_horario, c.nombre_curso, c.descripcion, c.duracion,
                        h.lunes, h.martes, h.miercoles, h.jueves, h.viernes, h.sabado,
                        h.fecha_creacion
                 FROM horarios h
                 JOIN cursos c ON h.id_curso = c.id_curso
                 WHERE h.id_profesor = ?
                 ORDER BY h.fecha_creacion DESC";
$stmt_horarios = $conn->prepare($sql_horarios);
$stmt_horarios->bind_param("i", $profesor['id_profesor']);
$stmt_horarios->execute();
$result_horarios = $stmt_horarios->get_result();
$horarios = $result_horarios->fetch_all(MYSQLI_ASSOC);

echo "<p><strong>Horarios encontrados:</strong> " . count($horarios) . "</p>";

if (count($horarios) > 0) {
    echo "<p style='color: green;'>✅ El profesor tiene horarios asignados</p>";
    
    echo "<h4>Detalles de Horarios:</h4>";
    foreach ($horarios as $index => $horario) {
        echo "<div style='border: 1px solid #ddd; padding: 15px; margin: 10px 0; background: #f9f9f9;'>";
        echo "<h5>Curso " . ($index + 1) . ": " . htmlspecialchars($horario['nombre_curso']) . "</h5>";
        echo "<p><strong>Descripción:</strong> " . htmlspecialchars($horario['descripcion']) . "</p>";
        echo "<p><strong>Duración:</strong> " . htmlspecialchars($horario['duracion']) . " semanas</p>";
        echo "<p><strong>Fecha de creación:</strong> " . $horario['fecha_creacion'] . "</p>";
        
        echo "<h6>Horarios por día:</h6>";
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
    echo "<p><a href='horario_profesor.php?id_profesor=" . $profesor['id_profesor'] . "' target='_blank'>Ver horario completo</a></p>";
    echo "<p><a href='test_horarios.php' target='_blank'>Test de horarios</a></p>";
    
} else {
    echo "<p style='color: red;'>❌ No se encontraron horarios asignados para este profesor</p>";
    echo "<p>Para asignar horarios:</p>";
    echo "<ol>";
    echo "<li>Ve a la administración</li>";
    echo "<li>Accede a 'Asignación de Horarios'</li>";
    echo "<li>Selecciona un curso y asigna horarios para este profesor</li>";
    echo "</ol>";
}

// 3. Verificar estructura de la base de datos
echo "<h3>3. Verificación de Base de Datos</h3>";
$tablas_importantes = ['usuario', 'profesor', 'cursos', 'horarios'];
foreach ($tablas_importantes as $tabla) {
    $sql_count = "SELECT COUNT(*) as total FROM $tabla";
    $result_count = $conn->query($sql_count);
    if ($result_count) {
        $count = $result_count->fetch_assoc()['total'];
        echo "<p><strong>$tabla:</strong> $count registros</p>";
    } else {
        echo "<p style='color: red;'><strong>$tabla:</strong> Error al consultar</p>";
    }
}

$conn->close();
?>
