<?php
require_once '../../scripts/auth.php';
require_once '../../scripts/conexion.php';
require_once '../../scripts/config.php';
requireLogin();
checkPermission('estudiante');

// Obtener el id_estudiante correcto
$id_usuario = $_SESSION['id_usuario'];
$sql_estudiante = "SELECT id_estudiante FROM estudiante WHERE id_usuario = ?";
$stmt_estudiante = $conn->prepare($sql_estudiante);
$stmt_estudiante->bind_param("i", $id_usuario);
$stmt_estudiante->execute();
$result_estudiante = $stmt_estudiante->get_result();
$estudiante = $result_estudiante->fetch_assoc();
$id_estudiante = $estudiante['id_estudiante'];

$id_curso = isset($_GET['id_curso']) ? intval($_GET['id_curso']) : 0;

if ($id_curso == 0) {
    die("ID de curso no válido");
}

// Obtener información del curso
$sql_curso = "SELECT nombre_curso FROM cursos WHERE id_curso = ?";
$stmt_curso = $conn->prepare($sql_curso);
$stmt_curso->bind_param("i", $id_curso);
$stmt_curso->execute();
$result_curso = $stmt_curso->get_result();
$curso = $result_curso->fetch_assoc();

// Obtener detalles de asistencia
$sql_asistencias = "SELECT a.fecha, a.estado
                    FROM asistencia a
                    WHERE a.id_estudiante = ? AND a.id_curso = ?
                    ORDER BY a.fecha DESC";
$stmt_asistencias = $conn->prepare($sql_asistencias);
if (!$stmt_asistencias) {
    die("Error en la preparación de la consulta: " . $conn->error);
}
$stmt_asistencias->bind_param("ii", $id_estudiante, $id_curso);
$stmt_asistencias->execute();
$result_asistencias = $stmt_asistencias->get_result();

// Calcular estadísticas
$total_clases = $result_asistencias->num_rows;
$asistencias = 0;
$inasistencias = 0;
$retardos = 0;


while ($row = $result_asistencias->fetch_assoc()) {
    switch ($row['estado']) {
        case 'presente':
            $asistencias++;
            break;
        case 'ausente':
            $inasistencias++;
            break;
        case 'retardo':
            $retardos++;
            break;
    }
}

$porcentaje_asistencia = $total_clases > 0 ? (($asistencias + $retardos) / $total_clases) * 100 : 0;



