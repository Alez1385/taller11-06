<?php
require_once '../../scripts/auth.php';
require_once '../../scripts/conexion.php';
require_once '../../scripts/config.php';
requireLogin();
checkPermission('profesor');

$id_curso = $_GET['id_curso'] ?? null;
$fecha = $_GET['fecha'] ?? $_POST['fecha'] ?? date('Y-m-d');

if (!$id_curso) {
    die("ID de curso no proporcionado");
}

// Obtener información del curso y sus horarios
$sql_curso = "SELECT c.nombre_curso, c.descripcion, c.nivel_educativo,
              h.lunes, h.martes, h.miercoles, h.jueves, h.viernes, h.sabado
              FROM cursos c
              LEFT JOIN horarios h ON c.id_curso = h.id_curso
              WHERE c.id_curso = ?
              ORDER BY h.id_horario DESC
              LIMIT 1";
$stmt_curso = $conn->prepare($sql_curso);
$stmt_curso->bind_param("i", $id_curso);
$stmt_curso->execute();
$result_curso = $stmt_curso->get_result();
$curso = $result_curso->fetch_assoc();

// Construir string de horarios para mostrar
$horarios_display = [];
if ($curso) {
    $dias_mapping = [
        'lunes' => 'Lunes',
        'martes' => 'Martes', 
        'miercoles' => 'Miércoles',
        'jueves' => 'Jueves',
        'viernes' => 'Viernes',
        'sabado' => 'Sábado'
    ];
    
    foreach ($dias_mapping as $columna => $dia_nombre) {
        if (!empty($curso[$columna])) {
            $horarios_display[] = $dia_nombre . ' ' . $curso[$columna];
        }
    }
}
$curso['horarios'] = implode(', ', $horarios_display);

// Obtener los días de la semana en los que se imparte el curso
$sql_dias = "SELECT lunes, martes, miercoles, jueves, viernes, sabado 
             FROM horarios 
             WHERE id_curso = ? 
             ORDER BY id_horario DESC 
             LIMIT 1";
$stmt_dias = $conn->prepare($sql_dias);
$stmt_dias->bind_param("i", $id_curso);
$stmt_dias->execute();
$result_dias = $stmt_dias->get_result();
$horario_row = $result_dias->fetch_assoc();

$dias_curso = [];
if ($horario_row) {
    $dias_mapping = [
        'lunes' => 'Lunes',
        'martes' => 'Martes', 
        'miercoles' => 'Miércoles',
        'jueves' => 'Jueves',
        'viernes' => 'Viernes',
        'sabado' => 'Sábado'
    ];
    
    foreach ($dias_mapping as $columna => $dia_nombre) {
        if (!empty($horario_row[$columna])) {
            $dias_curso[] = $dia_nombre;
        }
    }
}

$dias_curso_normalizados = $dias_curso; // Ya están normalizados

// Obtener estudiantes del curso (solo los que tienen inscripción activa)
$sql_estudiantes = "SELECT DISTINCT e.id_estudiante, u.nombre, u.apellido, u.id_usuario
                    FROM estudiante e
                    INNER JOIN usuario u ON e.id_usuario = u.id_usuario
                    INNER JOIN inscripciones i ON e.id_estudiante = i.id_estudiante
                    WHERE i.id_curso = ? 
                    AND i.estado = 'aprobada'
                    ORDER BY u.apellido, u.nombre";
$stmt_estudiantes = $conn->prepare($sql_estudiantes);
if ($stmt_estudiantes === false) {
    die("Error preparando consulta de estudiantes: " . $conn->error);
}
$stmt_estudiantes->bind_param("i", $id_curso);
$stmt_estudiantes->execute();
$result_estudiantes = $stmt_estudiantes->get_result();

// Debug: Verificar cuántos estudiantes se encontraron
$num_estudiantes = $result_estudiantes->num_rows;
if ($num_estudiantes == 0) {
    // Intentar consulta alternativa sin filtro de estado
    $sql_estudiantes_alt = "SELECT DISTINCT e.id_estudiante, u.nombre, u.apellido, u.id_usuario, i.estado
                           FROM estudiante e
                           INNER JOIN usuario u ON e.id_usuario = u.id_usuario
                           INNER JOIN inscripciones i ON e.id_estudiante = i.id_estudiante
                           WHERE i.id_curso = ?
                           ORDER BY u.apellido, u.nombre";
    $stmt_alt = $conn->prepare($sql_estudiantes_alt);
    if ($stmt_alt) {
        $stmt_alt->bind_param("i", $id_curso);
        $stmt_alt->execute();
        $result_estudiantes = $stmt_alt->get_result();
        $num_estudiantes = $result_estudiantes->num_rows;
    }
}

