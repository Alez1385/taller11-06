<?php
require_once '../scripts/conexion.php';
require_once '../scripts/functions.php';
require_once '../scripts/auth.php';

// Ensure user is logged in
requireLogin();

echo "<h2>🧪 Test de Sistema de Inscripciones</h2>";

// Get user details
$id_usuario = $_SESSION['id_usuario'] ?? '';
$user = getUserInfo($conn, $id_usuario);

echo "<h3>👤 Usuario Actual:</h3>";
echo "<p><strong>ID:</strong> " . $user['id_usuario'] . "</p>";
echo "<p><strong>Nombre:</strong> " . $user['nombre'] . "</p>";
echo "<p><strong>Tipo:</strong> " . $user['tipo_nombre'] . " (ID: " . $user['id_tipo_usuario'] . ")</p>";
echo "<p><strong>Perfil Incompleto:</strong> " . ($user['perfil_incompleto'] ? "Sí" : "No") . "</p>";

// Verificar si puede inscribirse
if ($user['id_tipo_usuario'] == 4 && $user['perfil_incompleto'] == 1) {
    echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "❌ No puedes inscribirte hasta completar tu perfil.";
    echo "</div>";
} else {
    echo "<div style='background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "✅ Puedes inscribirte a cursos.";
    echo "</div>";
}

// Mostrar cursos disponibles
echo "<h3>📚 Cursos Disponibles:</h3>";
$sql = "SELECT id_curso, nombre_curso, descripcion, estado FROM cursos WHERE estado = 'activo'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr style='background: #f8f9fa;'><th>ID</th><th>Curso</th><th>Descripción</th><th>Estado</th><th>Acciones</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['id_curso'] . "</td>";
        echo "<td>" . htmlspecialchars($row['nombre_curso']) . "</td>";
        echo "<td>" . htmlspecialchars($row['descripcion']) . "</td>";
        echo "<td>" . $row['estado'] . "</td>";
        echo "<td>";
        echo "<a href='inscripcion_completa.php?curso_id=" . $row['id_curso'] . "' style='background: #007bff; color: white; padding: 5px 10px; text-decoration: none; border-radius: 3px; margin-right: 5px;'>Inscripción Completa</a>";
        echo "<a href='../scripts/preinscribir.php?curso_id=" . $row['id_curso'] . "' style='background: #28a745; color: white; padding: 5px 10px; text-decoration: none; border-radius: 3px;'>Preinscripción</a>";
        echo "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>No hay cursos disponibles.</p>";
}

// Mostrar inscripciones existentes
echo "<h3>📋 Tus Inscripciones:</h3>";
$sql = "SELECT i.*, c.nombre_curso, e.id_estudiante 
        FROM inscripciones i 
        JOIN estudiante e ON i.id_estudiante = e.id_estudiante 
        JOIN cursos c ON i.id_curso = c.id_curso 
        WHERE e.id_usuario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr style='background: #f8f9fa;'><th>ID</th><th>Curso</th><th>Estado</th><th>Fecha</th><th>Comprobante</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['id_inscripcion'] . "</td>";
        echo "<td>" . htmlspecialchars($row['nombre_curso']) . "</td>";
        echo "<td style='color: " . ($row['estado'] == 'aprobada' ? 'green' : ($row['estado'] == 'pendiente' ? 'orange' : 'red')) . ";'>" . $row['estado'] . "</td>";
        echo "<td>" . $row['fecha_inscripcion'] . "</td>";
        echo "<td>" . ($row['comprobante_pago'] ? "✅ Sí" : "❌ No") . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>No tienes inscripciones.</p>";
}

// Mostrar preinscripciones existentes
echo "<h3>📝 Tus Preinscripciones:</h3>";
$sql = "SELECT p.*, c.nombre_curso 
        FROM preinscripciones p 
        JOIN cursos c ON p.id_curso = c.id_curso 
        WHERE p.id_usuario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr style='background: #f8f9fa;'><th>ID</th><th>Curso</th><th>Estado</th><th>Fecha</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['id_preinscripcion'] . "</td>";
        echo "<td>" . htmlspecialchars($row['nombre_curso']) . "</td>";
        echo "<td style='color: " . ($row['estado'] == 'pendiente' ? 'orange' : 'red') . ";'>" . $row['estado'] . "</td>";
        echo "<td>" . $row['fecha_preinscripcion'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>No tienes preinscripciones.</p>";
}

echo "<h3>🔧 Información del Sistema:</h3>";
echo "<p><strong>Directorio de uploads:</strong> " . (is_dir("../uploads/comprobantes/") ? "✅ Existe" : "❌ No existe") . "</p>";
echo "<p><strong>Permisos de escritura:</strong> " . (is_writable("../uploads/comprobantes/") ? "✅ Escribible" : "❌ No escribible") . "</p>";
echo "<p><strong>Upload max filesize:</strong> " . ini_get('upload_max_filesize') . "</p>";
echo "<p><strong>Post max size:</strong> " . ini_get('post_max_size') . "</p>";

$conn->close();
?>
