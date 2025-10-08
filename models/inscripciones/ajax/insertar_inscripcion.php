<?php
require_once '../../../scripts/conexion.php';
require_once '../../../scripts/error_logger.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recuperar y sanitizar la entrada
    $id_curso = filter_input(INPUT_POST, 'id_curso', FILTER_SANITIZE_NUMBER_INT);
    $id_estudiante = filter_input(INPUT_POST, 'id_estudiante', FILTER_SANITIZE_NUMBER_INT);
    $comprobante_pago = $_FILES['comprobante_pago'] ?? null;  

    if (!$comprobante_pago) {
        logError("No se ha cargado ningún comprobante de pago.", "");
        sendJsonResponse(false, "No se ha cargado ningún comprobante de pago.");
        exit;
    }

    // Validar entrada
    if (!$id_curso || !$id_estudiante) {
        logError("Datos de entrada inválidos: id_curso = $id_curso, id_estudiante = $id_estudiante", "");
        sendJsonResponse(false, "Datos de entrada inválidos. Por favor, verifica los campos del formulario.");
        exit;
    }

    // Iniciar transacción
    $conn->begin_transaction();

    try {
        // Obtener el id_usuario desde la sesión o POST (ajusta según tu flujo)
        $id_usuario = $_SESSION['id_usuario'] ?? (isset($_POST['id_usuario']) ? intval($_POST['id_usuario']) : null);
        if (!$id_usuario) {
            logError("No se pudo determinar el usuario.", "");
            sendJsonResponse(false, "No se pudo determinar el usuario actual.");
            exit;
        }
        // Buscar el id_estudiante asociado al usuario (si existe)
        $sql = "SELECT id_estudiante FROM estudiante WHERE id_usuario = ? ORDER BY id_estudiante ASC LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id_usuario);
        $stmt->execute();
        $stmt->bind_result($id_estudiante_real);
        $stmt->fetch();
        $stmt->close();
        if ($id_estudiante_real) {
            $id_estudiante = $id_estudiante_real;
        }
        // Eliminar todas las preinscripciones previas para este usuario y curso
        $sql_del_pre = "DELETE FROM preinscripciones WHERE id_curso = ? AND (id_usuario = ? OR email = (SELECT mail FROM usuario WHERE id_usuario = ?))";
        $stmt_del_pre = $conn->prepare($sql_del_pre);
        $stmt_del_pre->bind_param("iii", $id_curso, $id_usuario, $id_usuario);
        $stmt_del_pre->execute();
        $stmt_del_pre->close();
        // Forzar limpieza de inscripciones activas para este usuario y curso
        $sql_update = "UPDATE inscripciones i JOIN estudiante e ON i.id_estudiante = e.id_estudiante SET i.estado = 'rechazada' WHERE i.id_curso = ? AND e.id_usuario = ? AND i.estado IN ('aprobada', 'pendiente')";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("ii", $id_curso, $id_usuario);
        $stmt_update->execute();
        $stmt_update->close();
        // Validar si ya existe inscripción activa para este usuario en el curso
        $sql_check = "SELECT estado FROM inscripciones WHERE id_curso = ? AND id_estudiante IN (SELECT id_estudiante FROM estudiante WHERE id_usuario = ?)";
        $stmt_check = $conn->prepare($sql_check);
        $stmt_check->bind_param("ii", $id_curso, $id_usuario);
        $stmt_check->execute();
        $result = $stmt_check->get_result();
        $bloqueado = false;
        while ($row = $result->fetch_assoc()) {
            if ($row['estado'] === 'aprobada' || $row['estado'] === 'pendiente') {
                $bloqueado = true;
                break;
            }
        }
        $stmt_check->close();
        if ($bloqueado) {
            logError("El estudiante ya está inscrito en este curso.", "", "INFO");
            throw new Exception("El estudiante ya está inscrito en este curso.");
        }

        // Manejo del archivo de comprobante de pago
        $comprobante_pago_path = handleFileUpload($comprobante_pago);

        if (!$comprobante_pago_path) {
            throw new Exception("Error al subir el comprobante de pago.");
        }

        // Preparar declaración SQL
        $sql = "INSERT INTO inscripciones (id_curso, id_estudiante, fecha_inscripcion, estado, comprobante_pago) 
                VALUES (?, ?, CURDATE(), 'pendiente', ?)";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iis", $id_curso, $id_estudiante, $comprobante_pago_path);
        if (!$stmt->execute()) {
            throw new Exception("Error al ejecutar la declaración: " . $stmt->error);
        }
        // Refuerzo: Si el usuario tiene inscripción aprobada y registro en estudiante, fuerza el tipo a estudiante
        $sql_check_aprob = "SELECT COUNT(*) as total FROM inscripciones i INNER JOIN estudiante e ON i.id_estudiante = e.id_estudiante WHERE e.id_usuario = ? AND i.estado = 'aprobada'";
        $stmt_check_aprob = $conn->prepare($sql_check_aprob);
        $stmt_check_aprob->bind_param("i", $id_usuario);
        $stmt_check_aprob->execute();
        $res_check_aprob = $stmt_check_aprob->get_result();
        $total_aprob = 0;
        if ($res_check_aprob && $row_aprob = $res_check_aprob->fetch_assoc()) {
            $total_aprob = $row_aprob['total'];
        }
        $stmt_check_aprob->close();
        if ($total_aprob > 0) {
            $sql_user = "UPDATE usuario SET id_tipo_usuario = 3 WHERE id_usuario = ?";
            $stmt_user = $conn->prepare($sql_user);
            if ($stmt_user) {
                $stmt_user->bind_param("i", $id_usuario);
                $stmt_user->execute();
                if ($stmt_user->affected_rows === 0) {
                    $conn->rollback();
                    sendJsonResponse(false, "Error: No se pudo actualizar el tipo de usuario a estudiante.");
                }
                $_SESSION['id_tipo_usuario'] = 3;
                $_SESSION['user_role'] = 'estudiante';
                $stmt_user->close();
            } else {
                $conn->rollback();
                sendJsonResponse(false, "Error: No se pudo preparar el UPDATE para tipo estudiante: " . $conn->error);
            }
        }

        // Confirmar transacción
        $conn->commit();
        logError("Inscripción creada con éxito.");
        sendJsonResponse(true, "Inscripción creada con éxito.");

    } catch (Exception $e) {
        // En caso de error, hacer rollback
        $conn->rollback();
        logError($e->getMessage(), "");
        sendJsonResponse(false, "Error: " . $e->getMessage());
    } finally {
        // Cerrar la declaración
        if (isset($stmt)) $stmt->close();
    }
} else {
    sendJsonResponse(false, "Método de solicitud inválido.");
}

// Función para enviar respuesta JSON
function sendJsonResponse($success, $message, $data = []) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data
    ]);
}

function handleFileUpload($file) {
    $target_dir = "../../../uploads/comprobantes/";
    $file_name = uniqid() . '_' . basename($file["name"]);
    $target_file = $target_dir . $file_name;

    if ($file['error'] != 0) {
        logError("Error en el archivo: " . $file['error'], "");
        return null;
    }

    if (!move_uploaded_file($file["tmp_name"], $target_file)) {
        logError("Error al mover el archivo: " . $file["name"], "");
        return null;
    }

    return $target_file;
}