// Procesar el formulario de asistencia
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $asistencias = $_POST['asistencia'] ?? [];
    
    // Obtener nuevamente la lista de estudiantes para el procesamiento
    $result_estudiantes->data_seek(0); // Reiniciar el puntero del resultado
    
    $conn->begin_transaction();
    try {
    foreach ($result_estudiantes as $estudiante) {
        $id_estudiante = $estudiante['id_estudiante'];
        $estado = $asistencias[$id_estudiante] ?? 'ausente';
            
            // Verificar si ya existe un registro para este estudiante, curso y fecha
            $sql_check = "SELECT id_asistencia FROM asistencia WHERE id_estudiante = ? AND id_curso = ? AND fecha = ?";
            $stmt_check = $conn->prepare($sql_check);
            if ($stmt_check === false) {
                throw new Exception("Error preparando consulta de verificación: " . $conn->error);
            }
            $stmt_check->bind_param("iis", $id_estudiante, $id_curso, $fecha);
            $stmt_check->execute();
            $result_check = $stmt_check->get_result();
            
            if ($result_check->num_rows > 0) {
                // Actualizar registro existente
                $sql_update = "UPDATE asistencia SET estado = ? WHERE id_estudiante = ? AND id_curso = ? AND fecha = ?";
                $stmt_update = $conn->prepare($sql_update);
                if ($stmt_update === false) {
                    throw new Exception("Error preparando consulta de actualización: " . $conn->error);
                }
                $stmt_update->bind_param("siis", $estado, $id_estudiante, $id_curso, $fecha);
                $stmt_update->execute();
            } else {
                // Insertar nuevo registro
                $sql_insert = "INSERT INTO asistencia (id_estudiante, id_curso, fecha, estado) VALUES (?, ?, ?, ?)";
                $stmt_insert = $conn->prepare($sql_insert);
                if ($stmt_insert === false) {
                    throw new Exception("Error preparando consulta de inserción: " . $conn->error);
                }
                $stmt_insert->bind_param("iiss", $id_estudiante, $id_curso, $fecha, $estado);
                $stmt_insert->execute();
            }
        }
        
        $conn->commit();
    $_SESSION['mensaje'] = "Asistencia registrada exitosamente.";
        header("Location: registrar_asistencia.php?id_curso=$id_curso&fecha=$fecha");
        exit();
        
    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['error'] = "Error al registrar la asistencia: " . $e->getMessage();
    header("Location: registrar_asistencia.php?id_curso=$id_curso");
    exit();
    }
}

