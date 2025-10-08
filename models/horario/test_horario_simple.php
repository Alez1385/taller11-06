<?php
require_once '../../scripts/auth.php';
require_once '../../scripts/conexion.php';
require_once '../../scripts/config.php';
requireLogin();

echo "<h2>Test Simple - Horarios del Profesor</h2>";

// Obtener el ID del profesor del usuario actual
$id_usuario = $_SESSION['id_usuario'];
echo "<p><strong>ID Usuario:</strong> " . $id_usuario . "</p>";

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
    echo "<p style='color: green;'>✅ Profesor: " . $profesor['nombre'] . " " . $profesor['apellido'] . "</p>";
    echo "<p><strong>ID Profesor:</strong> " . $profesor['id_profesor'] . "</p>";
    
    // Obtener horarios
    $sql_horarios = "SELECT h.id_horario, c.nombre_curso, c.descripcion, c.duracion,
                            h.lunes, h.martes, h.miercoles, h.jueves, h.viernes, h.sabado
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
        echo "<h3>Datos JSON que recibiría el dashboard:</h3>";
        $json_data = [
            'success' => true,
            'horarios' => $horarios
        ];
        echo "<pre>" . json_encode($json_data, JSON_PRETTY_PRINT) . "</pre>";
        
        echo "<h3>Tabla de Horarios:</h3>";
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
        
        echo "<h3>Enlaces de Prueba:</h3>";
        echo "<p><a href='../../dashboard/dashboard.php' target='_blank'>Dashboard Principal</a></p>";
        echo "<p><a href='horario_profesor.php?id_profesor=" . $profesor['id_profesor'] . "' target='_blank'>Horario Completo</a></p>";
        
    } else {
        echo "<p style='color: red;'>❌ No se encontraron horarios asignados</p>";
        echo "<p>Necesitas asignar horarios desde la administración.</p>";
    }
    
} else {
    echo "<p style='color: red;'>❌ El usuario no es un profesor</p>";
}

$conn->close();
?>
