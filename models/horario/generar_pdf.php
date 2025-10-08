<?php
require_once '../../scripts/auth.php';
require_once '../../scripts/conexion.php';
require_once '../../scripts/config.php';
requireLogin();

// Incluir la librería TCPDF
require_once('../../vendor/autoload.php');

use TCPDF as TCPDF;

$tipo = $_GET['tipo'] ?? 'estudiante';
$id_curso = $_GET['id_curso'] ?? 0;
$id_profesor = $_GET['id_profesor'] ?? 0;

if ($tipo === 'estudiante' && $id_curso) {
    // Generar PDF para estudiante
    $sql = "SELECT c.nombre_curso, h.lunes, h.martes, h.miercoles, h.jueves, h.viernes, h.sabado, 
                   CONCAT(u.nombre, ' ', u.apellido) as nombre_profesor
            FROM cursos c
            LEFT JOIN horarios h ON c.id_curso = h.id_curso
            LEFT JOIN profesor p ON h.id_profesor = p.id_profesor
            LEFT JOIN usuario u ON p.id_usuario = u.id_usuario
            WHERE c.id_curso = ?
            ORDER BY h.id_horario DESC
            LIMIT 1";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_curso);
    $stmt->execute();
    $result = $stmt->get_result();
    $curso = $result->fetch_assoc();
    
    if (!$curso) {
        die("Curso no encontrado.");
    }
    
    generarPDFEstudiante($curso);
    
} elseif ($tipo === 'profesor' && $id_profesor) {
    // Generar PDF para profesor
    $sql = "SELECT h.id_horario, c.nombre_curso, c.descripcion, c.duracion, 
                   h.lunes, h.martes, h.miercoles, h.jueves, h.viernes, h.sabado
            FROM horarios h
            JOIN cursos c ON h.id_curso = c.id_curso
            WHERE h.id_profesor = ?
            ORDER BY h.fecha_creacion DESC";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_profesor);
    $stmt->execute();
    $result = $stmt->get_result();
    $horarios = $result->fetch_all(MYSQLI_ASSOC);
    
    // Obtener información del profesor
    $sql_info = "SELECT u.nombre, u.apellido, u.email, u.telefono 
                 FROM profesor p
                 JOIN usuario u ON p.id_usuario = u.id_usuario
                 WHERE p.id_profesor = ?";
    $stmt_info = $conn->prepare($sql_info);
    $stmt_info->bind_param("i", $id_profesor);
    $stmt_info->execute();
    $result_info = $stmt_info->get_result();
    $info_profesor = $result_info->fetch_assoc();
    
    generarPDFProfesor($horarios, $info_profesor);
}

