<?php
require_once '../../scripts/auth.php';
require_once '../../scripts/conexion.php';
require_once '../../scripts/config.php';
requireLogin();
checkPermission('estudiante');

// Obtener id_estudiante desde el usuario
$id_usuario = $_SESSION['id_usuario'];
$stmt_est = $conn->prepare("SELECT id_estudiante FROM estudiante WHERE id_usuario = ?");
if ($stmt_est === false) {
    die("Error en la consulta de estudiante: " . $conn->error);
}
$stmt_est->bind_param("i", $id_usuario);
$stmt_est->execute();
$res_est = $stmt_est->get_result();
$row_est = $res_est->fetch_assoc();
if (!$row_est) {
    die("No se encontró el estudiante para el usuario.");
}
$id_estudiante = $row_est['id_estudiante'];
$stmt_est->close();

// Traer el último horario por curso de todos los cursos donde el estudiante está inscrito (aprobada)
$sql = "SELECT h.id_horario, c.nombre_curso, c.descripcion, c.duracion,
               h.lunes, h.martes, h.miercoles, h.jueves, h.viernes, h.sabado,
               h.fecha_creacion
        FROM inscripciones i
        JOIN cursos c ON i.id_curso = c.id_curso
        JOIN horarios h ON h.id_curso = c.id_curso
        WHERE i.id_estudiante = ? AND i.estado = 'aprobada'
          AND h.id_horario IN (
              SELECT MAX(h2.id_horario)
              FROM horarios h2
              WHERE h2.id_curso = c.id_curso
          )
        ORDER BY h.fecha_creacion DESC";

