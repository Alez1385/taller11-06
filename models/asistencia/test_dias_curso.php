<?php
require_once '../../scripts/auth.php';
require_once '../../scripts/conexion.php';
require_once '../../scripts/config.php';
requireLogin();
checkPermission('profesor');

$id_curso = $_GET['id_curso'] ?? 1;

echo "<h2>Test de Días para Curso ID: $id_curso</h2>";

// 1. Verificar estructura de la tabla horarios
echo "<h3>1. Estructura de la tabla 'horarios':</h3>";
$result = $conn->query("DESCRIBE horarios");
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

// 2. Verificar horarios para este curso
echo "<h3>2. Horarios para el curso $id_curso:</h3>";
$sql_horarios = "SELECT * FROM horarios WHERE id_curso = ? ORDER BY id_horario DESC";
$stmt = $conn->prepare($sql_horarios);
$stmt->bind_param("i", $id_curso);
$stmt->execute();
$result = $stmt->get_result();

echo "<table border='1'>";
echo "<tr><th>ID Horario</th><th>ID Curso</th><th>ID Profesor</th><th>Lunes</th><th>Martes</th><th>Miércoles</th><th>Jueves</th><th>Viernes</th><th>Sábado</th><th>Fecha Creación</th></tr>";
while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . $row['id_horario'] . "</td>";
    echo "<td>" . $row['id_curso'] . "</td>";
    echo "<td>" . $row['id_profesor'] . "</td>";
    echo "<td>" . ($row['lunes'] ?? 'NULL') . "</td>";
    echo "<td>" . ($row['martes'] ?? 'NULL') . "</td>";
    echo "<td>" . ($row['miercoles'] ?? 'NULL') . "</td>";
    echo "<td>" . ($row['jueves'] ?? 'NULL') . "</td>";
    echo "<td>" . ($row['viernes'] ?? 'NULL') . "</td>";
    echo "<td>" . ($row['sabado'] ?? 'NULL') . "</td>";
    echo "<td>" . $row['fecha_creacion'] . "</td>";
    echo "</tr>";
}
echo "</table>";

// 3. Probar la lógica de extracción de días
echo "<h3>3. Días extraídos con la nueva lógica:</h3>";
$sql_dias = "SELECT lunes, martes, miercoles, jueves, viernes, sabado 
             FROM horarios 
             WHERE id_curso = ? 
             ORDER BY id_horario DESC 
             LIMIT 1";
$stmt_dias = $conn->prepare($sql_dias);
$stmt_dias->bind_param("i", $id_curso);
$stmt_dias->execute();
$result_dias = $stmt_dias->get_result();
$horario_row = $result_dias->fetch_assoc();

$dias_curso = [];
if ($horario_row) {
    $dias_mapping = [
        'lunes' => 'Lunes',
        'martes' => 'Martes', 
        'miercoles' => 'Miércoles',
        'jueves' => 'Jueves',
        'viernes' => 'Viernes',
        'sabado' => 'Sábado'
    ];
    
    echo "<p><strong>Datos del horario:</strong></p>";
    echo "<ul>";
    foreach ($dias_mapping as $columna => $dia_nombre) {
        $valor = $horario_row[$columna] ?? 'NULL';
        echo "<li>$columna: $valor</li>";
        if (!empty($horario_row[$columna])) {
            $dias_curso[] = $dia_nombre;
        }
    }
    echo "</ul>";
    
    echo "<p><strong>Días de clase extraídos:</strong> " . implode(', ', $dias_curso) . "</p>";
} else {
    echo "<p>No se encontraron horarios para este curso.</p>";
}

// 4. Verificar información del curso
echo "<h3>4. Información del curso:</h3>";
$sql_curso = "SELECT c.nombre_curso, c.descripcion, c.nivel_educativo
              FROM cursos c
              WHERE c.id_curso = ?";
$stmt_curso = $conn->prepare($sql_curso);
$stmt_curso->bind_param("i", $id_curso);
$stmt_curso->execute();
$result_curso = $stmt_curso->get_result();
$curso = $result_curso->fetch_assoc();

if ($curso) {
    echo "<p><strong>Nombre:</strong> " . $curso['nombre_curso'] . "</p>";
    echo "<p><strong>Descripción:</strong> " . $curso['descripcion'] . "</p>";
    echo "<p><strong>Nivel:</strong> " . $curso['nivel_educativo'] . "</p>";
} else {
    echo "<p>No se encontró información del curso.</p>";
}

$conn->close();
?>


