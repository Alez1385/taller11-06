<?php
require_once '../../scripts/auth.php';
require_once '../../scripts/conexion.php';
require_once '../../scripts/config.php';
requireLogin();
checkPermission('estudiante');

// Obtener el id_estudiante basado en el id_usuario de la sesión
$id_usuario = $_SESSION['id_usuario'];
$sql_estudiante = "SELECT id_estudiante FROM estudiante WHERE id_usuario = ?";
$stmt_estudiante = $conn->prepare($sql_estudiante);
if ($stmt_estudiante === false) {
    error_log("Error preparando consulta de estudiante: " . $conn->error);
    die("Error en la consulta de estudiante: " . $conn->error);
}

$stmt_estudiante->bind_param("i", $id_usuario);
if (!$stmt_estudiante->execute()) {
    error_log("Error ejecutando consulta de estudiante: " . $stmt_estudiante->error);
    die("Error ejecutando consulta de estudiante: " . $stmt_estudiante->error);
}

$result_estudiante = $stmt_estudiante->get_result();
$estudiante = $result_estudiante->fetch_assoc();

if (!$estudiante) {
    die("No se encontró el estudiante para el usuario ID: " . $id_usuario);
}

$id_estudiante = $estudiante['id_estudiante'];

// Consulta SQL simplificada para obtener cursos del estudiante
$sql_cursos = "SELECT c.id_curso, c.nombre_curso, c.descripcion, c.icono, c.nivel_educativo, c.duracion, c.estado,
               cc.nombre_categoria
               FROM cursos c
               INNER JOIN inscripciones i ON c.id_curso = i.id_curso
               LEFT JOIN categoria_curso cc ON c.id_categoria = cc.id_categoria
               WHERE i.id_estudiante = ? AND i.estado = 'aprobada'
               ORDER BY c.nombre_curso";

$stmt_cursos = $conn->prepare($sql_cursos);
if ($stmt_cursos === false) {
    error_log("Error preparando consulta de cursos: " . $conn->error);
    die("Error en la consulta de cursos: " . $conn->error);
}

$stmt_cursos->bind_param("i", $id_estudiante);
if (!$stmt_cursos->execute()) {
    error_log("Error ejecutando consulta de cursos: " . $stmt_cursos->error);
    die("Error ejecutando consulta de cursos: " . $stmt_cursos->error);
}

$result_cursos = $stmt_cursos->get_result();

// Debug: Verificar datos
error_log("ID Estudiante: " . $id_estudiante);
error_log("Número de cursos encontrados: " . $result_cursos->num_rows);


