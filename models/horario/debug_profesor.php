<?php
require_once '../../scripts/auth.php';
require_once '../../scripts/conexion.php';
require_once '../../scripts/config.php';
requireLogin();

// Obtener el ID del profesor del usuario actual
$id_usuario = $_SESSION['user_id'];

echo "<h2>Debug de Profesor</h2>";
echo "<p>ID Usuario: " . $id_usuario . "</p>";

// Verificar estructura de la tabla usuario
echo "<h3>Estructura de la tabla usuario:</h3>";
$sql_estructura = "DESCRIBE usuario";
$result_estructura = $conn->query($sql_estructura);
if ($result_estructura) {
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>Campo</th><th>Tipo</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    while ($row = $result_estructura->fetch_assoc()) {
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
}

// Verificar si el usuario es profesor
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
    echo "<p>Profesor encontrado: " . $profesor['nombre'] . " " . $profesor['apellido'] . "</p>";
    echo "<p>ID Profesor: " . $profesor['id_profesor'] . "</p>";
    
    // Verificar horarios asignados
    $sql_horarios = "SELECT h.id_horario, c.nombre_curso, h.lunes, h.martes, h.miercoles, h.jueves, h.viernes, h.sabado
                     FROM horarios h
                     JOIN cursos c ON h.id_curso = c.id_curso
                     WHERE h.id_profesor = ?";
    $stmt_horarios = $conn->prepare($sql_horarios);
    $stmt_horarios->bind_param("i", $profesor['id_profesor']);
    $stmt_horarios->execute();
    $result_horarios = $stmt_horarios->get_result();
    $horarios = $result_horarios->fetch_all(MYSQLI_ASSOC);
    
    echo "<p>Horarios encontrados: " . count($horarios) . "</p>";
    
    if (count($horarios) > 0) {
        echo "<h3>Horarios Asignados:</h3>";
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>Curso</th><th>Lunes</th><th>Martes</th><th>Miércoles</th><th>Jueves</th><th>Viernes</th><th>Sábado</th></tr>";
        
        foreach ($horarios as $horario) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($horario['nombre_curso']) . "</td>";
            echo "<td>" . htmlspecialchars($horario['lunes'] ?? '-') . "</td>";
            echo "<td>" . htmlspecialchars($horario['martes'] ?? '-') . "</td>";
            echo "<td>" . htmlspecialchars($horario['miercoles'] ?? '-') . "</td>";
            echo "<td>" . htmlspecialchars($horario['jueves'] ?? '-') . "</td>";
            echo "<td>" . htmlspecialchars($horario['viernes'] ?? '-') . "</td>";
            echo "<td>" . htmlspecialchars($horario['sabado'] ?? '-') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='color: red;'>No se encontraron horarios asignados para este profesor.</p>";
    }
    
    echo "<p><a href='horario_profesor.php?id_profesor=" . $profesor['id_profesor'] . "&debug=1'>Ver horario completo con debug</a></p>";
    
} else {
    echo "<p style='color: red;'>El usuario no es un profesor.</p>";
}

$conn->close();
?>
