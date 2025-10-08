<?php
require_once '../../scripts/auth.php';
require_once '../../scripts/conexion.php';
require_once '../../scripts/config.php';
requireLogin();

$id_curso = isset($_GET['id_curso']) ? intval($_GET['id_curso']) : 0;

if (!$id_curso) {
    die("ID de curso no válido.");
}

// Consulta SQL simplificada para obtener un solo registro de horario por curso
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

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Horario del Curso</title>
    <link rel="stylesheet" href="css/horarios_asignados.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons+Sharp">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <style>
        .horario-container {
            background: white;
            border-radius: 12px;
           
            padding: 2rem;
            margin: 2rem 0;
        }
        
        .horario-header {
            text-align: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 3px solid #00bcff;
        }
        
        .horario-title {
            color: #00bcff;
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        
        .curso-subtitle {
            color: #6c757d;
            font-size: 1.1rem;
        }
        
        .profesor-info {
            background: linear-gradient( #00bcff, #00bcff);
            color: white;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 2rem;
            text-align: center;
        }
        
        .profesor-name {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        
        .horario-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            
        }
        
        .horario-table th {
            background: linear-gradient(#00bcff, #00bcff);
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
            min-height: 50px;
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
                    <a href="cursos_listado.php" class="btn-back">
                        <i class="material-icons-sharp">arrow_back</i>
                        Volver a Mis Cursos
                    </a>
                </div>
            </header>

            <section class="content">
                <div class="horario-container" id="horarioContainer">
                    <div class="horario-header">
                        <h2 class="horario-title"><?php echo htmlspecialchars($curso['nombre_curso']); ?></h2>
                        <p class="curso-subtitle">Horario de Clases</p>
                    </div>
                    
                    <div class="profesor-info">
                        <div class="profesor-name">
                            <i class="material-icons-sharp">person</i>
                            Profesor: <?php echo htmlspecialchars($curso['nombre_profesor']); ?>
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
                                    if (!empty($curso[$dia])) {
                                        list($inicio, $fin) = explode(' - ', $curso[$dia]);
                                        $hora_actual = strtotime($hora);
                                        $hora_siguiente = strtotime("+1 hour", $hora_actual);
                                        $hora_inicio = strtotime($inicio);
                                        $hora_fin = strtotime($fin);
                                        
                                        if (($hora_actual >= $hora_inicio && $hora_actual < $hora_fin) ||
                                            ($hora_siguiente > $hora_inicio && $hora_siguiente <= $hora_fin) ||
                                            ($hora_actual <= $hora_inicio && $hora_siguiente >= $hora_fin)) {
                                            echo "<div class='curso-info'>";
                                            echo "<div class='curso-name'>" . htmlspecialchars($curso['nombre_curso']) . "</div>";
                                            echo "<div class='horario-time'>" . date("h:i A", $hora_inicio) . " - " . date("h:i A", $hora_fin) . "</div>";
                                            echo "</div>";
                                        } else {
                                            echo "<div class='empty-cell'>-</div>";
                                        }
                                    } else {
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
                scale: 1.5,
                useCORS: true,
                allowTaint: true,
                backgroundColor: '#ffffff',
                height: 800, // Limitar altura
                width: 1200  // Limitar ancho
            }).then(canvas => {
                const imgData = canvas.toDataURL('image/png');
                const pdf = new jsPDF('l', 'mm', 'a4'); // Orientación horizontal
                
                const imgWidth = 297; // A4 width in mm
                const pageHeight = 210; // A4 height in mm
                const imgHeight = (canvas.height * imgWidth) / canvas.width;
                
                // Ajustar para que quepa en una página
                let finalHeight = imgHeight;
                if (imgHeight > pageHeight) {
                    finalHeight = pageHeight;
                }
                
                // Centrar verticalmente si es necesario
                const yPosition = (pageHeight - finalHeight) / 2;
                
                // Agregar imagen al PDF centrada
                pdf.addImage(imgData, 'PNG', 0, yPosition, imgWidth, finalHeight);
                
                // Descargar el PDF
                const cursoName = '<?php echo htmlspecialchars($curso['nombre_curso']); ?>';
                pdf.save(`Horario_${cursoName.replace(/\s+/g, '_')}.pdf`);
                
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
