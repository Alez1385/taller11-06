<?php
require_once '../../scripts/auth.php';
require_once '../../scripts/conexion.php';
require_once '../../scripts/config.php';
requireLogin();
checkPermission('profesor');

$id_curso = $_GET['id_curso'] ?? 1;

echo "<h2>Debug de Estudiantes para Curso ID: $id_curso</h2>";

// 1. Verificar estructura de la tabla asistencia
echo "<h3>1. Estructura de la tabla 'asistencia':</h3>";
$result = $conn->query("DESCRIBE asistencia");
if ($result) {
    echo "<table border='1'>";
    echo "<tr><th>Campo</th><th>Tipo</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    while ($row = $result->fetch_assoc()) {
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

// 2. Verificar inscripciones para este curso
echo "<h3>2. Inscripciones para el curso $id_curso:</h3>";
$sql_inscripciones = "SELECT i.*, u.nombre, u.apellido, e.id_estudiante 
                     FROM inscripciones i
                     LEFT JOIN estudiante e ON i.id_estudiante = e.id_estudiante
                     LEFT JOIN usuario u ON e.id_usuario = u.id_usuario
                     WHERE i.id_curso = ?";
$stmt = $conn->prepare($sql_inscripciones);
$stmt->bind_param("i", $id_curso);
$stmt->execute();
$result = $stmt->get_result();

echo "<table border='1'>";
echo "<tr><th>ID Inscripción</th><th>ID Estudiante</th><th>Nombre</th><th>Apellido</th><th>Estado</th><th>Fecha</th></tr>";
while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . $row['id_inscripcion'] . "</td>";
    echo "<td>" . $row['id_estudiante'] . "</td>";
    echo "<td>" . ($row['nombre'] ?? 'NULL') . "</td>";
    echo "<td>" . ($row['apellido'] ?? 'NULL') . "</td>";
    echo "<td>" . $row['estado'] . "</td>";
    echo "<td>" . $row['fecha_inscripcion'] . "</td>";
    echo "</tr>";
}
echo "</table>";

// 3. Verificar estudiantes activos
echo "<h3>3. Estudiantes con inscripción aprobada:</h3>";
$sql_estudiantes = "SELECT DISTINCT e.id_estudiante, u.nombre, u.apellido, u.id_usuario, i.estado
                   FROM estudiante e
                   INNER JOIN usuario u ON e.id_usuario = u.id_usuario
                   INNER JOIN inscripciones i ON e.id_estudiante = i.id_estudiante
                   WHERE i.id_curso = ? AND i.estado = 'aprobada'
                   ORDER BY u.apellido, u.nombre";
$stmt = $conn->prepare($sql_estudiantes);
$stmt->bind_param("i", $id_curso);
$stmt->execute();
$result = $stmt->get_result();

echo "<table border='1'>";
echo "<tr><th>ID Estudiante</th><th>ID Usuario</th><th>Nombre</th><th>Apellido</th><th>Estado Inscripción</th></tr>";
while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . $row['id_estudiante'] . "</td>";
    echo "<td>" . $row['id_usuario'] . "</td>";
    echo "<td>" . ($row['nombre'] ?? 'NULL') . "</td>";
    echo "<td>" . ($row['apellido'] ?? 'NULL') . "</td>";
    echo "<td>" . $row['estado'] . "</td>";
    echo "</tr>";
}
echo "</table>";

// 4. Verificar todos los cursos disponibles
echo "<h3>4. Cursos disponibles:</h3>";
$sql_cursos = "SELECT id_curso, nombre_curso, estado FROM cursos ORDER BY id_curso";
$result = $conn->query($sql_cursos);

echo "<table border='1'>";
echo "<tr><th>ID Curso</th><th>Nombre</th><th>Estado</th></tr>";
while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . $row['id_curso'] . "</td>";
    echo "<td>" . $row['nombre_curso'] . "</td>";
    echo "<td>" . $row['estado'] . "</td>";
    echo "</tr>";
}
echo "</table>";

$conn->close();
?>