function generarPDFEstudiante($curso) {
    $pdf = new TCPDF('L', 'mm', 'A4', true, 'UTF-8', false);
    
    // Configurar documento
    $pdf->SetCreator('Sistema de Gestión de Cursos');
    $pdf->SetAuthor('I.E. Sagrado Corazón de Jesús');
    $pdf->SetTitle('Horario de Clases - ' . $curso['nombre_curso']);
    $pdf->SetSubject('Horario de Clases');
    
    // Configurar márgenes
    $pdf->SetMargins(15, 20, 15);
    $pdf->SetHeaderMargin(5);
    $pdf->SetFooterMargin(10);
    
    // Agregar página
    $pdf->AddPage();
    
    // Configurar fuente
    $pdf->SetFont('helvetica', 'B', 20);
    $pdf->SetTextColor(30, 58, 138);
    
    // Título
    $pdf->Cell(0, 15, 'HORARIO DE CLASES', 0, 1, 'C');
    $pdf->SetFont('helvetica', 'B', 16);
    $pdf->Cell(0, 10, $curso['nombre_curso'], 0, 1, 'C');
    
    // Información del profesor
    $pdf->SetFont('helvetica', '', 12);
    $pdf->SetTextColor(100, 100, 100);
    $pdf->Cell(0, 8, 'Profesor: ' . $curso['nombre_profesor'], 0, 1, 'C');
    $pdf->Ln(10);
    
    // Crear tabla de horario
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->SetTextColor(255, 255, 255);
    $pdf->SetFillColor(30, 58, 138);
    
    // Encabezados de la tabla
    $pdf->Cell(25, 12, 'Hora', 1, 0, 'C', true);
    $pdf->Cell(35, 12, 'Lunes', 1, 0, 'C', true);
    $pdf->Cell(35, 12, 'Martes', 1, 0, 'C', true);
    $pdf->Cell(35, 12, 'Miércoles', 1, 0, 'C', true);
    $pdf->Cell(35, 12, 'Jueves', 1, 0, 'C', true);
    $pdf->Cell(35, 12, 'Viernes', 1, 0, 'C', true);
    $pdf->Cell(35, 12, 'Sábado', 1, 0, 'C', true);
    $pdf->Ln();
    
    // Configurar colores para el contenido
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('helvetica', '', 9);
    
    // Generar filas de horario
    $horas = [];
    for ($i = 6; $i <= 14; $i++) {
        $horas[] = sprintf("%02d:00", $i);
    }
    $dias = ['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado'];
    
    foreach ($horas as $hora) {
        $pdf->SetFillColor(248, 249, 250);
        $pdf->Cell(25, 15, date("h:i A", strtotime($hora)), 1, 0, 'C', true);
        
        foreach ($dias as $dia) {
            $contenido = '';
            if (!empty($curso[$dia])) {
                list($inicio, $fin) = explode(' - ', $curso[$dia]);
                $hora_actual = strtotime($hora);
                $hora_siguiente = strtotime("+1 hour", $hora_actual);
                $hora_inicio = strtotime($inicio);
                $hora_fin = strtotime($fin);
                
                if (($hora_actual >= $hora_inicio && $hora_actual < $hora_fin) ||
                    ($hora_siguiente > $hora_inicio && $hora_siguiente <= $hora_fin) ||
                    ($hora_actual <= $hora_inicio && $hora_siguiente >= $hora_fin)) {
                    $contenido = $curso['nombre_curso'] . "\n" . 
                                date("h:i A", $hora_inicio) . " - " . date("h:i A", $hora_fin);
                }
            }
            
            if (empty($contenido)) {
                $contenido = '-';
            }
            
            $pdf->SetFillColor(255, 255, 255);
            $pdf->MultiCell(35, 15, $contenido, 1, 'C', true);
        }
        $pdf->Ln();
    }
    
    // Pie de página
    $pdf->SetY(-20);
    $pdf->SetFont('helvetica', 'I', 8);
    $pdf->SetTextColor(100, 100, 100);
    $pdf->Cell(0, 10, 'Generado el ' . date('d/m/Y H:i:s'), 0, 0, 'C');
    
    // Descargar PDF
    $filename = 'Horario_' . preg_replace('/[^a-zA-Z0-9_]/', '_', $curso['nombre_curso']) . '.pdf';
    $pdf->Output($filename, 'D');
}

