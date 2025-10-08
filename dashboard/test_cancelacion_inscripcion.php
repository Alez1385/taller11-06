<?php
require_once '../scripts/conexion.php';
require_once '../scripts/functions.php';
require_once '../scripts/auth.php';

// Ensure user is logged in
requireLogin();

// Get user details
$id_usuario = $_SESSION['id_usuario'] ?? '';
$user = getUserInfo($conn, $id_usuario);

echo "<h2>🧪 Test de Cancelación de Inscripciones</h2>";

// Verificar si es estudiante
if ($user['tipo_nombre'] !== 'estudiante') {
    echo "<p style='color: red;'>❌ Este test es solo para estudiantes. Tu tipo de usuario es: " . $user['tipo_nombre'] . "</p>";
    exit;
}

echo "<h3>👤 Usuario Actual:</h3>";
echo "<p><strong>ID:</strong> " . $user['id_usuario'] . "</p>";
echo "<p><strong>Nombre:</strong> " . $user['nombre'] . " " . $user['apellido'] . "</p>";

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

// Mostrar TODAS las inscripciones del estudiante (incluyendo canceladas)
echo "<h3>📋 Todas las Inscripciones (Incluyendo Canceladas):</h3>";
$sql_todas_inscripciones = "SELECT i.*, c.nombre_curso 
                           FROM inscripciones i 
                           JOIN cursos c ON i.id_curso = c.id_curso 
                           WHERE i.id_estudiante = ? 
                           ORDER BY i.fecha_inscripcion DESC";
$stmt_todas_inscripciones = $conn->prepare($sql_todas_inscripciones);
$stmt_todas_inscripciones->bind_param("i", $id_estudiante);
$stmt_todas_inscripciones->execute();
$result_todas_inscripciones = $stmt_todas_inscripciones->get_result();

