<?php
// Include necessary files and perform initial checks
require_once '../scripts/conexion.php';
require_once '../scripts/functions.php';

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Obtener el tipo de usuario SIEMPRE desde la base de datos
$user = getUserInfo($conn, $_SESSION['id_usuario']);
$_SESSION['id_tipo_usuario'] = $user['id_tipo_usuario'];
$_SESSION['user_role'] = $user['tipo_nombre'];

// Check if the user is logged in and is a professor
if (!isset($_SESSION['username']) || $_SESSION['user_role'] !== 'profesor') {
    error_log("Intento de acceso no autorizado al dashboard de profesor. Usuario: " . ($_SESSION['username'] ?? 'No definido') . ", Tipo de usuario: " . ($_SESSION['user_role'] ?? 'No definido'));
    echo '<h2 class="no-tienes-permiso">No tienes permiso para acceder a esta página.</h2>';
    exit;
}

// Obtener información básica del usuario
$id_usuario = $_SESSION['id_usuario'];
$sql_user = "SELECT * FROM usuario WHERE id_usuario = ?";
$stmt_user = $conn->prepare($sql_user);
$stmt_user->bind_param("i", $id_usuario);
$stmt_user->execute();
$result_user = $stmt_user->get_result();
$user = $result_user->fetch_assoc();

// Mostrar notificación de perfil incompleto universal
include '../scripts/profile_notification.php';

