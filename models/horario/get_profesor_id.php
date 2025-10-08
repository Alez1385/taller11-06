<?php
require_once '../../scripts/auth.php';
require_once '../../scripts/conexion.php';
require_once '../../scripts/config.php';
requireLogin();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id_horario'])) {
    $id_horario = intval($_POST['id_horario']);

    $sql = "SELECT id_profesor FROM horarios WHERE id_horario = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_horario);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo json_encode(['success' => true, 'id_profesor' => $row['id_profesor']]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Horario no encontrado']);
    }
    
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Solicitud inválida']);
}

$conn->close();
?>
