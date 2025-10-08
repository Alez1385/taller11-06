<?php
require_once '../../scripts/auth.php';
require_once '../../scripts/conexion.php';
require_once '../../scripts/config.php';
requireLogin();
checkPermission('profesor');

$id_profesor = $_SESSION['id_usuario'];

// Obtener los cursos asignados al profesor con información adicional
$sql_cursos = "SELECT c.id_curso, c.nombre_curso, c.descripcion, c.icono, c.nivel_educativo, c.duracion,
               CASE 
                   WHEN COUNT(DISTINCT h.id_horario) > 0 THEN 
                       GROUP_CONCAT(DISTINCT CONCAT(h.dia_semana, ' ', h.hora_inicio, '-', h.hora_fin) SEPARATOR ', ')
                   ELSE 'Sin horarios asignados'
               END AS horarios,
               COUNT(DISTINCT i.id_estudiante) AS num_estudiantes
               FROM cursos c
               INNER JOIN asignacion_curso ac ON c.id_curso = ac.id_curso
               INNER JOIN profesor p ON ac.id_profesor = p.id_profesor
               LEFT JOIN horarios h ON c.id_curso = h.id_curso
               LEFT JOIN inscripciones i ON c.id_curso = i.id_curso
               WHERE p.id_usuario = ?
               GROUP BY c.id_curso";