?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle de Asistencia - <?php echo htmlspecialchars($curso['nombre_curso']); ?></title>
    <link rel="stylesheet" href="../cursos/cursos.css">
    <link rel="stylesheet" href="css/asistencia.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons+Sharp">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .asistencia-presente { color: #28a745; font-weight: 600; }
        .asistencia-ausente { color: #dc3545; font-weight: 600; }
        .asistencia-retardo { color: #ffc107; font-weight: 600; }
        
        .stats-overview {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: #00bcff;
            color: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            text-align: center;
        }
        
        .stat-card h3 {
            margin: 0 0 10px 0;
            font-size: 0.9rem;
            opacity: 0.9;
        }
        
        .stat-card .number {
            font-size: 2.5rem;
            font-weight: bold;
            margin: 0;
        }
        
        .stat-card .icon {
            font-size: 2rem;
            opacity: 0.7;
            margin-top: 10px;
        }
        
        
        .asistencia-detalle {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            border: 1px solid #e5e7eb;
        }
        
        .asistencia-detalle h2 {
            margin-top: 0;
            margin-bottom: 20px;
            color: #00bcff;
            font-size: 1.5rem;
        }
        
        .asistencia-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .asistencia-table th {
            background: #00bcff;
            color: white;
            padding: 15px;
            font-weight: 600;
            text-align: center;
            font-size: 1rem;
        }
        
        .asistencia-table td {
            border: 1px solid #e5e7eb;
            padding: 12px;
            text-align: center;
            vertical-align: middle;
        }
        
        .asistencia-table tbody tr:nth-child(even) {
            background: #f8f9fa;
        }
        
        .asistencia-table tbody tr:hover {
            background: #e3f2fd;
        }
        
        .btn-back {
            background: #f8f9fa;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 8px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-weight: 600;
            margin-bottom: 20px;
        }
        
        .btn-back:hover {
            background: #5a6268;
            color: white;
            text-decoration: none;
        }
        
        .chart-container {
            background: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 30px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            border: 1px solid #e5e7eb;
        }
        
        .chart-container h3 {
            margin-top: 0;
            margin-bottom: 20px;
            color: #00bcff;
            font-size: 1.3rem;
        }
        
        .chart-wrapper {
            height: 300px;
            position: relative;
        }
        
        @media (max-width: 768px) {
            .stats-overview {
                grid-template-columns: 1fr;
            }
            
            .asistencia-table {
                font-size: 0.9rem;
            }
            
            .asistencia-table th,
            .asistencia-table td {
                padding: 8px;
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
                    <h1>Detalle de Asistencia - <?php echo htmlspecialchars($curso['nombre_curso']); ?></h1>
                </div>
                <div class="header-right">
                    <a href="asistencia_estudiante.php" class="btn-back">
                        <span class="material-icons-sharp">arrow_back</span>
                        Volver
                    </a>
                </div>
            </header>

            <section class="content">
                <!-- Estadísticas Generales -->
                <div class="stats-overview">
                    <div class="stat-card">
                        <h3>Total de Clases</h3>
                        <p class="number asistencia_color_text"><?php echo $total_clases; ?></p>
                        <span class="material-icons-sharp icon">event</span>
                    </div>
                    
                    <div class="stat-card">
                        <h3>Asistencias</h3>
                        <p class="number asistencia_color_text"><?php echo $asistencias; ?></p>
                        <span class="material-icons-sharp icon">check_circle</span>
                    </div>
                    
                    <div class="stat-card">
                        <h3>Inasistencias</h3>
                        <p class="number asistencia_color_text"><?php echo $inasistencias; ?></p>
                        <span class="material-icons-sharp icon">cancel</span>
                    </div>
                    
                    <div class="stat-card">
                        <h3>Retardos</h3>
                        <p class="number asistencia_color_text"><?php echo $retardos; ?></p>
                        <span class="material-icons-sharp icon">schedule</span>
                    </div>
                    
                    <div class="stat-card">
                        <h3>Porcentaje</h3>
                        <p class="number asistencia_color_text"><?php echo number_format($porcentaje_asistencia, 1); ?>%</p>
                        <span class="material-icons-sharp icon">trending_up</span>
                    </div>
                </div>

                <!-- Gráfico de Asistencia -->
                <div class="chart-container">
                    <h3>Distribución de Asistencia</h3>
                    <div class="chart-wrapper">
                        <canvas id="attendanceChart"></canvas>
                    </div>
                </div>


                <!-- Registro Detallado -->
                <div class="asistencia-detalle">
                    <h2>Registro de Asistencias</h2>
                    <table class="asistencia-table">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Día de la Semana</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $result_asistencias->data_seek(0); // Reiniciar el puntero del resultado
                            while ($asistencia = $result_asistencias->fetch_assoc()):
                                $fecha = new DateTime($asistencia['fecha']);
                                $dias_semana = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
                                $dia_semana = $dias_semana[$fecha->format('w')];
                            ?>
                            <tr>
                                <td><?php echo $fecha->format('d/m/Y'); ?></td>
                                <td><?php echo $dia_semana; ?></td>
                                <td>
                                    <?php
                                    switch ($asistencia['estado']) {
                                        case 'presente':
                                            echo '<span class="asistencia-presente">✓ Presente</span>';
                                            break;
                                        case 'ausente':
                                            echo '<span class="asistencia-ausente">✗ Ausente</span>';
                                            break;
                                        case 'retardo':
                                            echo '<span class="asistencia-retardo">⏰ Retardo</span>';
                                            break;
                                        default:
                                            echo 'Desconocido';
                                    }
                                    ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </div>

    <script>
        // Crear gráfico de asistencia
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('attendanceChart').getContext('2d');
            
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Asistencias', 'Inasistencias', 'Retardos'],
                    datasets: [{
                        data: [<?php echo $asistencias; ?>, <?php echo $inasistencias; ?>, <?php echo $retardos; ?>],
                        backgroundColor: ['#28a745', '#dc3545', '#ffc107'],
                        borderWidth: 0,
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 20,
                                usePointStyle: true,
                                font: {
                                    size: 14
                                }
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = total > 0 ? ((context.parsed / total) * 100).toFixed(1) : 0;
                                    return context.label + ': ' + context.parsed + ' (' + percentage + '%)';
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
</body>
</html>
