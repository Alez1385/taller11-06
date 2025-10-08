<?php
require_once '../../scripts/conexion.php';

echo "<h1>Debug de Asistencia</h1>";

// Verificar estructura de la tabla asistencia
echo "<h2>1. Estructura de la tabla asistencia</h2>";
$sql_describe = "DESCRIBE asistencia";
$result_describe = $conn->query($sql_describe);
if ($result_describe) {
    echo "<table border='1'>";
    echo "<tr><th>Campo</th><th>Tipo</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    while ($row = $result_describe->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['Field'] . "</td>";
        echo "<td>" . $row['Type'] . "</td>";
        echo "<td>" . $row['Null'] . "</td>";
        echo "<td>" . $row['Key'] . "</td>";
        echo "<td>" . $row['Default'] . "</td>";
        echo "<td>" . $row['Extra'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "Error: " . $conn->error;
}

// Verificar datos en la tabla asistencia
echo "<h2>2. Datos en la tabla asistencia</h2>";
$sql_data = "SELECT * FROM asistencia LIMIT 10";
$result_data = $conn->query($sql_data);
if ($result_data && $result_data->num_rows > 0) {
    echo "<p>Total registros: " . $result_data->num_rows . "</p>";
    echo "<table border='1'>";
    echo "<tr>";
    $fields = $result_data->fetch_fields();
    foreach ($fields as $field) {
        echo "<th>" . $field->name . "</th>";
    }
    echo "</tr>";
    
    $result_data->data_seek(0);
    while ($row = $result_data->fetch_assoc()) {
        echo "<tr>";
        foreach ($row as $value) {
            echo "<td>" . htmlspecialchars($value) . "</td>";
        }
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>No hay datos en la tabla asistencia o error: " . $conn->error . "</p>";
}

// Verificar estudiantes
echo "<h2>3. Estudiantes disponibles</h2>";
$sql_estudiantes = "SELECT e.id_estudiante, e.id_usuario, u.nombre, u.username FROM estudiante e LEFT JOIN usuario u ON e.id_usuario = u.id_usuario LIMIT 10";
$result_estudiantes = $conn->query($sql_estudiantes);
if ($result_estudiantes && $result_estudiantes->num_rows > 0) {
    echo "<table border='1'>";
    echo "<tr><th>ID Estudiante</th><th>ID Usuario</th><th>Nombre</th><th>Username</th></tr>";
    while ($row = $result_estudiantes->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['id_estudiante'] . "</td>";
        echo "<td>" . $row['id_usuario'] . "</td>";
        echo "<td>" . htmlspecialchars($row['nombre']) . "</td>";
        echo "<td>" . htmlspecialchars($row['username']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>No hay estudiantes o error: " . $conn->error . "</p>";
}

// Verificar inscripciones
echo "<h2>4. Inscripciones aprobadas</h2>";
$sql_inscripciones = "SELECT i.id_estudiante, i.id_curso, c.nombre_curso, i.estado FROM inscripciones i LEFT JOIN cursos c ON i.id_curso = c.id_curso WHERE i.estado = 'aprobada' LIMIT 10";
$result_inscripciones = $conn->query($sql_inscripciones);
if ($result_inscripciones && $result_inscripciones->num_rows > 0) {
    echo "<table border='1'>";
    echo "<tr><th>ID Estudiante</th><th>ID Curso</th><th>Nombre Curso</th><th>Estado</th></tr>";
    while ($row = $result_inscripciones->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['id_estudiante'] . "</td>";
        echo "<td>" . $row['id_curso'] . "</td>";
        echo "<td>" . htmlspecialchars($row['nombre_curso']) . "</td>";
        echo "<td>" . $row['estado'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>No hay inscripciones aprobadas o error: " . $conn->error . "</p>";
}

// Probar la consulta de asistencia
echo "<h2>5. Prueba de consulta de asistencia</h2>";
$sql_test = "SELECT c.id_curso, c.nombre_curso,
             COALESCE(COUNT(DISTINCT a.fecha), 0) as total_clases,
             COALESCE(SUM(CASE WHEN a.presente = 1 THEN 1 ELSE 0 END), 0) as asistencias,
             COALESCE(SUM(CASE WHEN a.presente = 0 AND a.retardo = 0 THEN 1 ELSE 0 END), 0) as inasistencias,
             COALESCE(SUM(CASE WHEN a.retardo = 1 THEN 1 ELSE 0 END), 0) as retardos
             FROM cursos c
             INNER JOIN inscripciones i ON c.id_curso = i.id_curso
             LEFT JOIN asistencia a ON c.id_curso = a.id_curso AND i.id_estudiante = a.id_estudiante
             WHERE i.estado = 'aprobada'
             GROUP BY c.id_curso, c.nombre_curso
             LIMIT 5";

$result_test = $conn->query($sql_test);
if ($result_test && $result_test->num_rows > 0) {
    echo "<table border='1'>";
    echo "<tr><th>ID Curso</th><th>Nombre Curso</th><th>Total Clases</th><th>Asistencias</th><th>Inasistencias</th><th>Retardos</th></tr>";
    while ($row = $result_test->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['id_curso'] . "</td>";
        echo "<td>" . htmlspecialchars($row['nombre_curso']) . "</td>";
        echo "<td>" . $row['total_clases'] . "</td>";
        echo "<td>" . $row['asistencias'] . "</td>";
        echo "<td>" . $row['inasistencias'] . "</td>";
        echo "<td>" . $row['retardos'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>No hay resultados o error: " . $conn->error . "</p>";
}

echo "<h2>Debug completado</h2>";
?>








