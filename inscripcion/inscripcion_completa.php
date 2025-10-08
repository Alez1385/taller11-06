<?php
require_once '../scripts/conexion.php';
require_once '../scripts/functions.php';
require_once '../scripts/auth.php';

// Ensure user is logged in
requireLogin();

// Get user details
$id_usuario = $_SESSION['id_usuario'] ?? '';
$user = getUserInfo($conn, $id_usuario);

if (!$user) {
    die('Error: Usuario no encontrado. Por favor, contacte al administrador.');
}

$id_usuario = $user['id_usuario'];
$curso_id = filter_input(INPUT_GET, 'curso_id', FILTER_VALIDATE_INT);

if (!$curso_id) {
    die('Error: No se especificó un curso válido.');
}

// Validar perfil antes de permitir inscripción
if ($user['id_tipo_usuario'] == 4 && (!empty($user['perfil_incompleto']) && $user['perfil_incompleto'] == 1)) {
    echo '<div class="message error">No puedes inscribirte a cursos hasta completar tu perfil. <a href="../models/perfil/perfil.php">Completa tu perfil aquí</a>.</div>';
    exit;
}

// Function to get course details
function getCursoDetails($conn, $curso_id) {
    $stmt = $conn->prepare("SELECT * FROM cursos WHERE id_curso = ?");
    $stmt->bind_param("i", $curso_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

// Process registration form
$error_message = '';
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $target_dir = "../uploads/comprobantes/";
    $file_name = basename($_FILES["comprobante"]["name"]);
    $target_file = $target_dir . time() . '_' . $file_name;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Validate file
    $uploadOk = validateFile($_FILES["comprobante"], $imageFileType);

    if ($uploadOk) {
        // Crear directorio si no existe
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0755, true);
        }
        
        if (move_uploaded_file($_FILES["comprobante"]["tmp_name"], $target_file)) {
            $result = processInscripcion($conn, $curso_id, $id_usuario, $target_file);
            if ($result === true) {
                $success_message = "Inscripción completada con éxito.";
            } elseif ($result === 'duplicate') {
                $error_message = "Ya existe una inscripción para este curso y usuario.";
            } elseif ($result === 'pre_duplicate') {
                $error_message = "Ya existe una preinscripción pendiente para este curso y usuario.";
            } else {
                $error_message = "Error al procesar la inscripción: " . $result;
            }
        } else {
            $error_message = "Lo siento, hubo un error al subir tu archivo. Verifica los permisos del directorio.";
        }
    } else {
        $error_message = "Error en el archivo: formato no válido o tamaño excesivo. Formatos permitidos: JPG, JPEG, PNG, GIF. Tamaño máximo: 500KB.";
    }
}

// Get course and user details
$curso = getCursoDetails($conn, $curso_id);

// Helper functions
function validateFile($file, $fileType) {
    $maxFileSize = 500000; // 500KB
    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];

    // Verificar si hay errores en la subida
    if ($file['error'] !== UPLOAD_ERR_OK) {
        error_log("Error en subida de archivo: " . $file['error']);
        return false;
    }

    // Verificar tipo de archivo
    if (!in_array($fileType, $allowedTypes)) {
        error_log("Tipo de archivo no permitido: " . $fileType);
        return false;
    }

    // Verificar tamaño
    if ($file['size'] > $maxFileSize) {
        error_log("Archivo demasiado grande: " . $file['size'] . " bytes");
        return false;
    }

    // Verificar que sea una imagen válida
    $check = getimagesize($file["tmp_name"]);
    if ($check === false) {
        error_log("Archivo no es una imagen válida");
        return false;
    }

    return true;
}