?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Asistencias</title>
    <link rel="stylesheet" href="../cursos/cursos.css">
    <link rel="stylesheet" href="css/asistencia.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons+Sharp">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .stats-overview {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: #00bcff;
            color: white;
            padding: 15px;
            border-radius: 10px;
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
        
        .attendance-stats {
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
        
        .attendance-chart {
            height: 150px;
            margin-bottom: 15px;
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
        
        .btn-secondary {
            background: #f8f9fa;
            color: white;
            border: none;
            padding: 10px 16px;
            border-radius: 8px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-weight: 600;
            font-size: 0.9rem;
        }
        
        .btn-secondary:hover {
            background:rgb(203, 207, 210);
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
        
        .attendance-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .badge-excellent {
            background: #d4edda;
            color: #ffffff;
        }
        
        .badge-good {
            background: #fff3cd;
            color: #ffffff;
        }
        
        .badge-poor {
            background: #f8d7da;
            color: #ffffff;
        }
        
        @media (max-width: 768px) {
            .stats-overview {
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
                    <h1>Mis Asistencias</h1>
                </div>
            </header>

            <section class="content">
                <!-- Estadísticas Generales del Estudiante -->
                <div class="stats-overview">
                    <?php
                    $total_cursos = $result_cursos->num_rows;
                    $total_clases = 0;
                    $total_asistencias = 0;
                    $total_inasistencias = 0;
                    $total_retardos = 0;
                    
                    // Calcular estadísticas generales
                    $result_cursos->data_seek(0);
                    while ($curso = $result_cursos->fetch_assoc()) {
                        // Obtener estadísticas de asistencia para este curso específico
                        $sql_asistencia = "SELECT 
                            COUNT(DISTINCT fecha) as total_clases,
                            SUM(CASE WHEN estado = 'presente' THEN 1 ELSE 0 END) as asistencias,
                            SUM(CASE WHEN estado = 'ausente' THEN 1 ELSE 0 END) as inasistencias,
                            SUM(CASE WHEN estado = 'retardo' THEN 1 ELSE 0 END) as retardos
                            FROM asistencia 
                            WHERE id_curso = ? AND id_estudiante = ?";
                        
                        $stmt_asistencia = $conn->prepare($sql_asistencia);
                        if ($stmt_asistencia) {
                            $stmt_asistencia->bind_param("ii", $curso['id_curso'], $id_estudiante);
                            $stmt_asistencia->execute();
                            $result_asistencia = $stmt_asistencia->get_result();
                            $asistencia_data = $result_asistencia->fetch_assoc();
                            
                            if ($asistencia_data) {
                                $curso['total_clases'] = $asistencia_data['total_clases'] ?? 0;
                                $curso['asistencias'] = $asistencia_data['asistencias'] ?? 0;
                                $curso['inasistencias'] = $asistencia_data['inasistencias'] ?? 0;
                                $curso['retardos'] = $asistencia_data['retardos'] ?? 0;
                            } else {
                                $curso['total_clases'] = 0;
                                $curso['asistencias'] = 0;
                                $curso['inasistencias'] = 0;
                                $curso['retardos'] = 0;
                            }
                            $stmt_asistencia->close();
                        } else {
                            $curso['total_clases'] = 0;
                            $curso['asistencias'] = 0;
                            $curso['inasistencias'] = 0;
                            $curso['retardos'] = 0;
                        }
                        
                        error_log("Curso: " . $curso['nombre_curso'] . " - Total clases: " . $curso['total_clases'] . " - Asistencias: " . $curso['asistencias'] . " - Inasistencias: " . $curso['inasistencias']);
                        $total_clases += $curso['total_clases'];
                        $total_asistencias += $curso['asistencias'];
                        $total_inasistencias += $curso['inasistencias'];
                    }
                    
                    $porcentaje_general = $total_clases > 0 ? round(($total_asistencias / $total_clases) * 100, 1) : 0;
                    ?>
                    
                    <div class="stat-card">
                        <h3>Total de Cursos</h3>
                        <p class="number asistencia_color_text"><?php echo $total_cursos; ?></p>
                        <span class="material-icons-sharp icon">school</span>
                    </div>
                    
                    <div class="stat-card">
                        <h3>Total de Clases</h3>
                        <p class="number asistencia_color_text"><?php echo $total_clases; ?></p>
                        <span class="material-icons-sharp icon">event</span>
                    </div>
                    
                    <div class="stat-card">
                        <h3>Asistencias</h3>
                        <p class="number asistencia_color_text"><?php echo $total_asistencias; ?></p>
                        <span class="material-icons-sharp icon">check_circle</span>
                    </div>
                    
                    <div class="stat-card">
                        <h3>Asistencia General</h3>
                        <p class="number asistencia_color_text"><?php echo $porcentaje_general; ?>%</p>
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
                            <label for="filtro_asistencia">Nivel de Asistencia:</label>
                            <select id="filtro_asistencia">
                                <option value="">Todos los niveles</option>
                                <option value="excelente">Excelente (90%+)</option>
                                <option value="bueno">Bueno (70-89%)</option>
                                <option value="regular">Regular (50-69%)</option>
                                <option value="bajo">Bajo (<50%)</option>
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
                            // Obtener estadísticas de asistencia para este curso específico
                            $sql_asistencia_curso = "SELECT 
                                COUNT(DISTINCT fecha) as total_clases,
                                SUM(CASE WHEN estado = 'presente' THEN 1 ELSE 0 END) as asistencias,
                                SUM(CASE WHEN estado = 'ausente' THEN 1 ELSE 0 END) as inasistencias,
                                SUM(CASE WHEN estado = 'retardo' THEN 1 ELSE 0 END) as retardos
                                FROM asistencia 
                                WHERE id_curso = ? AND id_estudiante = ?";
                            
                            $stmt_asistencia_curso = $conn->prepare($sql_asistencia_curso);
                            if ($stmt_asistencia_curso) {
                                $stmt_asistencia_curso->bind_param("ii", $curso['id_curso'], $id_estudiante);
                                $stmt_asistencia_curso->execute();
                                $result_asistencia_curso = $stmt_asistencia_curso->get_result();
                                $asistencia_curso_data = $result_asistencia_curso->fetch_assoc();
                                
                                if ($asistencia_curso_data) {
                                    $curso['total_clases'] = $asistencia_curso_data['total_clases'] ?? 0;
                                    $curso['asistencias'] = $asistencia_curso_data['asistencias'] ?? 0;
                                    $curso['inasistencias'] = $asistencia_curso_data['inasistencias'] ?? 0;
                                    $curso['retardos'] = $asistencia_curso_data['retardos'] ?? 0;
                                } else {
                                    $curso['total_clases'] = 0;
                                    $curso['asistencias'] = 0;
                                    $curso['inasistencias'] = 0;
                                    $curso['retardos'] = 0;
                                }
                                $stmt_asistencia_curso->close();
                            } else {
                                $curso['total_clases'] = 0;
                                $curso['asistencias'] = 0;
                                $curso['inasistencias'] = 0;
                                $curso['retardos'] = 0;
                            }
                            
                            $porcentaje_curso = $curso['total_clases'] > 0 ? 
                                round(($curso['asistencias'] / $curso['total_clases']) * 100, 1) : 0;
                            
                            // Determinar el nivel de asistencia
                            $nivel_asistencia = '';
                            $badge_class = '';
                            if ($porcentaje_curso >= 90) {
                                $nivel_asistencia = 'Excelente';
                                $badge_class = 'badge-excellent';
                            } elseif ($porcentaje_curso >= 70) {
                                $nivel_asistencia = 'Bueno';
                                $badge_class = 'badge-good';
                            } else {
                                $nivel_asistencia = 'Necesita Mejorar';
                                $badge_class = 'badge-poor';
                            }
                    ?>
                        <div class="course-card" data-curso="<?php echo $curso['id_curso']; ?>" 
                             data-nivel="<?php echo strtolower($curso['nivel_educativo']); ?>"
                             data-porcentaje="<?php echo $porcentaje_curso; ?>">
                            <div class="course-header">
                                <h3 class="course-title"><?php echo htmlspecialchars($curso['nombre_curso']); ?></h3>
                                <p class="course-subtitle"><?php echo htmlspecialchars($curso['descripcion']); ?></p>
                                <span class="attendance-badge <?php echo $badge_class; ?>">
                                    <?php echo $nivel_asistencia; ?>
                                </span>
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
                                        <span class="material-icons-sharp icon">event</span>
                                        <span class="text"><?php echo $curso['total_clases']; ?> clases</span>
                                    </div>
                                    <div class="info-item">
                                        <span class="material-icons-sharp icon">assessment</span>
                                        <span class="text"><?php echo $porcentaje_curso; ?>% asistencia</span>
                                    </div>
                                </div>
                                
                                <div class="attendance-stats">
                                    <div class="stats-row">
                                        <span class="stats-label">Total de Clases:</span>
                                        <span class="stats-value"><?php echo $curso['total_clases']; ?></span>
                                    </div>
                                    <div class="stats-row">
                                        <span class="stats-label">Asistencias:</span>
                                        <span class="stats-value" style="color: #28a745;"><?php echo $curso['asistencias']; ?></span>
                                    </div>
                                    <div class="stats-row">
                                        <span class="stats-label">Inasistencias:</span>
                                        <span class="stats-value" style="color: #dc3545;"><?php echo $curso['inasistencias']; ?></span>
                                    </div>
                                    <div class="stats-row">
                                        <span class="stats-label">Porcentaje:</span>
                                        <span class="stats-value" style="color: <?php echo $porcentaje_curso >= 80 ? '#28a745' : ($porcentaje_curso >= 60 ? '#ffc107' : '#dc3545'); ?>; font-weight: bold;">
                                            <?php echo $porcentaje_curso; ?>%
                                        </span>
                                    </div>
                                </div>
                                
                                <!-- Gráfico de Asistencia -->
                                <div class="attendance-chart">
                                    <canvas id="chart_<?php echo $curso['id_curso']; ?>"></canvas>
                                </div>
                                
                                <div class="course-actions">
                                    <a href="detalle_asistencia.php?id_curso=<?php echo $curso['id_curso']; ?>" class="btn-primary">
                                        <span class="material-icons-sharp">visibility</span>
                                        Ver Detalles
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
                            <h3>No estás inscrito en ningún curso</h3>
                            <p>Actualmente no tienes cursos inscritos para ver asistencias.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </section>
        </div>
    </div>

    <script>
        // Función para aplicar filtros
        function aplicarFiltros() {
            const filtroCurso = document.getElementById('filtro_curso').value;
            const filtroNivel = document.getElementById('filtro_nivel').value;
            const filtroAsistencia = document.getElementById('filtro_asistencia').value;
            
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
                
                // Filtro por nivel de asistencia
                if (filtroAsistencia) {
                    const porcentaje = parseFloat(card.dataset.porcentaje);
                    switch (filtroAsistencia) {
                        case 'excelente':
                            if (porcentaje < 90) mostrar = false;
                            break;
                        case 'bueno':
                            if (porcentaje < 70 || porcentaje >= 90) mostrar = false;
                            break;
                        case 'regular':
                            if (porcentaje < 50 || porcentaje >= 70) mostrar = false;
                            break;
                        case 'bajo':
                            if (porcentaje >= 50) mostrar = false;
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
        document.getElementById('filtro_asistencia').addEventListener('change', aplicarFiltros);
        
        // Crear gráficos de asistencia
        function crearGraficos() {
            const charts = document.querySelectorAll('[id^="chart_"]');
            
            charts.forEach(canvas => {
                const cursoId = canvas.id.replace('chart_', '');
                const card = canvas.closest('.course-card');
                const stats = card.querySelector('.attendance-stats');
                
                const totalClases = parseInt(stats.querySelector('.stats-row:nth-child(1) .stats-value').textContent);
                const asistencias = parseInt(stats.querySelector('.stats-row:nth-child(2) .stats-value').textContent);
                const inasistencias = parseInt(stats.querySelector('.stats-row:nth-child(3) .stats-value').textContent);
                
                const ctx = canvas.getContext('2d');
                new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Asistencias', 'Inasistencias'],
                        datasets: [{
                            data: [asistencias, inasistencias],
                            backgroundColor: ['#28a745', '#dc3545'],
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
                                        size: 12
                                    }
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        const percentage = ((context.parsed / total) * 100).toFixed(1);
                                        return context.label + ': ' + context.parsed + ' (' + percentage + '%)';
                                    }
                                }
                            }
                        }
                    }
                });
            });
        }
        
        // Inicializar gráficos cuando se carga la página
        document.addEventListener('DOMContentLoaded', function() {
            crearGraficos();
        });
        
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
    </script>
</body>
</html>
