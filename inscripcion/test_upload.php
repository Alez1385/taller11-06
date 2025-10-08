<?php
// Test simple para verificar la subida de archivos
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "<h3>📁 Información del Archivo Subido:</h3>";
    echo "<pre>";
    print_r($_FILES);
    echo "</pre>";
    
    echo "<h3>🔧 Configuración PHP:</h3>";
    echo "<p><strong>upload_max_filesize:</strong> " . ini_get('upload_max_filesize') . "</p>";
    echo "<p><strong>post_max_size:</strong> " . ini_get('post_max_size') . "</p>";
    echo "<p><strong>file_uploads:</strong> " . (ini_get('file_uploads') ? "Habilitado" : "Deshabilitado") . "</p>";
    echo "<p><strong>max_file_uploads:</strong> " . ini_get('max_file_uploads') . "</p>";
    
    echo "<h3>📂 Directorio de Destino:</h3>";
    $target_dir = "../uploads/comprobantes/";
    echo "<p><strong>Ruta:</strong> " . $target_dir . "</p>";
    echo "<p><strong>Existe:</strong> " . (is_dir($target_dir) ? "Sí" : "No") . "</p>";
    echo "<p><strong>Escribible:</strong> " . (is_writable($target_dir) ? "Sí" : "No") . "</p>";
    
    if (isset($_FILES['comprobante'])) {
        $file = $_FILES['comprobante'];
        echo "<h3>✅ Archivo Recibido:</h3>";
        echo "<p><strong>Nombre:</strong> " . $file['name'] . "</p>";
        echo "<p><strong>Tamaño:</strong> " . $file['size'] . " bytes</p>";
        echo "<p><strong>Tipo:</strong> " . $file['type'] . "</p>";
        echo "<p><strong>Error:</strong> " . $file['error'] . "</p>";
        
        if ($file['error'] === UPLOAD_ERR_OK) {
            echo "<p style='color: green;'>✅ Archivo subido correctamente</p>";
        } else {
            echo "<p style='color: red;'>❌ Error en la subida: " . $file['error'] . "</p>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test de Subida de Archivos</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .form-group { margin: 15px 0; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="file"] { padding: 10px; border: 1px solid #ddd; border-radius: 5px; }
        button { background: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; }
        button:hover { background: #0056b3; }
        .info { background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 20px 0; }
    </style>
</head>
<body>
    <h2>🧪 Test de Subida de Archivos</h2>
    
    <div class="info">
        <h3>📋 Instrucciones:</h3>
        <ol>
            <li>Selecciona una imagen (JPG, PNG, GIF)</li>
            <li>Haz clic en "Probar Subida"</li>
            <li>Revisa la información mostrada</li>
        </ol>
    </div>
    
    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="comprobante">Seleccionar Archivo:</label>
            <input type="file" id="comprobante" name="comprobante" accept="image/*" required>
        </div>
        
        <button type="submit">Probar Subida</button>
    </form>
    
    <div class="info">
        <h3>🔗 Enlaces de Prueba:</h3>
        <p><a href="inscripcion_completa.php?curso_id=1" target="_blank">Probar Inscripción Completa (Curso ID 1)</a></p>
        <p><a href="test_inscripcion.php" target="_blank">Ver Test de Inscripciones</a></p>
        <p><a href="debug_inscripcion.php" target="_blank">Ver Diagnóstico Completo</a></p>
    </div>
</body>
</html>
