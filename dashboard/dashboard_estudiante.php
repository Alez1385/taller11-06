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

// Check if the user is logged in and is a student
if (!isset($_SESSION['username']) || !checkPermission('estudiante')) {
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
<section class="student-dashboard">
    <!-- Div para mostrar errores -->
    <div id="error-message" style="display:none;"></div>
    <h2>Mi Horario</h2>
    <?php
    // Renderizar una grilla de horario como en horario_estudiante
    require_once '../scripts/conexion.php';
    $id_usuario_tmp = $_SESSION['id_usuario'];
    $stmt_est_dash = $conn->prepare("SELECT id_estudiante FROM estudiante WHERE id_usuario = ?");
    if ($stmt_est_dash) {
        $stmt_est_dash->bind_param("i", $id_usuario_tmp);
        $stmt_est_dash->execute();
        $res_est_dash = $stmt_est_dash->get_result();
        $row_est_dash = $res_est_dash->fetch_assoc();
        if ($row_est_dash) {
            $id_est_dash = $row_est_dash['id_estudiante'];
            $sql_dash = "SELECT h.id_horario, c.nombre_curso, c.descripcion,
                                 h.lunes, h.martes, h.miercoles, h.jueves, h.viernes, h.sabado
                          FROM inscripciones i
                          JOIN cursos c ON i.id_curso = c.id_curso
                          JOIN horarios h ON h.id_curso = c.id_curso
                          WHERE i.id_estudiante = ? AND i.estado = 'aprobada'
                            AND h.id_horario IN (
                                SELECT MAX(h2.id_horario) FROM horarios h2 WHERE h2.id_curso = c.id_curso
                            )";
            $stmt_dash = $conn->prepare($sql_dash);
            if ($stmt_dash) {
                $stmt_dash->bind_param("i", $id_est_dash);
                $stmt_dash->execute();
                $res_dash = $stmt_dash->get_result();
                $horarios_dash = $res_dash->fetch_all(MYSQLI_ASSOC);
                echo '<div class="horario-container" style="background:#fff;border-radius:10px;padding:10px;margin-bottom:20px;"><table style="width:100%;border-collapse:collapse;">';
                echo '<thead><tr>';
                echo '<th style="background:#00bcff;color:#fff;padding:8px;">Hora</th>';
                $dias_lbl = ['Lunes'=>'lunes','Martes'=>'martes','Miércoles'=>'miercoles','Jueves'=>'jueves','Viernes'=>'viernes','Sábado'=>'sabado'];
                foreach ($dias_lbl as $dLabel => $dKey) {
                    echo '<th style="background:#00bcff;color:#fff;padding:8px;">'.$dLabel.'</th>';
                }
                echo '</tr></thead><tbody>';
                $horas_tmp = [];
                for ($i = 6; $i <= 14; $i++) { $horas_tmp[] = sprintf("%02d:00", $i); }
                foreach ($horas_tmp as $hora_tmp) {
                    echo '<tr>';
                    echo '<td style="background:#f8f9fa;font-weight:600;padding:6px;border:1px solid #e9ecef;">'.date('h:i A', strtotime($hora_tmp)).'</td>';
                    foreach ($dias_lbl as $dKey) {
                        echo '<td style="padding:6px;border:1px solid #e9ecef;">';
                        $found = false;
                        foreach ($horarios_dash as $h) {
                            if (!empty($h[$dKey])) {
                                list($ini,$fin) = explode(' - ',$h[$dKey]);
                                $ha = strtotime($hora_tmp);
                                $hs = strtotime('+1 hour',$ha);
                                $hi = strtotime($ini); $hf = strtotime($fin);
                                if (($ha >= $hi && $ha < $hf) || ($hs > $hi && $hs <= $hf) || ($ha <= $hi && $hs >= $hf)) {
                                    echo '<div style="background:linear-gradient(135deg,#e3f2fd,#bbdefb);padding:6px;border-left:3px solid #1e3a8a;border-radius:6px;">';
                                    echo '<div style="font-weight:700;color:#1e3a8a;">'.htmlspecialchars($h['nombre_curso']).'</div>';
                                    echo '<div style="font-size:12px;color:#6c757d;">'.date('h:i A',$hi).' - '.date('h:i A',$hf).'</div>';
                                    echo '</div>';
                                    $found = true; break;
                                }
                            }
                        }
                        if (!$found) { echo '<div style="color:#adb5bd;">-</div>'; }
                        echo '</td>';
                    }
                    echo '</tr>';
                }
                echo '</tbody></table></div>';
            }
        }
    }
    ?>
    <div id="inscripciones-list" class="inscripciones-list">
        <!-- Inscripciones will be dynamically inserted here -->
    </div>

    <h2>Mis Preinscripciones</h2>
    <div id="preinscripciones-list" class="preinscripciones-list">
        <!-- Preinscripciones will be dynamically inserted here -->
    </div>

    <h2>Cursos Disponibles</h2>
    <div id="course-list" class="course-list">
        <!-- Available courses will be dynamically inserted here -->
    </div>
</section>

<script src="../js/inscripcion-handler.js"></script>
<script src="../js/dashboard_updater.js"></script>