// Verificar si el usuario necesita completar sus datos
$datos_incompletos = false;
$campos_faltantes = [];
if (isset($user['perfil_incompleto']) && $user['perfil_incompleto'] == 1) {
    $datos_incompletos = true;
    if (empty($user['nombre'])) $campos_faltantes[] = 'nombre';
    if (empty($user['apellido'])) $campos_faltantes[] = 'apellido';
    if (empty($user['telefono'])) $campos_faltantes[] = 'teléfono';
    if (empty($user['direccion'])) $campos_faltantes[] = 'dirección';
    if (empty($user['fecha_nac'])) $campos_faltantes[] = 'fecha de nacimiento';
    if (empty($campos_faltantes)) $campos_faltantes[] = 'información personal';
}
?>
<style>
.profile-notification {
    background: linear-gradient(135deg, #ff6b6b 0%, #ee5a52 100%);
    color: white;
    padding: 20px;
    border-radius: 12px;
    margin-bottom: 25px;
    box-shadow: 0 8px 25px rgba(255, 107, 107, 0.3);
    border-left: 5px solid #ff4757;
    position: relative;
    overflow: hidden;
}
.profile-notification h3 {margin:0 0 10px 0;font-size:18px;font-weight:600;display:flex;align-items:center;gap:10px;}
.profile-notification p {margin:0 0 15px 0;font-size:14px;opacity:0.9;}
.profile-notification .missing-fields {background:rgba(255,255,255,0.2);padding:10px;border-radius:8px;margin:10px 0;font-size:13px;}
.profile-notification .btn-complete-profile {background:rgba(255,255,255,0.2);color:white;border:2px solid rgba(255,255,255,0.3);padding:10px 20px;border-radius:8px;text-decoration:none;font-weight:600;font-size:14px;transition:all 0.3s ease;display:inline-flex;align-items:center;gap:8px;}
.profile-notification .btn-complete-profile:hover {background:rgba(255,255,255,0.3);transform:translateY(-2px);box-shadow:0 5px 15px rgba(0,0,0,0.2);}
.profile-notification .close-notification {position:absolute;top:15px;right:15px;background:none;border:none;color:white;font-size:20px;cursor:pointer;opacity:0.7;transition:opacity 0.3s ease;}
.profile-notification .close-notification:hover {opacity:1;}

/* Estilos para la sección de horario */
.horario-section {
    background: white;
    border-radius: 12px;
    padding: 2rem;
    margin: 2rem 0;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    padding-bottom: 1rem;
    border-bottom: 3px solid #1e3a8a;
}

.section-header h2 {
    color: #1e3a8a;
    font-size: 1.8rem;
    font-weight: 700;
    margin: 0;
}

.btn-ver-horario {
    background: linear-gradient(#00bcff);
    color: white;
    border: none;
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 1rem;
}

.btn-ver-horario:hover {
    transform: translateY(-2px);
    
}

.horario-preview {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 1.5rem;
    min-height: 200px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.horario-preview .loading {
    color: #6c757d;
    font-size: 1.1rem;
    text-align: center;
}

.horario-preview .no-horario {
    color: #6c757d;
    font-size: 1.1rem;
    text-align: center;
}

.horario-preview .horario-table {
    width: 100%;
    border-collapse: collapse;
    background: white;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.horario-preview .horario-table th {
    background: linear-gradient(#00bcff);
    color: white;
    padding: 0.75rem;
    font-weight: 600;
    text-align: center;
    font-size: 0.9rem;
}

.horario-preview .horario-table td {
    border: 1px solid #e9ecef;
    padding: 0.5rem;
    text-align: center;
    vertical-align: middle;
    min-height: 40px;
    max-width: 100px;
    font-size: 0.8rem;
}

.horario-preview .time-cell {
    background: #f8f9fa;
    font-weight: 600;
    color: #495057;
    width: 80px;
}

.horario-preview .curso-info {
    background: linear-gradient(135deg, #e3f2fd, #bbdefb);
    padding: 0.4rem;
    border-radius: 4px;
    border-left: 2px solid #1e3a8a;
    margin: 0.2rem 0;
    font-size: 0.75rem;
    word-wrap: break-word;
    overflow: hidden;
}

.horario-preview .curso-name {
    font-weight: 700;
    color: #1e3a8a;
    font-size: 0.8rem;
    margin-bottom: 0.2rem;
    line-height: 1.1;
}

.horario-preview .horario-time {
    color: #6c757d;
    font-size: 0.7rem;
    font-weight: 500;
    line-height: 1.1;
}

.horario-preview .empty-cell {
    background: #f8f9fa;
    color: #adb5bd;
    font-style: italic;
    font-size: 0.7rem;
}

@media (max-width: 768px) {
    .section-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
    }
    
    .horario-preview .horario-table {
        font-size: 0.7rem;
    }
    
    .horario-preview .horario-table th, 
    .horario-preview .horario-table td {
        padding: 0.3rem;
    }
}
</style>
<?php if ($datos_incompletos): ?>
<div class="profile-notification" id="profile-notification">
    <button class="close-notification" onclick="closeNotification()">&times;</button>
    <h3><i class="fas fa-exclamation-triangle"></i> ¡Completa tu perfil!</h3>
    <p>Para una mejor experiencia, necesitamos que completes algunos datos de tu perfil:</p>
    <div class="missing-fields"><strong>Campos faltantes:</strong> <?php echo implode(', ', $campos_faltantes); ?></div>
    <a href="../models/perfil/perfil.php" class="btn-complete-profile"><i class="fas fa-user-edit"></i>Completar Perfil</a>
</div>
<script>
function closeNotification() {
    const notification = document.getElementById('profile-notification');
    if (notification) notification.style.display = 'none';
    localStorage.setItem('profileNotificationClosed', Date.now());
}
document.addEventListener('DOMContentLoaded', function() {
    const lastClosed = localStorage.getItem('profileNotificationClosed');
    if (lastClosed) {
        const timeDiff = Date.now() - parseInt(lastClosed);
        if (timeDiff < 24 * 60 * 60 * 1000) {
            const notification = document.getElementById('profile-notification');
            if (notification) notification.style.display = 'none';
        }
    }
});
</script>
<?php endif; ?>

<section class="professor-dashboard">
    <!-- Div para mostrar errores -->
    <div id="error-message" style="display:none;"></div>

    <div class="dashboard-summary">
        <div class="summary-card">
            <i class="fas fa-chalkboard-teacher"></i>
            <h3>Cursos Asignados</h3>
            <p id="cursos-count">0</p>
        </div>
        <div class="summary-card">
            <i class="fas fa-user-graduate"></i>
            <h3>Total Estudiantes</h3>
            <p id="estudiantes-count">0</p>
        </div>
        <div class="summary-card">
            <i class="fas fa-clock"></i>
            <h3>Horas de Clase</h3>
            <p id="horas-clase">0</p>
        </div>
    </div>

    <div class="horario-section">
        <div class="section-header">
            <h2>Mi Horario de Clases</h2>
            <button onclick="verHorarioCompleto()" class="btn-ver-horario">
                <i class="fas fa-calendar-alt"></i>
                Ver Horario Completo
            </button>
        </div>
        <div class="horario-preview" id="horario-preview">
            <!-- Vista previa del horario será insertada aquí -->
        </div>
    </div>

    <div class="dashboard-charts">
        <div class="chart-container">
            <h3>Distribución de Estudiantes por Curso</h3>
            <canvas id="estudiantesPorCursoChart"></canvas>
        </div>
    </div>

    <div class="cursos-asignados">
        <h2>Mis Cursos Asignados</h2>
        <div id="cursos-asignados-list" class="cursos-asignados-list">
            <!-- Cursos asignados will be dynamically inserted here -->
        </div>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="../js/dashboard_profesor_updater.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        dashboardProfesorUpdater.init();
        cargarHorarioPreview();
    });

    function verHorarioCompleto() {
        // Obtener el ID del profesor del usuario actual
        fetch('../models/horario/get_profesor_id_by_user.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'id_usuario=' + <?php echo $id_usuario; ?>
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.open('../models/horario/horario_profesor.php?id_profesor=' + data.id_profesor, '_blank');
            } else {
                alert('Error al obtener información del profesor: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Ocurrió un error al obtener la información del profesor');
        });
    }

    function cargarHorarioPreview() {
        const preview = document.getElementById('horario-preview');
        preview.innerHTML = '<div class="loading"><i class="fas fa-spinner fa-spin"></i> Cargando horario...</div>';

        // Obtener el ID del profesor del usuario actual
        fetch('../models/horario/get_profesor_id_by_user.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'id_usuario=' + <?php echo $id_usuario; ?>
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                cargarHorarioData(data.id_profesor);
            } else {
                preview.innerHTML = '<div class="no-horario"><i class="fas fa-calendar-times"></i><br>No se encontró información del profesor</div>';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            preview.innerHTML = '<div class="no-horario"><i class="fas fa-exclamation-triangle"></i><br>Error al cargar el horario</div>';
        });
    }

    function cargarHorarioData(idProfesor) {
        fetch('../models/horario/get_horario_preview.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'id_profesor=' + idProfesor
        })
        .then(response => response.json())
        .then(data => {
            console.log('Datos recibidos:', data); // Debug
            if (data.success && data.horarios.length > 0) {
                console.log('Horarios encontrados:', data.horarios); // Debug
                mostrarHorarioPreview(data.horarios);
            } else {
                console.log('No hay horarios o error:', data); // Debug
                document.getElementById('horario-preview').innerHTML = '<div class="no-horario"><i class="fas fa-calendar-times"></i><br>No tienes horarios asignados</div>';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('horario-preview').innerHTML = '<div class="no-horario"><i class="fas fa-exclamation-triangle"></i><br>Error al cargar el horario</div>';
        });
    }

    function mostrarHorarioPreview(horarios) {
        const preview = document.getElementById('horario-preview');
        
        console.log('Mostrando horario preview con datos:', horarios);
        
        // Crear tabla de horario
        let html = '<table class="horario-table">';
        html += '<thead><tr><th class="time-cell">Hora</th><th>Lunes</th><th>Martes</th><th>Miércoles</th><th>Jueves</th><th>Viernes</th><th>Sábado</th></tr></thead>';
        html += '<tbody>';

        // Solo mostrar horas de 6:00 AM a 2:00 PM
        const horas = [];
        for (let i = 6; i <= 14; i++) {
            horas.push(String(i).padStart(2, '0') + ':00');
        }
        const dias = ['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado'];

        horas.forEach(hora => {
            html += '<tr>';
            html += '<td class="time-cell">' + formatTime(hora) + '</td>';
            
            dias.forEach(dia => {
                html += '<td>';
                let cursoEncontrado = false;
                
                horarios.forEach(horario => {
                    if (horario[dia]) {
                        const [inicio, fin] = horario[dia].split(' - ');
                        
                        console.log(`Verificando ${dia} para ${horario.nombre_curso}: ${inicio} - ${fin} en hora ${hora}`);
                        
                        // Convertir a minutos para comparación más precisa
                        const horaActualMin = parseInt(hora.split(':')[0]) * 60 + parseInt(hora.split(':')[1]);
                        const horaSiguienteMin = horaActualMin + 60;
                        const horaInicioMin = parseInt(inicio.split(':')[0]) * 60 + parseInt(inicio.split(':')[1]);
                        const horaFinMin = parseInt(fin.split(':')[0]) * 60 + parseInt(fin.split(':')[1]);
                        
                        console.log(`Comparando: horaActual=${horaActualMin}, horaSiguiente=${horaSiguienteMin}, inicio=${horaInicioMin}, fin=${horaFinMin}`);
                        
                        // Verificar si la hora actual está dentro del rango del curso
                        if ((horaActualMin >= horaInicioMin && horaActualMin < horaFinMin) ||
                            (horaSiguienteMin > horaInicioMin && horaSiguienteMin <= horaFinMin) ||
                            (horaActualMin <= horaInicioMin && horaSiguienteMin >= horaFinMin)) {
                            console.log(`✅ Curso encontrado: ${horario.nombre_curso} en ${dia} a las ${hora}`);
                            html += '<div class="curso-info">';
                            html += '<div class="curso-name">' + horario.nombre_curso + '</div>';
                            html += '<div class="horario-time">' + formatTime(inicio) + ' - ' + formatTime(fin) + '</div>';
                            html += '</div>';
                            cursoEncontrado = true;
                        }
                    }
                });
                
                if (!cursoEncontrado) {
                    html += '<div class="empty-cell">-</div>';
                }
                html += '</td>';
            });
            html += '</tr>';
        });

        html += '</tbody></table>';
        preview.innerHTML = html;
    }

    function formatTime(time) {
        const [hours, minutes] = time.split(':');
        const hour = parseInt(hours);
        const ampm = hour >= 12 ? 'PM' : 'AM';
        const displayHour = hour % 12 || 12;
        return displayHour + ':' + minutes + ' ' + ampm;
    }

    function sprintf(format, ...args) {
        return format.replace(/%[sd]/g, (match, index) => {
            const arg = args[index];
            if (match === '%s') return String(arg);
            if (match === '%d') return parseInt(arg, 10);
            return match;
        });
    }
</script>
