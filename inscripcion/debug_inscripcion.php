<?php
require_once '../scripts/conexion.php';
require_once '../scripts/functions.php';
require_once '../scripts/auth.php';

// Ensure user is logged in
requireLogin();

echo "<h2>🔍 Diagnóstico del Sistema de Inscripciones</h2>";

// Get user details
$id_usuario = $_SESSION['id_usuario'] ?? '';
$user = getUserInfo($conn, $id_usuario);

echo "<h3>👤 Información del Usuario:</h3>";
echo "<pre>";
print_r($user);
echo "</pre>";

echo "<h3>📊 Estado de la Sesión:</h3>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

// Verificar si el directorio de uploads existe
$target_dir = "../uploads/comprobantes/";
echo "<h3>📁 Directorio de Uploads:</h3>";
echo "<p><strong>Ruta:</strong> " . $target_dir . "</p>";
echo "<p><strong>Existe:</strong> " . (is_dir($target_dir) ? "✅ Sí" : "❌ No") . "</p>";
echo "<p><strong>Escribible:</strong> " . (is_writable($target_dir) ? "✅ Sí" : "❌ No") . "</p>";

// Verificar permisos de PHP
echo "<h3>🔧 Configuración PHP:</h3>";
echo "<p><strong>upload_max_filesize:</strong> " . ini_get('upload_max_filesize') . "</p>";
echo "<p><strong>post_max_size:</strong> " . ini_get('post_max_size') . "</p>";
echo "<p><strong>file_uploads:</strong> " . (ini_get('file_uploads') ? "✅ Habilitado" : "❌ Deshabilitado") . "</p>";
echo "<p><strong>max_file_uploads:</strong> " . ini_get('max_file_uploads') . "</p>";

// Verificar estructura de la base de datos
echo "<h3>🗄️ Estructura de la Base de Datos:</h3>";

// Verificar tabla usuario
$result = $conn->query("DESCRIBE usuario");
echo "<h4>Tabla usuario:</h4>";
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

// Verificar tabla estudiante
$result = $conn->query("DESCRIBE estudiante");
echo "<h4>Tabla estudiante:</h4>";
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

// Verificar tabla inscripciones
$result = $conn->query("DESCRIBE inscripciones");
echo "<h4>Tabla inscripciones:</h4>";
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

// Verificar inscripciones existentes del usuario
echo "<h3>📋 Inscripciones Existentes del Usuario:</h3>";
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
    echo "<table border='1'>";
    echo "<tr><th>ID Inscripción</th><th>Curso</th><th>Estado</th><th>Fecha</th><th>Comprobante</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['id_inscripcion'] . "</td>";
        echo "<td>" . $row['nombre_curso'] . "</td>";
        echo "<td>" . $row['estado'] . "</td>";
        echo "<td>" . $row['fecha_inscripcion'] . "</td>";
        echo "<td>" . ($row['comprobante_pago'] ? "✅ Sí" : "❌ No") . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>No hay inscripciones existentes.</p>";
}

// Verificar preinscripciones existentes del usuario
echo "<h3>📝 Preinscripciones Existentes del Usuario:</h3>";
$sql = "SELECT p.*, c.nombre_curso 
        FROM preinscripciones p 
        JOIN cursos c ON p.id_curso = c.id_curso 
        WHERE p.id_usuario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo "<table border='1'>";
    echo "<tr><th>ID Preinscripción</th><th>Curso</th><th>Estado</th><th>Fecha</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['id_preinscripcion'] . "</td>";
        echo "<td>" . $row['nombre_curso'] . "</td>";
        echo "<td>" . $row['estado'] . "</td>";
        echo "<td>" . $row['fecha_preinscripcion'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>No hay preinscripciones existentes.</p>";
}

// Verificar cursos disponibles
echo "<h3>📚 Cursos Disponibles:</h3>";
$sql = "SELECT id_curso, nombre_curso, descripcion, estado FROM cursos WHERE estado = 'activo'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "<table border='1'>";
    echo "<tr><th>ID Curso</th><th>Nombre</th><th>Descripción</th><th>Estado</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['id_curso'] . "</td>";
        echo "<td>" . $row['nombre_curso'] . "</td>";
        echo "<td>" . $row['descripcion'] . "</td>";
        echo "<td>" . $row['estado'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>No hay cursos disponibles.</p>";
}

echo "<h3>🔗 Enlaces de Prueba:</h3>";
echo "<p><a href='inscripcion_completa.php?curso_id=1' target='_blank'>Probar Inscripción Completa (Curso ID 1)</a></p>";
echo "<p><a href='../scripts/preinscribir.php?curso_id=1' target='_blank'>Probar Preinscripción (Curso ID 1)</a></p>";

$conn->close();
?>
