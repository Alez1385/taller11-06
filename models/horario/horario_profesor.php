<?php
require_once '../../scripts/auth.php';
require_once '../../scripts/conexion.php';
require_once '../../scripts/config.php';
requireLogin();

// Obtener el ID del profesor desde la URL o del usuario actual
$id_profesor = isset($_GET['id_profesor']) ? intval($_GET['id_profesor']) : 0;

if (!$id_profesor) {
    // Si no se proporciona ID, usar el del usuario actual
    $id_usuario = $_SESSION['id_usuario'];
    $sql_profesor = "SELECT id_profesor FROM profesor WHERE id_usuario = ?";
    $stmt_profesor = $conn->prepare($sql_profesor);
    
    if ($stmt_profesor === false) {
        die("Error en la consulta SQL del profesor: " . $conn->error);
    }
    
    $stmt_profesor->bind_param("i", $id_usuario);
    $stmt_profesor->execute();
    $result_profesor = $stmt_profesor->get_result();
    $profesor = $result_profesor->fetch_assoc();

    if (!$profesor) {
        die("No se encontró información del profesor para el usuario ID: " . $id_usuario);
    }

    $id_profesor = $profesor['id_profesor'];
}

// Verificar que el ID del profesor es válido
if ($id_profesor <= 0) {
    die("ID de profesor inválido: " . $id_profesor);
}

// Obtener el último horario por curso para el profesor (evita duplicados históricos)
$sql = "SELECT h.id_horario, c.nombre_curso, c.descripcion, c.duracion,
               h.lunes, h.martes, h.miercoles, h.jueves, h.viernes, h.sabado,
               h.fecha_creacion
        FROM horarios h
        JOIN cursos c ON h.id_curso = c.id_curso
        WHERE h.id_profesor = ?
          AND h.id_horario IN (
              SELECT MAX(h2.id_horario)
              FROM horarios h2
              WHERE h2.id_profesor = ?
              GROUP BY h2.id_curso
          )
        ORDER BY h.fecha_creacion DESC";

$stmt = $conn->prepare($sql);

if ($stmt === false) {
    die("Error en la consulta SQL de horarios: " . $conn->error);
}

$stmt->bind_param("ii", $id_profesor, $id_profesor);
$stmt->execute();
$result = $stmt->get_result();
$horarios = $result->fetch_all(MYSQLI_ASSOC);

// Obtener información del profesor
$sql_info = "SELECT u.nombre, u.apellido, u.telefono 
             FROM profesor p
             JOIN usuario u ON p.id_usuario = u.id_usuario
             WHERE p.id_profesor = ?";
$stmt_info = $conn->prepare($sql_info);

if ($stmt_info === false) {
    die("Error en la consulta SQL: " . $conn->error);
}

$stmt_info->bind_param("i", $id_profesor);
$stmt_info->execute();
$result_info = $stmt_info->get_result();
$info_profesor = $result_info->fetch_assoc();

// Debug: Verificar que tenemos datos
if (!$info_profesor) {
    die("No se encontró información del profesor con ID: " . $id_profesor);
}