function processInscripcion($conn, $curso_id, $id_usuario, $comprobante) {
    try {
        // Iniciar transacción
        $conn->begin_transaction();
        
        // Verificar si ya existe inscripción para este usuario y curso
        $stmt = $conn->prepare("SELECT e.id_estudiante, i.id_inscripcion, i.estado FROM estudiante e LEFT JOIN inscripciones i ON e.id_estudiante = i.id_estudiante AND i.id_curso = ? WHERE e.id_usuario = ?");
        if (!$stmt) {
            throw new Exception("Error preparando consulta de verificación: " . $conn->error);
        }
        $stmt->bind_param("ii", $curso_id, $id_usuario);
        $stmt->execute();
        $result = $stmt->get_result();
        $duplicate = false;
        while ($row = $result->fetch_assoc()) {
            if ($row['id_inscripcion'] && ($row['estado'] === 'aprobada' || $row['estado'] === 'pendiente')) {
                $duplicate = true;
                break;
            }
        }
        if ($duplicate) {
            $conn->rollback();
            return 'duplicate';
        }
        
        // Verificar si hay preinscripción pendiente
        $stmt = $conn->prepare("SELECT id_preinscripcion FROM preinscripciones WHERE id_usuario = ? AND id_curso = ? AND estado = 'pendiente'");
        if (!$stmt) {
            throw new Exception("Error preparando consulta de preinscripción: " . $conn->error);
        }
        $stmt->bind_param("ii", $id_usuario, $curso_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $conn->rollback();
            return 'pre_duplicate';
        }
        
        // Crear estudiante si no existe
        $stmt = $conn->prepare("SELECT id_estudiante FROM estudiante WHERE id_usuario = ?");
        if (!$stmt) {
            throw new Exception("Error preparando consulta de estudiante: " . $conn->error);
        }
        $stmt->bind_param("i", $id_usuario);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 0) {
            $stmt = $conn->prepare("INSERT INTO estudiante (id_usuario, fecha_registro, estado) VALUES (?, NOW(), 'activo')");
            if (!$stmt) {
                throw new Exception("Error preparando inserción de estudiante: " . $conn->error);
            }
            $stmt->bind_param("i", $id_usuario);
            if (!$stmt->execute()) {
                throw new Exception("Error ejecutando inserción de estudiante: " . $stmt->error);
            }
            $id_estudiante = $stmt->insert_id;
        } else {
            $row = $result->fetch_assoc();
            $id_estudiante = $row['id_estudiante'];
        }
        
        // Eliminar preinscripción si existe
        $stmt = $conn->prepare("DELETE FROM preinscripciones WHERE id_usuario = ? AND id_curso = ?");
        if (!$stmt) {
            throw new Exception("Error preparando eliminación de preinscripción: " . $conn->error);
        }
        $stmt->bind_param("ii", $id_usuario, $curso_id);
        $stmt->execute();
        
        // Crear inscripción formal
        $stmt = $conn->prepare("INSERT INTO inscripciones (id_curso, id_estudiante, fecha_inscripcion, estado, fecha_actualizacion, comprobante_pago) VALUES (?, ?, NOW(), 'pendiente', NOW(), ?)");
        if (!$stmt) {
            throw new Exception("Error preparando inserción de inscripción: " . $conn->error);
        }
        $stmt->bind_param("iis", $curso_id, $id_estudiante, $comprobante);
        if (!$stmt->execute()) {
            throw new Exception("Error ejecutando inserción de inscripción: " . $stmt->error);
        }

        // Actualizar tipo de usuario a estudiante si era user (tipo 4)
        $stmt = $conn->prepare("SELECT id_tipo_usuario FROM usuario WHERE id_usuario = ?");
        if (!$stmt) {
            throw new Exception("Error preparando consulta de tipo de usuario: " . $conn->error);
        }
        $stmt->bind_param("i", $id_usuario);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            if ($row['id_tipo_usuario'] == 4) {
                $stmt2 = $conn->prepare("UPDATE usuario SET id_tipo_usuario = 3 WHERE id_usuario = ?");
                if (!$stmt2) {
                    throw new Exception("Error preparando actualización de tipo de usuario: " . $conn->error);
                }
                $stmt2->bind_param("i", $id_usuario);
                if (!$stmt2->execute()) {
                    throw new Exception("Error ejecutando actualización de tipo de usuario: " . $stmt2->error);
                }
                // Actualizar sesión si corresponde
                if (isset($_SESSION['id_usuario']) && $_SESSION['id_usuario'] == $id_usuario) {
                    $_SESSION['id_tipo_usuario'] = 3;
                    $_SESSION['user_role'] = 'estudiante';
                }
            }
        }

        // Limpiar inscripciones rechazadas/canceladas para este usuario y curso
        $stmt = $conn->prepare("DELETE FROM inscripciones WHERE id_estudiante = ? AND id_curso = ? AND (estado = 'rechazada' OR estado = 'cancelada')");
        if (!$stmt) {
            throw new Exception("Error preparando limpieza de inscripciones: " . $conn->error);
        }
        $stmt->bind_param("ii", $id_estudiante, $curso_id);
        $stmt->execute();
        
        // Confirmar transacción
        $conn->commit();
        return true;
        
    } catch (Exception $e) {
        $conn->rollback();
        error_log("Error en processInscripcion: " . $e->getMessage());
        return $e->getMessage();
    }
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscripción al Curso</title>
    <link rel="stylesheet" href="css/styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <script src="scripts/logAuthinfo.js"></script>
</head>
<body>
    <div class="dashboard-container">
        <?php if ($error_message): ?>
            <div class="message error"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>
        <?php if ($success_message): ?>
            <div class="message success"><?php echo htmlspecialchars($success_message); ?></div>
            <script>
                setTimeout(function() {
                    window.location.href = '/dashboard/dashboard.php';
                }, 3000);
            </script>
        <?php endif; ?>
        <h1>Inscripción al Curso</h1>

        <div class="course-details">
            <div class="course-image">
                <img src="../uploads/icons/<?php echo htmlspecialchars($curso['icono']); ?>" alt="<?php echo htmlspecialchars($curso['nombre_curso']); ?>">
            </div>
            <div class="course-info">
                <h2><?php echo htmlspecialchars($curso['nombre_curso']); ?></h2>
                <p class="description"><?php echo htmlspecialchars($curso['descripcion']); ?></p>
                <div class="course-meta">
                    <span class="duration"><i class="icon-clock"></i> <?php echo htmlspecialchars($curso['duracion']); ?> semanas</span>
                    <span class="level"><i class="icon-graduation-cap"></i> <?php echo htmlspecialchars($curso['nivel_educativo']); ?></span>
                </div>
            </div>
        </div>

        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) . '?curso_id=' . $curso_id; ?>" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="comprobante">Comprobante de pago:</label>
                <div class="file-upload">
                    <input type="file" id="comprobante" name="comprobante" accept="image/*" required>
                    <span class="file-upload-label">Seleccionar archivo</span>
                </div>
                <p class="file-info">Formatos aceptados: JPG, JPEG, PNG, GIF. Tamaño máximo: 500KB.</p>
                <div id="imagePreview" class="image-preview">
                    <img id="previewImage" src="#" alt="Vista previa del comprobante">
                </div>
            </div>

            <button type="submit">Completar Inscripción</button>
        </form>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const fileInput = document.getElementById('comprobante');
            const imagePreview = document.getElementById('imagePreview');
            const previewImage = document.getElementById('previewImage');

            fileInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        previewImage.src = e.target.result;
                        imagePreview.style.display = 'block';
                    }
                    reader.readAsDataURL(file);
                }
            });
        });
    </script>
</body>
</html>