if ($result_todas_inscripciones->num_rows > 0) {
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr style='background: #f8f9fa;'><th>ID</th><th>Curso</th><th>Estado</th><th>Fecha Inscripción</th><th>Última Actualización</th><th>Acción</th></tr>";
    while ($row = $result_todas_inscripciones->fetch_assoc()) {
        $color = 'black';
        if ($row['estado'] == 'aprobada') $color = 'green';
        elseif ($row['estado'] == 'pendiente') $color = 'orange';
        elseif ($row['estado'] == 'cancelada') $color = 'red';
        elseif ($row['estado'] == 'rechazada') $color = 'red';
        
        echo "<tr>";
        echo "<td>" . $row['id_inscripcion'] . "</td>";
        echo "<td>" . htmlspecialchars($row['nombre_curso']) . "</td>";
        echo "<td style='color: $color; font-weight: bold;'>" . $row['estado'] . "</td>";
        echo "<td>" . $row['fecha_inscripcion'] . "</td>";
        echo "<td>" . $row['fecha_actualizacion'] . "</td>";
        echo "<td>";
        if ($row['estado'] == 'pendiente' || $row['estado'] == 'aprobada') {
            echo "<button onclick='cancelarInscripcion(" . $row['id_inscripcion'] . ")' style='background: #dc3545; color: white; border: none; padding: 5px 10px; border-radius: 3px; cursor: pointer;'>Cancelar</button>";
        } else {
            echo "<span style='color: #6c757d;'>No disponible</span>";
        }
        echo "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>No tienes inscripciones.</p>";
}

// Mostrar TODAS las preinscripciones del estudiante (incluyendo canceladas)
echo "<h3>📝 Todas las Preinscripciones (Incluyendo Canceladas):</h3>";
$sql_todas_preinscripciones = "SELECT p.*, c.nombre_curso 
                              FROM preinscripciones p 
                              JOIN cursos c ON p.id_curso = c.id_curso 
                              WHERE p.id_usuario = ? 
                              ORDER BY p.fecha_preinscripcion DESC";
$stmt_todas_preinscripciones = $conn->prepare($sql_todas_preinscripciones);
$stmt_todas_preinscripciones->bind_param("i", $id_usuario);
$stmt_todas_preinscripciones->execute();
$result_todas_preinscripciones = $stmt_todas_preinscripciones->get_result();

if ($result_todas_preinscripciones->num_rows > 0) {
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr style='background: #f8f9fa;'><th>ID</th><th>Curso</th><th>Estado</th><th>Fecha Preinscripción</th><th>Acción</th></tr>";
    while ($row = $result_todas_preinscripciones->fetch_assoc()) {
        $color = 'black';
        if ($row['estado'] == 'pendiente') $color = 'orange';
        elseif ($row['estado'] == 'cancelada') $color = 'red';
        elseif ($row['estado'] == 'rechazada') $color = 'red';
        
        echo "<tr>";
        echo "<td>" . $row['id_preinscripcion'] . "</td>";
        echo "<td>" . htmlspecialchars($row['nombre_curso']) . "</td>";
        echo "<td style='color: $color; font-weight: bold;'>" . $row['estado'] . "</td>";
        echo "<td>" . $row['fecha_preinscripcion'] . "</td>";
        echo "<td>";
        if ($row['estado'] == 'pendiente') {
            echo "<button onclick='cancelarPreinscripcion(" . $row['id_preinscripcion'] . ")' style='background: #dc3545; color: white; border: none; padding: 5px 10px; border-radius: 3px; cursor: pointer;'>Cancelar</button>";
        } else {
            echo "<span style='color: #6c757d;'>No disponible</span>";
        }
        echo "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>No tienes preinscripciones.</p>";
}

// Mostrar cursos disponibles después de las cancelaciones
echo "<h3>📚 Cursos Disponibles (Después de Cancelaciones):</h3>";
$sql_cursos_disponibles = "SELECT c.*, cc.nombre_categoria,
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
$stmt_cursos_disponibles = $conn->prepare($sql_cursos_disponibles);
$stmt_cursos_disponibles->bind_param("ii", $id_estudiante, $id_usuario);
$stmt_cursos_disponibles->execute();
$result_cursos_disponibles = $stmt_cursos_disponibles->get_result();

if ($result_cursos_disponibles->num_rows > 0) {
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr style='background: #f8f9fa;'><th>Curso</th><th>Categoría</th><th>Estado Inscripción</th><th>Estado Preinscripción</th><th>Puede Inscribirse</th></tr>";
    while ($row = $result_cursos_disponibles->fetch_assoc()) {
        $puede_inscribirse = (($row['estado_inscripcion'] === null || 
                              $row['estado_inscripcion'] === 'rechazada' || 
                              $row['estado_inscripcion'] === 'cancelada') &&
                             ($row['estado_preinscripcion'] === null || 
                              $row['estado_preinscripcion'] === 'rechazada' || 
                              $row['estado_preinscripcion'] === 'cancelada'));
        
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['nombre_curso']) . "</td>";
        echo "<td>" . htmlspecialchars($row['nombre_categoria']) . "</td>";
        echo "<td>" . ($row['estado_inscripcion'] ?? 'N/A') . "</td>";
        echo "<td>" . ($row['estado_preinscripcion'] ?? 'N/A') . "</td>";
        echo "<td style='color: " . ($puede_inscribirse ? 'green' : 'red') . "; font-weight: bold;'>" . ($puede_inscribirse ? 'SÍ' : 'NO') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>No hay cursos disponibles para inscribirse.</p>";
}

echo "<h3>🔗 Enlaces de Prueba:</h3>";
echo "<p><a href='dashboard.php' target='_blank'>Ver Dashboard del Estudiante</a></p>";
echo "<p><a href='test_dashboard_estudiante.php' target='_blank'>Ver Test Completo del Dashboard</a></p>";

?>

<script>
function cancelarInscripcion(idInscripcion) {
    if (confirm('¿Estás seguro de que quieres cancelar esta inscripción?')) {
        fetch('../inscripcion/scripts/cancelar_inscripcion.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'id_inscripcion=' + idInscripcion
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Inscripción cancelada exitosamente');
                location.reload();
            } else {
                alert('Error al cancelar la inscripción: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al cancelar la inscripción');
        });
    }
}

function cancelarPreinscripcion(idPreinscripcion) {
    if (confirm('¿Estás seguro de que quieres cancelar esta preinscripción?')) {
        fetch('../inscripcion/scripts/cancelar_preinscripcion.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'id_preinscripcion=' + idPreinscripcion
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Preinscripción cancelada exitosamente');
                location.reload();
            } else {
                alert('Error al cancelar la preinscripción: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al cancelar la preinscripción');
        });
    }
}
</script>

<?php
$conn->close();
?>
