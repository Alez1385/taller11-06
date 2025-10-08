<?php
require_once '../scripts/conexion.php';
require_once '../scripts/functions.php';
require_once '../scripts/auth.php';

// Ensure user is logged in
requireLogin();

// Get user details
$id_usuario = $_SESSION['id_usuario'] ?? '';
$user = getUserInfo($conn, $id_usuario);

echo "<h2>🧪 Test Dashboard Estudiante</h2>";

// Verificar si es estudiante
if ($user['tipo_nombre'] !== 'estudiante') {
    echo "<p style='color: red;'>❌ Este test es solo para estudiantes. Tu tipo de usuario es: " . $user['tipo_nombre'] . "</p>";
    exit;
}

echo "<h3>👤 Usuario Actual:</h3>";
echo "<p><strong>ID:</strong> " . $user['id_usuario'] . "</p>";
echo "<p><strong>Nombre:</strong> " . $user['nombre'] . " " . $user['apellido'] . "</p>";
echo "<p><strong>Tipo:</strong> " . $user['tipo_nombre'] . "</p>";

// Obtener ID del estudiante
$sql_estudiante = "SELECT id_estudiante FROM estudiante WHERE id_usuario = ?";
$stmt_estudiante = $conn->prepare($sql_estudiante);
$stmt_estudiante->bind_param("i", $id_usuario);
$stmt_estudiante->execute();
$result_estudiante = $stmt_estudiante->get_result();
$estudiante = $result_estudiante->fetch_assoc();

if (!$estudiante) {
    echo "<p style='color: red;'>❌ No se encontró registro de estudiante para este usuario.</p>";
    exit;
}

$id_estudiante = $estudiante['id_estudiante'];
echo "<p><strong>ID Estudiante:</strong> " . $id_estudiante . "</p>";

// Mostrar inscripciones actuales
echo "<h3>📋 Inscripciones Actuales:</h3>";
$sql_inscripciones = "SELECT i.*, c.nombre_curso 
                     FROM inscripciones i 
                     JOIN cursos c ON i.id_curso = c.id_curso 
                     WHERE i.id_estudiante = ? 
                     ORDER BY i.fecha_inscripcion DESC";
$stmt_inscripciones = $conn->prepare($sql_inscripciones);
$stmt_inscripciones->bind_param("i", $id_estudiante);
$stmt_inscripciones->execute();
$result_inscripciones = $stmt_inscripciones->get_result();