$stmt_cursos = $conn->prepare($sql_cursos);
$stmt_cursos->bind_param("i", $id_profesor);
$stmt_cursos->execute();
$result_cursos = $stmt_cursos->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Asistencias</title>
    <link rel="stylesheet" href="../cursos/cursos.css">
    <link rel="stylesheet" href="css/asistencia.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons+Sharp">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: #00bcff;
            color: white;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
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
            float: right;
            margin-top: -10px;
        }
        
        .filters-section {
            background: white;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        
        .filters-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            align-items: end;
        }
        
        .filter-group {
            display: flex;
            flex-direction: column;
        }
        
        .filter-group label {
            font-weight: 600;
            margin-bottom: 5px;
            color: #333;
        }
        
        .filter-group select,
        .filter-group input {
            padding: 10px;
            border: 2px solid #e1e5e9;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.3s ease;
        }
        
        .filter-group select:focus,
        .filter-group input:focus {
            outline: none;
            border-color: #667eea;
        }
        
        .btn-filter {
            background: #00bcff;
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
        }
        
        .btn-filter:hover {
            background: #1e40af;
        }
        
        .course-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }
        
        .course-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            overflow: hidden;
            border: 1px solid #e5e7eb;
        }
        
        .course-header {
            background: #00bcff;
            color: white;
            padding: 15px;
        }
        
        .course-title {
            font-size: 1.2rem;
            font-weight: 700;
            margin: 0 0 8px 0;
        }
        
        .course-subtitle {
            font-size: 0.85rem;
            opacity: 0.9;
        }
        
        .course-body {
            padding: 15px;
        }
        
        .course-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-bottom: 15px;
        }
        
        .info-item {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .info-item .icon {
            color: #00bcff;
            font-size: 1.1rem;
        }
        
        .info-item .text {
            font-size: 0.9rem;
            color: #666;
        }
        
        .course-stats {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 12px;
            margin-bottom: 15px;
        }
        
        .stats-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
        }
        
        .stats-row:last-child {
            margin-bottom: 0;
        }
        
        .stats-label {
            font-size: 0.9rem;
            color: #666;
        }
        
        .stats-value {
            font-weight: 600;
            color: #333;
        }
        
        .course-actions {
            display: flex;
            gap: 10px;
        }
        
        .btn-primary {
            background: #28a745;
            color: white;
            border: none;
            padding: 10px 16px;
            border-radius: 8px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-weight: 600;
            flex: 1;
            justify-content: center;
            font-size: 0.9rem;
        }
        
        .btn-primary:hover {
            background: #218838;
            color: white;
            text-decoration: none;
        }
        
        .no-courses {
            text-align: center;
            padding: 60px 20px;
            color: #666;
        }
        
        .no-courses .icon {
            font-size: 4rem;
            color: #ddd;
            margin-bottom: 20px;
        }
        
        .no-courses h3 {
            margin: 0 0 10px 0;
            color: #333;
        }
        
        .no-courses p {
            margin: 0;
            font-size: 1.1rem;
        }
        
        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .filters-grid {
                grid-template-columns: 1fr;
            }
            
            .course-grid {
                grid-template-columns: 1fr;
            }
            
            .course-info {
                grid-template-columns: 1fr;
            }
            
            .course-actions {
                flex-direction: column;
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
                    <h1>Gestión de Asistencias</h1>
                </div>
            </header>

            <section class="content">
                <!-- Estadísticas Generales -->
                <div class="stats-grid">
                    <?php
                    $total_cursos = $result_cursos->num_rows;
                    $total_estudiantes = 0;
                    $cursos_con_asistencia = 0;
                    
                    // Calcular estadísticas
                    $result_cursos->data_seek(0);
                    while ($curso = $result_cursos->fetch_assoc()) {
                        $total_estudiantes += $curso['num_estudiantes'];
                        
                        // Verificar si hay asistencias registradas para este curso
                        $sql_check = "SELECT COUNT(*) as count FROM asistencia WHERE id_curso = ?";
                        $stmt_check = $conn->prepare($sql_check);
                        $stmt_check->bind_param("i", $curso['id_curso']);
                        $stmt_check->execute();
                        $result_check = $stmt_check->get_result();
                        $check = $result_check->fetch_assoc();
                        
                        if ($check['count'] > 0) {
                            $cursos_con_asistencia++;
                        }
                    }
                    ?>
                    
                    <div class="stat-card">
                        <h3>Total de Cursos</h3>
                        <p class="number"><?php echo $total_cursos; ?></p>
                        <span class="material-icons-sharp icon">school</span>
                    </div>
                    
                    <div class="stat-card">
                        <h3>Estudiantes Totales</h3>
                        <p class="number"><?php echo $total_estudiantes; ?></p>
                        <span class="material-icons-sharp icon">people</span>
                    </div>
                    
                    <div class="stat-card">
                        <h3>Cursos con Asistencia</h3>
                        <p class="number"><?php echo $cursos_con_asistencia; ?></p>
                        <span class="material-icons-sharp icon">check_circle</span>
                    </div>
                    
                    <div class="stat-card">
                        <h3>Promedio por Curso</h3>
                        <p class="number"><?php echo $total_cursos > 0 ? round($total_estudiantes / $total_cursos, 1) : 0; ?></p>
                        <span class="material-icons-sharp icon">trending_up</span>
                    </div>
                </div>

                <!-- Filtros -->
                <div class="filters-section">
                    <h3 style="margin-top: 0; margin-bottom: 20px; color: #333;">
                        <span class="material-icons-sharp" style="vertical-align: middle; margin-right: 8px;">filter_list</span>
                        Filtros de Búsqueda
                    </h3>
                    <div class="filters-grid">
                        <div class="filter-group">
                            <label for="filtro_curso">Curso:</label>
                            <select id="filtro_curso">
                                <option value="">Todos los cursos</option>
                                <?php
                                $result_cursos->data_seek(0);
                                while ($curso = $result_cursos->fetch_assoc()): 
                                ?>
                                    <option value="<?php echo $curso['id_curso']; ?>">
                                        <?php echo htmlspecialchars($curso['nombre_curso']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        
                        <div class="filter-group">
                            <label for="filtro_nivel">Nivel Educativo:</label>
                            <select id="filtro_nivel">
                                <option value="">Todos los niveles</option>
                                <option value="basica">Básica</option>
                                <option value="media">Media</option>
                                <option value="superior">Superior</option>
                            </select>
                        </div>
                        
                        <div class="filter-group">
                            <label for="filtro_estudiantes">Estudiantes:</label>
                            <select id="filtro_estudiantes">
                                <option value="">Cualquier cantidad</option>
                                <option value="1-10">1-10 estudiantes</option>
                                <option value="11-20">11-20 estudiantes</option>
                                <option value="21+">21+ estudiantes</option>
                            </select>
                        </div>
                        
                        <div class="filter-group">
                            <button type="button" class="btn-filter" onclick="aplicarFiltros()">
                                <span class="material-icons-sharp" style="font-size: 18px; margin-right: 5px;">search</span>
                                Filtrar
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Lista de Cursos -->
                <div class="course-grid" id="courseGrid">
                    <?php 
                    $result_cursos->data_seek(0);
                    if ($result_cursos->num_rows > 0):
                        while ($curso = $result_cursos->fetch_assoc()): 
                            // Obtener estadísticas de asistencia para este curso
                            $sql_stats = "SELECT 
                                COUNT(DISTINCT fecha) as total_clases,
                                COUNT(CASE WHEN estado = 'presente' THEN 1 END) as total_presentes,
                                COUNT(CASE WHEN estado = 'ausente' THEN 1 END) as total_ausentes,
                                COUNT(CASE WHEN estado = 'retardo' THEN 1 END) as total_retardos
                                FROM asistencia 
                                WHERE id_curso = ?";
                            $stmt_stats = $conn->prepare($sql_stats);
                            $stmt_stats->bind_param("i", $curso['id_curso']);
                            $stmt_stats->execute();
                            $result_stats = $stmt_stats->get_result();
                            $stats = $result_stats->fetch_assoc();
                            
                            $porcentaje_asistencia = $stats['total_clases'] > 0 ? 
                                round((($stats['total_presentes'] + $stats['total_retardos']) / ($stats['total_clases'] * $curso['num_estudiantes'])) * 100, 1) : 0;
                    ?>
                        <div class="course-card" data-curso="<?php echo $curso['id_curso']; ?>" 
                             data-nivel="<?php echo strtolower($curso['nivel_educativo']); ?>"
                             data-estudiantes="<?php echo $curso['num_estudiantes']; ?>">
                            <div class="course-header">
                                <h3 class="course-title"><?php echo htmlspecialchars($curso['nombre_curso']); ?></h3>
                                <p class="course-subtitle"><?php echo htmlspecialchars($curso['descripcion']); ?></p>
                            </div>
                            
                            <div class="course-body">
                                <div class="course-info">
                                    <div class="info-item">
                                        <span class="material-icons-sharp icon">school</span>
                                        <span class="text"><?php echo htmlspecialchars(ucfirst($curso['nivel_educativo'])); ?></span>
                                    </div>
                                    <div class="info-item">
                                        <span class="material-icons-sharp icon">schedule</span>
                                        <span class="text"><?php echo htmlspecialchars($curso['duracion']); ?> semanas</span>
                                    </div>
                                    <div class="info-item">
                                        <span class="material-icons-sharp icon">people</span>
                                        <span class="text"><?php echo $curso['num_estudiantes']; ?> estudiantes</span>
                                    </div>
                                    <div class="info-item">
                                        <span class="material-icons-sharp icon">event</span>
                                        <span class="text"><?php echo htmlspecialchars($curso['horarios'] ?: 'Sin horarios'); ?></span>
                                    </div>
                                </div>
                                
                                <div class="course-stats">
                                    <div class="stats-row">
                                        <span class="stats-label">Total de Clases:</span>
                                        <span class="stats-value"><?php echo $stats['total_clases']; ?></span>
                                    </div>
                                    <div class="stats-row">
                                        <span class="stats-label">Asistencia Promedio:</span>
                                        <span class="stats-value" style="color: <?php echo $porcentaje_asistencia >= 80 ? '#28a745' : ($porcentaje_asistencia >= 60 ? '#ffc107' : '#dc3545'); ?>;">
                                            <?php echo $porcentaje_asistencia; ?>%
                                        </span>
                                    </div>
                                    <div class="stats-row">
                                        <span class="stats-label">Presentes:</span>
                                        <span class="stats-value" style="color: #28a745;"><?php echo $stats['total_presentes']; ?></span>
                                    </div>
                                    <div class="stats-row">
                                        <span class="stats-label">Ausentes:</span>
                                        <span class="stats-value" style="color: #dc3545;"><?php echo $stats['total_ausentes']; ?></span>
                                    </div>
                                </div>
                                
                                <div class="course-actions">
                                    <a href="registrar_asistencia.php?id_curso=<?php echo $curso['id_curso']; ?>" class="btn-primary">
                                        <span class="material-icons-sharp">how_to_reg</span>
                                        Registrar Asistencia
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php 
                        endwhile; 
                    else:
                    ?>
                        <div class="no-courses">
                            <span class="material-icons-sharp icon">school</span>
                            <h3>No tienes cursos asignados</h3>
                            <p>Actualmente no tienes cursos asignados para gestionar asistencias.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </section>
        </div>
    </div>

    <script>
        function aplicarFiltros() {
            const filtroCurso = document.getElementById('filtro_curso').value;
            const filtroNivel = document.getElementById('filtro_nivel').value;
            const filtroEstudiantes = document.getElementById('filtro_estudiantes').value;
            
            const cards = document.querySelectorAll('.course-card');
            
            cards.forEach(card => {
                let mostrar = true;
                
                // Filtro por curso
                if (filtroCurso && card.dataset.curso !== filtroCurso) {
                    mostrar = false;
                }
                
                // Filtro por nivel
                if (filtroNivel && card.dataset.nivel !== filtroNivel) {
                    mostrar = false;
                }
                
                // Filtro por cantidad de estudiantes
                if (filtroEstudiantes) {
                    const estudiantes = parseInt(card.dataset.estudiantes);
                    switch (filtroEstudiantes) {
                        case '1-10':
                            if (estudiantes < 1 || estudiantes > 10) mostrar = false;
                            break;
                        case '11-20':
                            if (estudiantes < 11 || estudiantes > 20) mostrar = false;
                            break;
                        case '21+':
                            if (estudiantes < 21) mostrar = false;
                            break;
                    }
                }
                
                // Mostrar/ocultar tarjeta
                if (mostrar) {
                    card.style.display = 'block';
                    card.style.animation = 'fadeIn 0.5s ease-in-out';
                } else {
                    card.style.display = 'none';
                }
            });
            
            // Actualizar contador de resultados
            const visibleCards = document.querySelectorAll('.course-card[style*="block"], .course-card:not([style*="none"])');
            console.log(`Mostrando ${visibleCards.length} cursos de ${cards.length} totales`);
        }
        
        // Aplicar filtros automáticamente cuando cambien los valores
        document.getElementById('filtro_curso').addEventListener('change', aplicarFiltros);
        document.getElementById('filtro_nivel').addEventListener('change', aplicarFiltros);
        document.getElementById('filtro_estudiantes').addEventListener('change', aplicarFiltros);
        
        // Animación CSS para las tarjetas
        const style = document.createElement('style');
        style.textContent = `
            @keyframes fadeIn {
                from { opacity: 0; transform: translateY(20px); }
                to { opacity: 1; transform: translateY(0); }
            }
            
            .course-card {
                animation: fadeIn 0.6s ease-out;
            }
        `;
        document.head.appendChild(style);
        
        // Inicialización básica
        document.addEventListener('DOMContentLoaded', function() {
            // Sin efectos de hover
        });
    </script>
</body>
</html>