function generarPDFProfesor($horarios, $info_profesor) {
    $pdf = new TCPDF('L', 'mm', 'A4', true, 'UTF-8', false);
    
    // Configurar documento
    $pdf->SetCreator('Sistema de Gestión de Cursos');
    $pdf->SetAuthor('I.E. Sagrado Corazón de Jesús');
    $pdf->SetTitle('Horario de Clases - Profesor ' . $info_profesor['nombre'] . ' ' . $info_profesor['apellido']);
    $pdf->SetSubject('Horario de Clases del Profesor');
    
    // Configurar márgenes
    $pdf->SetMargins(15, 20, 15);
    $pdf->SetHeaderMargin(5);
    $pdf->SetFooterMargin(10);
    
    // Agregar página
    $pdf->AddPage();
    
    // Configurar fuente
    $pdf->SetFont('helvetica', 'B', 20);
    $pdf->SetTextColor(30, 58, 138);
    
    // Título
    $pdf->Cell(0, 15, 'HORARIO DE CLASES', 0, 1, 'C');
    $pdf->SetFont('helvetica', 'B', 16);
    $pdf->Cell(0, 10, 'Profesor: ' . $info_profesor['nombre'] . ' ' . $info_profesor['apellido'], 0, 1, 'C');
    
    // Información del profesor
    $pdf->SetFont('helvetica', '', 10);
    $pdf->SetTextColor(100, 100, 100);
    $pdf->Cell(0, 6, 'Email: ' . $info_profesor['email'], 0, 1, 'C');
    $pdf->Cell(0, 6, 'Teléfono: ' . $info_profesor['telefono'], 0, 1, 'C');
    $pdf->Ln(10);
    
    // Crear tabla de horario
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->SetTextColor(255, 255, 255);
    $pdf->SetFillColor(30, 58, 138);
    
    // Encabezados de la tabla
    $pdf->Cell(25, 12, 'Hora', 1, 0, 'C', true);
    $pdf->Cell(35, 12, 'Lunes', 1, 0, 'C', true);
    $pdf->Cell(35, 12, 'Martes', 1, 0, 'C', true);
    $pdf->Cell(35, 12, 'Miércoles', 1, 0, 'C', true);
    $pdf->Cell(35, 12, 'Jueves', 1, 0, 'C', true);
    $pdf->Cell(35, 12, 'Viernes', 1, 0, 'C', true);
    $pdf->Cell(35, 12, 'Sábado', 1, 0, 'C', true);
    $pdf->Ln();
    
    // Configurar colores para el contenido
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('helvetica', '', 8);
    
    // Generar filas de horario
    $horas = [];
    for ($i = 6; $i <= 14; $i++) {
        $horas[] = sprintf("%02d:00", $i);
    }
    $dias = ['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado'];
    
    foreach ($horas as $hora) {
        $pdf->SetFillColor(248, 249, 250);
        $pdf->Cell(25, 15, date("h:i A", strtotime($hora)), 1, 0, 'C', true);
        
        foreach ($dias as $dia) {
            $contenido = '';
            foreach ($horarios as $horario) {
                if (!empty($horario[$dia])) {
                    list($inicio, $fin) = explode(' - ', $horario[$dia]);
                    $hora_actual = strtotime($hora);
                    $hora_siguiente = strtotime("+1 hour", $hora_actual);
                    $hora_inicio = strtotime($inicio);
                    $hora_fin = strtotime($fin);
                    
                    if (($hora_actual >= $hora_inicio && $hora_actual < $hora_fin) ||
                        ($hora_siguiente > $hora_inicio && $hora_siguiente <= $hora_fin) ||
                        ($hora_actual <= $hora_inicio && $hora_siguiente >= $hora_fin)) {
                        $contenido = $horario['nombre_curso'] . "\n" . 
                                    date("h:i A", $hora_inicio) . " - " . date("h:i A", $hora_fin);
                        break;
                    }
                }
            }
            
            if (empty($contenido)) {
                $contenido = '-';
            }
            
            $pdf->SetFillColor(255, 255, 255);
            $pdf->MultiCell(35, 15, $contenido, 1, 'C', true);
        }
        $pdf->Ln();
    }
    
    // Pie de página
    $pdf->SetY(-20);
    $pdf->SetFont('helvetica', 'I', 8);
    $pdf->SetTextColor(100, 100, 100);
    $pdf->Cell(0, 10, 'Generado el ' . date('d/m/Y H:i:s'), 0, 0, 'C');
    
    // Descargar PDF
    $filename = 'Horario_Profesor_' . preg_replace('/[^a-zA-Z0-9_]/', '_', $info_profesor['nombre'] . '_' . $info_profesor['apellido']) . '.pdf';
    $pdf->Output($filename, 'D');
}
?>