// Modificar la consulta SQL para obtener asistencias previas
$sql_asistencias = "SELECT id_estudiante, estado FROM asistencia WHERE id_curso = ? AND fecha = ?";
$stmt_asistencias = $conn->prepare($sql_asistencias);
$stmt_asistencias->bind_param("is", $id_curso, $fecha);
$stmt_asistencias->execute();
$result_asistencias = $stmt_asistencias->get_result();
$asistencias_previas = [];
while ($row = $result_asistencias->fetch_assoc()) {
    $asistencias_previas[$row['id_estudiante']] = $row['estado'];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Asistencia - <?php echo htmlspecialchars($curso['nombre_curso']); ?></title>
    <link rel="stylesheet" href="../cursos/cursos.css">
    <link rel="stylesheet" href="css/asistencia.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons+Sharp">
    <style>
        .content {
            background-color: #fff;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            border: 1px solid #e5e7eb;
        }
        
        .course-info {
            background: #00bcff;
            color: white;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 25px;
        }
        
        .course-info h2 {
            margin: 0 0 10px 0;
            font-size: 1.5rem;
        }
        
        .course-info p {
            margin: 5px 0;
            opacity: 0.9;
        }
        
        .form-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 25px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #00bcff;
            font-size: 1rem;
        }
        
        .form-group input[type="date"] {
            width: 100%;
            padding: 12px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }
        
        .form-group input[type="date"]:focus {
            outline: none;
            border-color: #00bcff;
        }
        
        .date-info {
            background: #e3f2fd;
            padding: 10px;
            border-radius: 6px;
            margin-top: 8px;
            font-size: 0.9rem;
            color: #00bcff;
        }
        
        .estudiantes-container {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .estudiantes-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #e5e7eb;
        }
        
        .estudiantes-header h3 {
            margin: 0;
            color: #00bcff;
            font-size: 1.3rem;
        }
        
        .attendance-summary {
            display: flex;
            gap: 15px;
            font-size: 0.9rem;
        }
        
        .summary-item {
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .summary-presente { color: #28a745; }
        .summary-ausente { color: #dc3545; }
        .summary-retardo { color: #ffc107; }
        
        .estudiantes-list {
            max-height: 500px;
            overflow-y: auto;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
        }
        
        .estudiante-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            border-bottom: 1px solid #f0f0f0;
            transition: background-color 0.3s ease;
        }
        
        .estudiante-item:last-child {
            border-bottom: none;
        }
        
        .estudiante-item:hover {
            background-color: #f8f9fa;
        }
        
        .estudiante-info {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-grow: 1;
        }
        
        .estudiante-avatar {
            width: 40px;
            height: 40px;
            background: #00bcff;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 1.1rem;
        }
        
        .estudiante-nombre {
            font-weight: 600;
            color: #333;
            font-size: 1rem;
        }
        
        .asistencia-options {
            display: flex;
            gap: 8px;
        }
        
        .asistencia-option {
            position: relative;
        }
        
        .asistencia-option input[type="radio"] {
            display: none;
        }
        
        .asistencia-option label {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 8px 12px;
            border: 2px solid #e5e7eb;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 0.9rem;
            font-weight: 500;
            background: white;
        }
        
        .asistencia-option input[type="radio"]:checked + label {
            border-color: #00bcff;
            background: #00bcff;
            color: white;
        }
        
        .asistencia-option.presente input[type="radio"]:checked + label {
            border-color: #28a745;
            background: #28a745;
        }
        
        .asistencia-option.ausente input[type="radio"]:checked + label {
            border-color: #dc3545;
            background: #dc3545;
        }
        
        .asistencia-option.retardo input[type="radio"]:checked + label {
            border-color: #ffc107;
            background: #ffc107;
            color: #333;
        }
        
        .btn-submit {
            background: #28a745;
            color: white;
            padding: 15px 30px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1.1rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
            margin-top: 20px;
            transition: background-color 0.3s ease;
        }
        
        .btn-submit:hover {
            background: #218838;
        }
        
        .btn-submit:disabled {
            background: #f8f9fa;
            cursor: not-allowed;
        }
        
        .mensaje {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 8px;
            font-weight: 500;
        }
        
        .exito {
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            color: #ffffff;
        }
        
        .error {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #ffffff;
        }
        
        .no-classes {
            text-align: center;
            padding: 40px;
            color: #f8f9fa;
            background: #f8f9fa;
            border-radius: 8px;
            margin: 20px 0;
        }
        
        .no-classes .material-icons-sharp {
            font-size: 3rem;
            margin-bottom: 15px;
            opacity: 0.5;
        }
        
        @media (max-width: 768px) {
            .estudiante-item {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
            
            .asistencia-options {
                width: 100%;
                justify-content: space-between;
            }
            
            .asistencia-option label {
                flex: 1;
                justify-content: center;
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
                    <h1>Registrar Asistencia - <?php echo htmlspecialchars($curso['nombre_curso']); ?></h1>
                </div>
                <div class="header-right">
                    <a href="asistencia.php" class="btn-back">
                        <span class="material-icons-sharp">arrow_back</span>
                        Volver
                    </a>
                </div>
            </header>

            <section class="content">
                <?php if (isset($_SESSION['mensaje'])): ?>
                    <div class="mensaje exito"><?php echo $_SESSION['mensaje']; unset($_SESSION['mensaje']); ?></div>
                <?php endif; ?>

                <?php if (isset($_SESSION['error'])): ?>
                    <div class="mensaje error"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
                <?php endif; ?>
                

                <!-- Información del Curso -->
                <div class="course-info">
                    <h2><?php echo htmlspecialchars($curso['nombre_curso']); ?></h2>
                    <?php if ($curso['descripcion']): ?>
                        <p><strong>Descripción:</strong> <?php echo htmlspecialchars($curso['descripcion']); ?></p>
                    <?php endif; ?>
                    <?php if ($curso['nivel_educativo']): ?>
                        <p><strong>Nivel:</strong> <?php echo htmlspecialchars($curso['nivel_educativo']); ?></p>
                    <?php endif; ?>
                    <?php if ($curso['horarios']): ?>
                        <p><strong>Horarios:</strong> <?php echo htmlspecialchars($curso['horarios']); ?></p>
                    <?php endif; ?>
                </div>

                <?php if (empty($dias_curso)): ?>
                    <div class="no-classes">
                        <span class="material-icons-sharp">event_busy</span>
                        <h3>Sin Horarios Asignados</h3>
                        <p>Este curso no tiene horarios asignados. Contacta al administrador para asignar horarios antes de registrar asistencia.</p>
                    </div>
                <?php else: ?>
                    <form method="POST" id="attendanceForm">
                        <!-- Selección de Fecha -->
                        <div class="form-section">
                            <div class="form-group">
                                <label for="fecha">Seleccionar Fecha de Clase:</label>
                                <input type="date" id="fecha" name="fecha" value="<?php echo $fecha; ?>" required>
                                <div class="date-info">
                                    <span class="material-icons-sharp" style="font-size: 1rem; vertical-align: middle;">info</span>
                                    <strong>Días de clase:</strong> <?php echo implode(', ', $dias_curso_normalizados); ?>
                                </div>
                            </div>
                        </div>

                        <!-- Lista de Estudiantes -->
                        <div class="estudiantes-container">
                            <div class="estudiantes-header">
                                <h3>Registro de Asistencia</h3>
                                <div class="attendance-summary">
                                    <div class="summary-item">
                                        <span class="material-icons-sharp summary-presente">check_circle</span>
                                        <span id="count-presente">0</span> Presentes
                                    </div>
                                    <div class="summary-item">
                                        <span class="material-icons-sharp summary-ausente">cancel</span>
                                        <span id="count-ausente">0</span> Ausentes
                                    </div>
                                    <div class="summary-item">
                                        <span class="material-icons-sharp summary-retardo">schedule</span>
                                        <span id="count-retardo">0</span> Retardos
                                    </div>
                                </div>
                    </div>

                            <div class="estudiantes-list">
                                <?php 
                                $result_estudiantes->data_seek(0); // Reiniciar el puntero
                                $estudiante_count = 0;
                                while ($estudiante = $result_estudiantes->fetch_assoc()): 
                                    $estudiante_count++;
                                    $nombre = $estudiante['nombre'] ?? 'Sin nombre';
                                    $apellido = $estudiante['apellido'] ?? 'Sin apellido';
                                    $iniciales = strtoupper(substr($nombre, 0, 1) . substr($apellido, 0, 1));
                                    
                                ?>
                            <div class="estudiante-item">
                                        <div class="estudiante-info">
                                            <div class="estudiante-avatar"><?php echo $iniciales; ?></div>
                                            <div class="estudiante-nombre">
                                                <?php echo htmlspecialchars($apellido . ', ' . $nombre); ?>
                                            </div>
                                        </div>
                                <div class="asistencia-options">
                                            <div class="asistencia-option presente">
                                        <input type="radio" 
                                               name="asistencia[<?php echo $estudiante['id_estudiante']; ?>]" 
                                               value="presente"
                                                       id="presente_<?php echo $estudiante['id_estudiante']; ?>"
                                               <?php echo (isset($asistencias_previas[$estudiante['id_estudiante']]) && $asistencias_previas[$estudiante['id_estudiante']] == 'presente') ? 'checked' : ''; ?>>
                                                <label for="presente_<?php echo $estudiante['id_estudiante']; ?>">
                                                    <span class="material-icons-sharp">check_circle</span>
                                        Presente
                                    </label>
                                            </div>
                                            <div class="asistencia-option ausente">
                                        <input type="radio" 
                                               name="asistencia[<?php echo $estudiante['id_estudiante']; ?>]" 
                                               value="ausente"
                                                       id="ausente_<?php echo $estudiante['id_estudiante']; ?>"
                                               <?php echo (isset($asistencias_previas[$estudiante['id_estudiante']]) && $asistencias_previas[$estudiante['id_estudiante']] == 'ausente') ? 'checked' : ''; ?>>
                                                <label for="ausente_<?php echo $estudiante['id_estudiante']; ?>">
                                                    <span class="material-icons-sharp">cancel</span>
                                        Ausente
                                    </label>
                                            </div>
                                            <div class="asistencia-option retardo">
                                        <input type="radio" 
                                               name="asistencia[<?php echo $estudiante['id_estudiante']; ?>]" 
                                               value="retardo"
                                                       id="retardo_<?php echo $estudiante['id_estudiante']; ?>"
                                               <?php echo (isset($asistencias_previas[$estudiante['id_estudiante']]) && $asistencias_previas[$estudiante['id_estudiante']] == 'retardo') ? 'checked' : ''; ?>>
                                                <label for="retardo_<?php echo $estudiante['id_estudiante']; ?>">
                                                    <span class="material-icons-sharp">schedule</span>
                                        Retardo
                                    </label>
                                            </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>

                            <button type="submit" class="btn-submit" id="submitBtn">
                                <span class="material-icons-sharp">save</span>
                                Guardar Asistencia
                            </button>
                        </div>
                </form>
                <?php endif; ?>
            </section>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Función para actualizar el conteo de asistencias
            function updateAttendanceCount() {
                const presentes = document.querySelectorAll('input[value="presente"]:checked').length;
                const ausentes = document.querySelectorAll('input[value="ausente"]:checked').length;
                const retardos = document.querySelectorAll('input[value="retardo"]:checked').length;
                
                document.getElementById('count-presente').textContent = presentes;
                document.getElementById('count-ausente').textContent = ausentes;
                document.getElementById('count-retardo').textContent = retardos;
                
                // Habilitar/deshabilitar botón de envío
                const submitBtn = document.getElementById('submitBtn');
                const totalStudents = document.querySelectorAll('.estudiante-item').length;
                const totalMarked = presentes + ausentes + retardos;
                
                if (totalMarked === totalStudents && totalStudents > 0) {
                    submitBtn.disabled = false;
                    submitBtn.style.opacity = '1';
                } else {
                    submitBtn.disabled = true;
                    submitBtn.style.opacity = '0.6';
                }
            }
            
            // Función para limpiar todos los estados de asistencia
            function clearAllAttendance() {
                document.querySelectorAll('input[type="radio"]').forEach(function(radio) {
                    radio.checked = false;
                });
                updateAttendanceCount();
            }
            
            // Agregar event listeners a todos los radio buttons
            document.querySelectorAll('input[type="radio"]').forEach(function(radio) {
                radio.addEventListener('change', updateAttendanceCount);
            });
            
            // Manejo de cambio de fecha (sin validaciones complejas)
            const fechaInput = document.getElementById('fecha');
            
            fechaInput.addEventListener('change', function() {
                // Limpiar todos los estados de asistencia al cambiar fecha
                clearAllAttendance();
                
                // Recargar la página con la nueva fecha para obtener asistencias previas
                const url = new URL(window.location);
                url.searchParams.set('fecha', this.value);
                window.location.href = url.toString();
            });
            
            // Validación del formulario
            document.getElementById('attendanceForm').addEventListener('submit', function(e) {
                const totalStudents = document.querySelectorAll('.estudiante-item').length;
                const totalMarked = document.querySelectorAll('input[type="radio"]:checked').length;
                
                if (totalMarked < totalStudents) {
                    e.preventDefault();
                    alert('⚠️ Por favor, marca la asistencia de todos los estudiantes antes de guardar.');
                    return false;
                }
                
                // Mostrar loading en el botón
                const submitBtn = document.getElementById('submitBtn');
                submitBtn.innerHTML = '<span class="material-icons-sharp">hourglass_empty</span> Guardando...';
                submitBtn.disabled = true;
            });
            
            // Inicializar conteo
            updateAttendanceCount();
            
            // Auto-scroll a la lista de estudiantes si hay muchos
            const estudiantesList = document.querySelector('.estudiantes-list');
            if (estudiantesList && estudiantesList.scrollHeight > 400) {
                estudiantesList.scrollTop = 0;
            }
            
            // Función para marcar todos como presente (atajo)
            document.addEventListener('keydown', function(e) {
                if (e.ctrlKey && e.key === 'p') {
                    e.preventDefault();
                    document.querySelectorAll('input[value="presente"]').forEach(function(radio) {
                        radio.checked = true;
                    });
                    updateAttendanceCount();
                }
            });
        });
    </script>
</body>
</html>
