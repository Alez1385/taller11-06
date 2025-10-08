<?php
require_once '../../scripts/auth.php';
require_once '../../scripts/conexion.php';
require_once '../../scripts/config.php';
requireLogin();

echo "<h2>Test de Horarios del Profesor</h2>";

// Obtener el ID del profesor del usuario actual
$id_usuario = $_SESSION['user_id'];
echo "<p>ID Usuario: " . $id_usuario . "</p>";

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
    echo "<p>Profesor: " . $profesor['nombre'] . " " . $profesor['apellido'] . "</p>";
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
        foreach ($horarios as $horario) {
            echo "<div style='border: 1px solid #ccc; padding: 10px; margin: 10px 0;'>";
            echo "<h4>" . htmlspecialchars($horario['nombre_curso']) . "</h4>";
            echo "<p>Lunes: " . htmlspecialchars($horario['lunes'] ?? '-') . "</p>";
            echo "<p>Martes: " . htmlspecialchars($horario['martes'] ?? '-') . "</p>";
            echo "<p>Miércoles: " . htmlspecialchars($horario['miercoles'] ?? '-') . "</p>";
            echo "<p>Jueves: " . htmlspecialchars($horario['jueves'] ?? '-') . "</p>";
            echo "<p>Viernes: " . htmlspecialchars($horario['viernes'] ?? '-') . "</p>";
            echo "<p>Sábado: " . htmlspecialchars($horario['sabado'] ?? '-') . "</p>";
            echo "</div>";
        }
        
        echo "<p><a href='horario_profesor.php?id_profesor=" . $profesor['id_profesor'] . "'>Ver horario completo</a></p>";
    } else {
        echo "<p style='color: red;'>No se encontraron horarios asignados para este profesor.</p>";
        echo "<p>Necesitas asignar horarios desde la administración.</p>";
    }
    
} else {
    echo "<p style='color: red;'>El usuario no es un profesor.</p>";
}

$conn->close();
?>