if ($result_inscripciones->num_rows > 0) {
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr style='background: #f8f9fa;'><th>Curso</th><th>Estado</th><th>Fecha Inscripción</th><th>Última Actualización</th></tr>";
    while ($row = $result_inscripciones->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['nombre_curso']) . "</td>";
        echo "<td style='color: " . ($row['estado'] == 'aprobada' ? 'green' : ($row['estado'] == 'pendiente' ? 'orange' : 'red')) . ";'>" . $row['estado'] . "</td>";
        echo "<td>" . $row['fecha_inscripcion'] . "</td>";
        echo "<td>" . $row['fecha_actualizacion'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>No tienes inscripciones.</p>";
}

// Mostrar preinscripciones actuales
echo "<h3>📝 Preinscripciones Actuales:</h3>";
$sql_preinscripciones = "SELECT p.*, c.nombre_curso 
                        FROM preinscripciones p 
                        JOIN cursos c ON p.id_curso = c.id_curso 
                        WHERE p.id_usuario = ? 
                        ORDER BY p.fecha_preinscripcion DESC";
$stmt_preinscripciones = $conn->prepare($sql_preinscripciones);
$stmt_preinscripciones->bind_param("i", $id_usuario);
$stmt_preinscripciones->execute();
$result_preinscripciones = $stmt_preinscripciones->get_result();

if ($result_preinscripciones->num_rows > 0) {
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr style='background: #f8f9fa;'><th>Curso</th><th>Estado</th><th>Fecha Preinscripción</th></tr>";
    while ($row = $result_preinscripciones->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['nombre_curso']) . "</td>";
        echo "<td style='color: " . ($row['estado'] == 'pendiente' ? 'orange' : 'red') . ";'>" . $row['estado'] . "</td>";
        echo "<td>" . $row['fecha_preinscripcion'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>No tienes preinscripciones.</p>";
}

// Mostrar cursos disponibles (con la nueva lógica)
echo "<h3>📚 Cursos Disponibles (Nueva Lógica):</h3>";
$sql_cursos = "SELECT c.*, cc.nombre_categoria,
               GROUP_CONCAT(DISTINCT CONCAT(h.dia_semana, ' ', h.hora_inicio, '-', h.hora_fin) SEPARATOR ', ') AS horarios,
               i.estado AS estado_inscripcion,
               p.estado AS estado_preinscripcion
               FROM cursos c
               LEFT JOIN categoria_curso cc ON c.id_categoria = cc.id_categoria
               LEFT JOIN horarios h ON c.id_curso = h.id_curso
               LEFT JOIN (
                   SELECT i1.* 
                   FROM inscripciones i1
                   WHERE i1.id_estudiante = ?
                   AND i1.id_inscripcion = (
                       SELECT MAX(i2.id_inscripcion) 
                       FROM inscripciones i2 
                       WHERE i2.id_curso = i1.id_curso 
                       AND i2.id_estudiante = i1.id_estudiante
                   )
               ) i ON c.id_curso = i.id_curso
               LEFT JOIN (
                   SELECT p1.* 
                   FROM preinscripciones p1
                   WHERE p1.id_usuario = ?
                   AND p1.id_preinscripcion = (
                       SELECT MAX(p2.id_preinscripcion) 
                       FROM preinscripciones p2 
                       WHERE p2.id_curso = p1.id_curso 
                       AND p2.id_usuario = p1.id_usuario
                   )
               ) p ON c.id_curso = p.id_curso
               WHERE c.estado = 'activo'
               AND (i.estado IS NULL OR i.estado IN ('rechazada', 'cancelada'))
               AND (p.estado IS NULL OR p.estado IN ('rechazada', 'cancelada'))
               GROUP BY c.id_curso
               ORDER BY c.nombre_curso";
$stmt_cursos = $conn->prepare($sql_cursos);
$stmt_cursos->bind_param("ii", $id_estudiante, $id_usuario);
$stmt_cursos->execute();
$result_cursos = $stmt_cursos->get_result();

if ($result_cursos->num_rows > 0) {
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr style='background: #f8f9fa;'><th>Curso</th><th>Categoría</th><th>Nivel</th><th>Duración</th><th>Horarios</th><th>Estado Inscripción</th><th>Estado Preinscripción</th><th>Puede Inscribirse</th></tr>";
    while ($row = $result_cursos->fetch_assoc()) {
        $puede_inscribirse = (($row['estado_inscripcion'] === null || 
                              $row['estado_inscripcion'] === 'rechazada' || 
                              $row['estado_inscripcion'] === 'cancelada') &&
                             ($row['estado_preinscripcion'] === null || 
                              $row['estado_preinscripcion'] === 'rechazada' || 
                              $row['estado_preinscripcion'] === 'cancelada'));
        
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['nombre_curso']) . "</td>";
        echo "<td>" . htmlspecialchars($row['nombre_categoria']) . "</td>";
        echo "<td>" . htmlspecialchars($row['nivel_educativo']) . "</td>";
        echo "<td>" . $row['duracion'] . " semanas</td>";
        echo "<td>" . htmlspecialchars($row['horarios'] ?? 'Sin horarios') . "</td>";
        echo "<td>" . ($row['estado_inscripcion'] ?? 'N/A') . "</td>";
        echo "<td>" . ($row['estado_preinscripcion'] ?? 'N/A') . "</td>";
        echo "<td style='color: " . ($puede_inscribirse ? 'green' : 'red') . ";'>" . ($puede_inscribirse ? 'Sí' : 'No') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>No hay cursos disponibles para inscribirse.</p>";
}

// Mostrar todos los cursos (para comparación)
echo "<h3>🔍 Todos los Cursos (Para Comparación):</h3>";
$sql_todos_cursos = "SELECT c.*, cc.nombre_categoria,
                     GROUP_CONCAT(DISTINCT CONCAT(h.dia_semana, ' ', h.hora_inicio, '-', h.hora_fin) SEPARATOR ', ') AS horarios,
                     i.estado AS estado_inscripcion,
                     p.estado AS estado_preinscripcion
                     FROM cursos c
                     LEFT JOIN categoria_curso cc ON c.id_categoria = cc.id_categoria
                     LEFT JOIN horarios h ON c.id_curso = h.id_curso
                     LEFT JOIN inscripciones i ON c.id_curso = i.id_curso AND i.id_estudiante = ?
                     LEFT JOIN preinscripciones p ON c.id_curso = p.id_curso AND p.id_usuario = ?
                     WHERE c.estado = 'activo'
                     GROUP BY c.id_curso
                     ORDER BY c.nombre_curso";
$stmt_todos_cursos = $conn->prepare($sql_todos_cursos);
$stmt_todos_cursos->bind_param("ii", $id_estudiante, $id_usuario);
$stmt_todos_cursos->execute();
$result_todos_cursos = $stmt_todos_cursos->get_result();

if ($result_todos_cursos->num_rows > 0) {
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr style='background: #f8f9fa;'><th>Curso</th><th>Estado Inscripción</th><th>Estado Preinscripción</th><th>Aparece en Disponibles</th></tr>";
    while ($row = $result_todos_cursos->fetch_assoc()) {
        $aparece_en_disponibles = (($row['estado_inscripcion'] === null || 
                                   $row['estado_inscripcion'] === 'rechazada' || 
                                   $row['estado_inscripcion'] === 'cancelada') &&
                                  ($row['estado_preinscripcion'] === null || 
                                   $row['estado_preinscripcion'] === 'rechazada' || 
                                   $row['estado_preinscripcion'] === 'cancelada'));
        
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['nombre_curso']) . "</td>";
        echo "<td>" . ($row['estado_inscripcion'] ?? 'N/A') . "</td>";
        echo "<td>" . ($row['estado_preinscripcion'] ?? 'N/A') . "</td>";
        echo "<td style='color: " . ($aparece_en_disponibles ? 'green' : 'red') . ";'>" . ($aparece_en_disponibles ? 'Sí' : 'No') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
}

echo "<h3>🔗 Enlaces de Prueba:</h3>";
echo "<p><a href='dashboard.php' target='_blank'>Ver Dashboard del Estudiante</a></p>";
echo "<p><a href='dashboard_data.php' target='_blank'>Ver Datos del Dashboard (JSON)</a></p>";

$conn->close();
?>