// Debug: Mostrar información de debug (solo para desarrollo)
if (isset($_GET['debug'])) {
    echo "<pre>";
    echo "ID Profesor: " . $id_profesor . "\n";
    echo "Horarios encontrados: " . count($horarios) . "\n";
    echo "Info profesor: " . print_r($info_profesor, true) . "\n";
    echo "</pre>";
}

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
        .horario-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            padding: 2rem;
            margin: 2rem 0;
        }
        
        .horario-header {
            text-align: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 3px solid #1e3a8a;
        }
        
        .horario-title {
            color: #00bcff;
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        
        .profesor-subtitle {
            color: #6c757d;
            font-size: 1.2rem;
        }
        
        .profesor-info {
            background: linear-gradient(#00bcff, #00bcff);
            color: white;
            padding: 1.5rem;
            border-radius: 12px;
            margin-bottom: 2rem;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }
        
        .info-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .info-label {
            font-weight: 600;
            font-size: 0.9rem;
            opacity: 0.9;
        }
        
        .info-value {
            font-size: 1rem;
        }
        
        .horario-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .horario-table th {
            background: linear-gradient(#00bcff);
            color: white;
            padding: 1rem;
            font-weight: 600;
            text-align: center;
            font-size: 1rem;
        }
        
        .horario-table td {
            border: 1px solid #e9ecef;
            padding: 0.5rem;
            text-align: center;
            vertical-align: middle;
            min-height: 60px;
            max-width: 120px;
        }
        
        .time-cell {
            background: #f8f9fa;
            font-weight: 600;
            color: #495057;
            width: 120px;
        }
        
        .curso-info {
            background: linear-gradient(135deg, #e3f2fd, #bbdefb);
            padding: 0.6rem;
            border-radius: 6px;
            border-left: 3px solid #1e3a8a;
            margin: 0.3rem 0;
            box-shadow: 0 1px 4px rgba(0,0,0,0.1);
            font-size: 0.85rem;
            word-wrap: break-word;
            overflow: hidden;
        }
        
        .curso-name {
            font-weight: 700;
            color: #1e3a8a;
            font-size: 0.9rem;
            margin-bottom: 0.3rem;
            line-height: 1.2;
        }
        
        .curso-desc {
            color: #6c757d;
            font-size: 0.75rem;
            margin-bottom: 0.3rem;
            line-height: 1.2;
        }
        
        .horario-time {
            color: #6c757d;
            font-size: 0.8rem;
            font-weight: 500;
            line-height: 1.2;
        }
        
        .empty-cell {
            background: #f8f9fa;
            color: #adb5bd;
            font-style: italic;
        }
        
        .action-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            margin: 2rem 0;
            flex-wrap: wrap;
        }
        
        .btn-download {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            border: none;
            padding: 1rem 2rem;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 1rem;
        }
        
        .btn-download:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
        }
        
        .btn-back {
            background: linear-gradient(135deg, #6c757d, #495057);
            color: white;
            border: none;
            padding: 1rem 2rem;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .btn-back:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(108, 117, 125, 0.3);
            color: white;
            text-decoration: none;
        }
        
        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            padding: 1.5rem;
            border-radius: 8px;
            text-align: center;
            border-left: 4px solid #1e3a8a;
        }
        
        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            color: #1e3a8a;
            margin-bottom: 0.5rem;
        }
        
        .stat-label {
            color: #6c757d;
            font-size: 0.9rem;
            font-weight: 500;
        }
        
        @media print {
            .action-buttons, .btn-back {
                display: none !important;
            }
            
            .horario-container {
                box-shadow: none;
                border: 1px solid #ddd;
            }
        }
        
        @media (max-width: 768px) {
            .horario-table {
                font-size: 0.8rem;
            }
            
            .horario-table th, .horario-table td {
                padding: 0.5rem;
            }
            
            .action-buttons {
                flex-direction: column;
                align-items: center;
            }
            
            .profesor-info {
                grid-template-columns: 1fr;
            }
        }
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
                <div class="header-right">
                    <a href="../../dashboard/dashboard.php" class="btn-back">
                        <i class="material-icons-sharp">arrow_back</i>
                        Volver al Dashboard
                    </a>
                </div>
            </header>

            <section class="content">
                <div class="horario-container" id="horarioContainer">
                    <div class="horario-header">
                        <h2 class="horario-title">Horario de Clases</h2>
                        <p class="profesor-subtitle">Profesor: <?php echo htmlspecialchars($info_profesor['nombre'] . ' ' . $info_profesor['apellido']); ?></p>
                    </div>
                    
                    <div class="profesor-info">
                        <div class="info-item">
                            <i class="material-icons-sharp">person</i>
                            <div>
                                <div class="info-label">Nombre</div>
                                <div class="info-value"><?php echo htmlspecialchars($info_profesor['nombre'] . ' ' . $info_profesor['apellido']); ?></div>
                            </div>
                        </div>
                        <div class="info-item">
                            <i class="material-icons-sharp">work</i>
                            <div>
                                <div class="info-label">Cursos Asignados</div>
                                <div class="info-value"><?php echo count($horarios); ?> curso(s)</div>
                            </div>
                        </div>
                        <div class="info-item">
                            <i class="material-icons-sharp">phone</i>
                            <div>
                                <div class="info-label">Teléfono</div>
                                <div class="info-value"><?php echo htmlspecialchars($info_profesor['telefono']); ?></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="stats-container">
                        <div class="stat-card">
                            <div class="stat-number"><?php echo count($horarios); ?></div>
                            <div class="stat-label">Cursos Asignados</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-number"><?php 
                                $total_horas = 0;
                                foreach ($horarios as $horario) {
                                    $dias = ['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado'];
                                    foreach ($dias as $dia) {
                                        if (!empty($horario[$dia])) {
                                            $total_horas++;
                                        }
                                    }
                                }
                                echo $total_horas;
                            ?></div>
                            <div class="stat-label">Horas de Clase por Semana</div>
                        </div>
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
                            // Solo mostrar horas de 6:00 AM a 2:00 PM
                            $horas = [];
                            for ($i = 6; $i <= 14; $i++) {
                                $horas[] = sprintf("%02d:00", $i);
                            }
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
                                    
                                    if (!$curso_encontrado) {
                                        echo "<div class='empty-cell'>-</div>";
                                    }
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
            
            // Mostrar indicador de carga
            const btn = document.querySelector('.btn-download');
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="material-icons-sharp">hourglass_empty</i> Generando PDF...';
            btn.disabled = true;
            
            html2canvas(element, {
                scale: 1.2,
                useCORS: true,
                allowTaint: true,
                backgroundColor: '#ffffff',
                scrollX: 0,
                scrollY: 0,
                windowWidth: element.scrollWidth,
                windowHeight: element.scrollHeight
            }).then(canvas => {
                const imgData = canvas.toDataURL('image/png');
                const pdf = new jsPDF('l', 'mm', 'a4'); // Orientación horizontal
                
                const imgWidth = 297; // A4 width in mm
                const pageHeight = 210; // A4 height in mm
                const imgHeight = (canvas.height * imgWidth) / canvas.width;
                
                // Si la imagen es más alta que la página, dividir en múltiples páginas
                if (imgHeight > pageHeight) {
                    const totalPages = Math.ceil(imgHeight / pageHeight);
                    
                    for (let i = 0; i < totalPages; i++) {
                        if (i > 0) {
                            pdf.addPage();
                        }
                        
                        const yOffset = -i * pageHeight;
                        const currentPageHeight = Math.min(pageHeight, imgHeight - (i * pageHeight));
                        
                        pdf.addImage(imgData, 'PNG', 0, yOffset, imgWidth, imgHeight);
                    }
                } else {
                    // Centrar verticalmente si cabe en una página
                    const yPosition = (pageHeight - imgHeight) / 2;
                    pdf.addImage(imgData, 'PNG', 0, yPosition, imgWidth, imgHeight);
                }
                
                // Descargar el PDF
                const profesorName = '<?php echo htmlspecialchars($info_profesor['nombre'] . '_' . $info_profesor['apellido']); ?>';
                pdf.save(`Horario_Profesor_${profesorName.replace(/\s+/g, '_')}.pdf`);
                
                // Restaurar botón
                btn.innerHTML = originalText;
                btn.disabled = false;
            }).catch(error => {
                console.error('Error al generar PDF:', error);
                alert('Error al generar el PDF. Por favor, inténtelo de nuevo.');
                
                // Restaurar botón
                btn.innerHTML = originalText;
                btn.disabled = false;
            });
        }
        
        // Agregar animaciones de entrada
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.getElementById('horarioContainer');
            container.style.opacity = '0';
            container.style.transform = 'translateY(20px)';
            
            setTimeout(() => {
                container.style.transition = 'all 0.6s ease';
                container.style.opacity = '1';
                container.style.transform = 'translateY(0)';
            }, 100);
        });
    </script>
</body>
</html>