$stmt = $conn->prepare($sql);
if ($stmt === false) {
    die("Error en la consulta SQL de horarios: " . $conn->error);
}
$stmt->bind_param("i", $id_estudiante);
$stmt->execute();
$result = $stmt->get_result();
$horarios = $result->fetch_all(MYSQLI_ASSOC);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Horario de Clases</title>
    <link rel="stylesheet" href="css/horarios_asignados.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons+Sharp">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <style>
        .horario-container {background: white;border-radius: 12px;box-shadow: 0 4px 20px rgba(0,0,0,0.1);padding: 2rem;margin: 2rem 0;}
        .horario-header {text-align: center;margin-bottom: 2rem;padding-bottom: 1rem;border-bottom: 3px solid #00bcff;}
        .horario-title {color: #00bcff;font-size: 2rem;font-weight: 700;margin-bottom: 0.5rem;}
        .horario-table {width: 100%;border-collapse: collapse;margin-top: 20px;background: white;border-radius: 8px;overflow: hidden;}
        .horario-table th {background: linear-gradient(#00bcff);color: white;padding: 1rem;font-weight: 600;text-align: center;font-size: 1rem;}
        .horario-table td {border: 1px solid #e9ecef;padding: 0.5rem;text-align: center;vertical-align: middle;min-height: 60px;max-width: 120px;}
        .time-cell {background: #f8f9fa;font-weight: 600;color: #495057;width: 120px;}
        .curso-info {background: linear-gradient(135deg, #e3f2fd, #bbdefb);padding: 0.6rem;border-radius: 6px;border-left: 3px solid #1e3a8a;margin: 0.3rem 0;box-shadow: 0 1px 4px rgba(0,0,0,0.1);font-size: 0.85rem;word-wrap: break-word;overflow: hidden;}
        .curso-name {font-weight: 700;color: #1e3a8a;font-size: 0.9rem;margin-bottom: 0.3rem;line-height: 1.2;}
        .curso-desc {color: #6c757d;font-size: 0.75rem;margin-bottom: 0.3rem;line-height: 1.2;}
        .horario-time {color: #6c757d;font-size: 0.8rem;font-weight: 500;line-height: 1.2;}
        .empty-cell {background: #f8f9fa;color: #adb5bd;font-style: italic;}
        .action-buttons {display: flex;gap: 1rem;justify-content: center;margin: 2rem 0;flex-wrap: wrap;}
        .btn-download {background: linear-gradient(135deg, #28a745, #20c997);color: white;border: none;padding: 1rem 2rem;border-radius: 8px;font-weight: 600;cursor: pointer;transition: all 0.3s ease;display: flex;align-items: center;gap: 0.5rem;font-size: 1rem;}
        .btn-download:hover {transform: translateY(-2px);box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);}
        @media (max-width: 768px) {.horario-table {font-size: 0.8rem;} .horario-table th, .horario-table td {padding: 0.5rem;}}
    </style>
</head>
<body>
    <div class="dashboard-container">
        <?php include "../../scripts/sidebar.php"; ?>
        <div class="main-content">
            <header class="header">
                <div class="header-left">
                    <h1>Mi Horario de Clases</h1>
                </div>
            </header>

            <section class="content">
                <div class="horario-container" id="horarioContainer">
                    <div class="horario-header">
                        <h2 class="horario-title">Horario de Clases</h2>
                    </div>

                    <table class="horario-table">
                        <thead>
                            <tr>
                                <th class="time-cell">Hora</th>
                                <th>Lunes</th>
                                <th>Martes</th>
                                <th>Miércoles</th>
                                <th>Jueves</th>
                                <th>Viernes</th>
                                <th>Sábado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $horas = [];
                            for ($i = 6; $i <= 14; $i++) {$horas[] = sprintf("%02d:00", $i);}    
                            $dias = ['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado'];

                            foreach ($horas as $hora) {
                                echo "<tr>";
                                echo "<td class='time-cell'>" . date("h:i A", strtotime($hora)) . "</td>";
                                foreach ($dias as $dia) {
                                    echo "<td>";
                                    $curso_encontrado = false;
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
                                                echo "<div class='curso-info'>";
                                                echo "<div class='curso-name'>" . htmlspecialchars($horario['nombre_curso']) . "</div>";
                                                echo "<div class='curso-desc'>" . htmlspecialchars($horario['descripcion']) . "</div>";
                                                echo "<div class='horario-time'>" . date("h:i A", $hora_inicio) . " - " . date("h:i A", $hora_fin) . "</div>";
                                                echo "</div>";
                                                $curso_encontrado = true;
                                                break;
                                            }
                                        }
                                    }
                                    if (!$curso_encontrado) {echo "<div class='empty-cell'>-</div>";}
                                    echo "</td>";
                                }
                                echo "</tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

                <div class="action-buttons">
                    <button onclick="descargarPDF()" class="btn-download">
                        <i class="material-icons-sharp">download</i>
                        Descargar PDF
                    </button>
                </div>
            </section>
        </div>
    </div>

    <script>
        function descargarPDF() {
            const { jsPDF } = window.jspdf;
            const element = document.getElementById('horarioContainer');
            const btn = document.querySelector('.btn-download');
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="material-icons-sharp">hourglass_empty</i> Generando PDF...';
            btn.disabled = true;
            html2canvas(element, {scale: 1.2, useCORS: true, allowTaint: true, backgroundColor: '#ffffff', scrollX: 0, scrollY: 0, windowWidth: element.scrollWidth, windowHeight: element.scrollHeight})
            .then(canvas => {
                const imgData = canvas.toDataURL('image/png');
                const pdf = new jsPDF('l', 'mm', 'a4');
                const imgWidth = 297; const pageHeight = 210; const imgHeight = (canvas.height * imgWidth) / canvas.width;
                if (imgHeight > pageHeight) {const totalPages = Math.ceil(imgHeight / pageHeight); for (let i = 0; i < totalPages; i++) {if (i > 0) {pdf.addPage();} const yOffset = -i * pageHeight; pdf.addImage(imgData, 'PNG', 0, yOffset, imgWidth, imgHeight);} } else {const yPosition = (pageHeight - imgHeight) / 2; pdf.addImage(imgData, 'PNG', 0, yPosition, imgWidth, imgHeight);} pdf.save('Mi_Horario.pdf'); btn.innerHTML = originalText; btn.disabled = false;})
            .catch(() => {alert('Error al generar el PDF'); btn.innerHTML = originalText; btn.disabled = false;});
        }
        document.addEventListener('DOMContentLoaded', function() {const container = document.getElementById('horarioContainer'); container.style.opacity = '0'; container.style.transform = 'translateY(20px)'; setTimeout(() => {container.style.transition = 'all 0.6s ease'; container.style.opacity = '1'; container.style.transform = 'translateY(0)';}, 100);});
    </script>
</body>
</html>









