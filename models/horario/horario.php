<?php
require_once '../../scripts/auth.php';
require_once '../../scripts/conexion.php';
require_once '../../scripts/config.php';
requireLogin();
checkPermission('admin');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_curso = $_POST['curso'];
    $id_profesor = $_POST['profesor'];
    $dias = ['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado'];
    
    // Limpiar cualquier horario previo del curso antes de crear uno nuevo
    $stmt_cleanup = $conn->prepare("DELETE FROM horarios WHERE id_curso = ?");
    $stmt_cleanup->bind_param("i", $id_curso);
    $stmt_cleanup->execute();
    $stmt_cleanup->close();

    {
        $horarios = array_fill(0, 6, null);
        $conflicto = false;
        $error = "";
        
        $horas_semanales_nuevas = 0;
        foreach ($dias as $index => $dia) {
            if (!empty($_POST["hora_inicio"][$dia]) && !empty($_POST["hora_fin"][$dia])) {
                $hora_inicio = $_POST["hora_inicio"][$dia];
                $hora_fin = $_POST["hora_fin"][$dia];
                
                // Validar que las horas estén dentro del rango permitido (6:00 AM - 2:00 PM)
                if (strtotime($hora_inicio) < strtotime('06:00') || strtotime($hora_fin) > strtotime('14:00')) {
                    $conflicto = true;
                    $error = "Las horas deben estar entre las 6:00 AM y las 2:00 PM para el día " . ucfirst($dia) . ".";
                    break;
                }
                
                // Verificar que la hora de inicio sea menor que la hora de fin
                if (strtotime($hora_inicio) >= strtotime($hora_fin)) {
                    $conflicto = true;
                    $error = "La hora de inicio debe ser menor que la hora de fin para el día " . ucfirst($dia) . ".";
                    break;
                }
                
                $horarios[$index] = $hora_inicio . " - " . $hora_fin;

                // Acumular horas (bloques de 1h)
                $horas_semanales_nuevas += max(0, (strtotime($hora_fin) - strtotime($hora_inicio)) / 3600);
                
                // Verificar conflicto de horarios
                $sql = "SELECT * FROM horarios WHERE id_profesor = ? AND $dia IS NOT NULL";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $id_profesor);
                $stmt->execute();
                $result = $stmt->get_result();
                
                while ($row = $result->fetch_assoc()) {
                    list($h_inicio, $h_fin) = explode(" - ", $row[$dia]);
                    if ((strtotime($hora_inicio) >= strtotime($h_inicio) && strtotime($hora_inicio) < strtotime($h_fin)) ||
                        (strtotime($hora_fin) > strtotime($h_inicio) && strtotime($hora_fin) <= strtotime($h_fin)) ||
                        (strtotime($hora_inicio) <= strtotime($h_inicio) && strtotime($hora_fin) >= strtotime($h_fin))) {
                        $conflicto = true;
                        $error = "Conflicto de horario para el profesor en el día " . ucfirst($dia) . ".";
                        break 2;
                    }
                }
            }
        }
        
        // Calcular horas actuales ya asignadas al profesor esta semana en otros cursos
        if (!$conflicto) {
            $sql_horas = "SELECT lunes, martes, miercoles, jueves, viernes, sabado FROM horarios WHERE id_profesor = ?";
            $stmt_horas = $conn->prepare($sql_horas);
            $stmt_horas->bind_param("i", $id_profesor);
            $stmt_horas->execute();
            $res_horas = $stmt_horas->get_result();
            $horas_asignadas_actuales = 0;
            while ($row = $res_horas->fetch_assoc()) {
                foreach ($dias as $d) {
                    if (!empty($row[$d])) {
                        list($hi, $hf) = explode(' - ', $row[$d]);
                        $horas_asignadas_actuales += max(0, (strtotime($hf) - strtotime($hi)) / 3600);
                    }
                }
            }
            $stmt_horas->close();

            if (($horas_asignadas_actuales + $horas_semanales_nuevas) > 8) {
                $conflicto = true;
                $error = "Este profesor excede el máximo de 8 horas semanales con este horario.";
            }
        }

        if (!$conflicto) {
            $sql = "INSERT INTO horarios (id_curso, id_profesor, lunes, martes, miercoles, jueves, viernes, sabado) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("iissssss", $id_curso, $id_profesor, $horarios[0], $horarios[1], $horarios[2], $horarios[3], $horarios[4], $horarios[5]);
            
            if ($stmt->execute()) {
                $_SESSION['mensaje'] = "Horario creado exitosamente.";
                header("Location: horarios_asignados.php");
                exit();
            } else {
                $error = "Error al crear el horario: " . $conn->error;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asignación de Horarios</title>
    <link rel="stylesheet" href="css/horario.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons+Sharp">
</head>
<body>
    <div class="dashboard-container">
        <?php include "../../scripts/sidebar.php"; ?>
        <div class="main-content">
            <header class="header">
                <div class="header-left">
                    <h1>Asignación de Horarios</h1>
                </div>
                <div class="header-right">
                    <button onclick="window.location.href='horarios_asignados.php'" class="btn-back">Volver a Horarios Asignados</button>
                </div>
            </header>

            <section class="content">
                <h2>Asignación de Horarios</h2>
                <p><strong>Nota:</strong> Los horarios deben estar entre las 6:00 AM y las 2:00 PM.</p>
                
                <?php if (isset($error)): ?>
                    <div class="error-message"><?php echo $error; ?></div>
                <?php endif; ?>

                <form id="horarioForm" method="POST">
                    <div class="form-group">
                        <label for="curso">Curso:</label>
                        <select id="curso" name="curso" required>
                            <option value="">Seleccione un curso</option>
                            <?php
                            $sql_cursos = "SELECT c.id_curso, c.nombre_curso 
                                           FROM cursos c
                                           LEFT JOIN horarios h ON c.id_curso = h.id_curso
                                           WHERE c.estado = 'activo' AND h.id_horario IS NULL";
                            $result_cursos = $conn->query($sql_cursos);
                            while ($curso = $result_cursos->fetch_assoc()) {
                                echo "<option value='" . $curso['id_curso'] . "'>" . htmlspecialchars($curso['nombre_curso']) . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="profesor">Profesor:</label>
                        <select id="profesor" name="profesor" required>
                            <option value="">Seleccione un profesor</option>
                            <?php
                            $sql_profesores = "SELECT p.id_profesor, u.nombre, u.apellido FROM profesor p JOIN usuario u ON p.id_usuario = u.id_usuario";
                            $result_profesores = $conn->query($sql_profesores);
                            while ($profesor = $result_profesores->fetch_assoc()) {
                                echo "<option value='" . $profesor['id_profesor'] . "'>" . htmlspecialchars($profesor['nombre'] . ' ' . $profesor['apellido']) . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Horario:</label>
                        <?php
                        $dias = ['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado'];
                        foreach ($dias as $dia) {
                            echo "<div class='dia-horario'>";
                            echo "<label>" . ucfirst($dia) . ":</label>";
                            echo "<input type='time' name='hora_inicio[$dia]' min='06:00' max='14:00'>";
                            echo "<input type='time' name='hora_fin[$dia]' min='06:00' max='14:00'>";
                            echo "</div>";
                        }
                        ?>
                    </div>
                    <div class="form-actions">
                        <button type="button" id="asignarAleatorio" class="btn-random">Asignar Horario Aleatorio</button>
                        <button type="submit" class="btn-submit">Crear Horario</button>
                    </div>
                </form>
            </section>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const profesorSelect = document.getElementById('profesor');
        const dias = ['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado'];
        const submitButton = document.querySelector('.btn-submit');
        const asignarAleatorioBtn = document.getElementById('asignarAleatorio');
        let formValido = true;

        // Función para generar horarios aleatorios
        function generarHorarioAleatorio() {
            const cursoSelect = document.getElementById('curso');
            const profesorSelect = document.getElementById('profesor');
            
            if (!cursoSelect.value || !profesorSelect.value) {
                alert('Por favor seleccione un curso y un profesor primero');
                return;
            }
            
            // Mostrar indicador de carga
            asignarAleatorioBtn.disabled = true;
            asignarAleatorioBtn.textContent = 'Asignando...';
            
            // Llamar al endpoint de asignación aleatoria
            fetch('asignar_horario_aleatorio.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `id_curso=${cursoSelect.value}&id_profesor=${profesorSelect.value}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Horario aleatorio asignado exitosamente');
                    // Recargar la página para mostrar el nuevo horario
                    window.location.href = 'horarios_asignados.php';
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Ocurrió un error al asignar el horario aleatorio');
            })
            .finally(() => {
                asignarAleatorioBtn.disabled = false;
                asignarAleatorioBtn.textContent = 'Asignar Horario Aleatorio';
            });
        }

        function verificarDisponibilidad() {
            const idProfesor = profesorSelect.value;
            if (!idProfesor) {
                alert('Por favor seleccione un profesor primero');
                return;
            }
            
            formValido = true;
            const promesas = dias.map(dia => {
                const horaInicio = document.querySelector(`input[name="hora_inicio[${dia}]"]`).value;
                const horaFin = document.querySelector(`input[name="hora_fin[${dia}]"]`).value;

                if (horaInicio && horaFin) {
                    return fetch('verificar_disponibilidad.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `id_profesor=${idProfesor}&dia=${dia}&hora_inicio=${horaInicio}&hora_fin=${horaFin}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (!data.disponible) {
                            formValido = false;
                            if (data.error) {
                                alert(`Error en ${dia}: ${data.error}`);
                            } else {
                                alert(`Conflicto de horario en ${dia}: El profesor ya tiene un horario asignado en este período.`);
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        formValido = false;
                    });
                }
                return Promise.resolve();
            });

            Promise.all(promesas).then(() => {
                if (submitButton) {
                    submitButton.disabled = !formValido;
                }
            });
        }

        // Event listeners
        profesorSelect.addEventListener('change', verificarDisponibilidad);
        dias.forEach(dia => {
            document.querySelector(`input[name="hora_inicio[${dia}]"]`).addEventListener('change', verificarDisponibilidad);
            document.querySelector(`input[name="hora_fin[${dia}]"]`).addEventListener('change', verificarDisponibilidad);
        });
        
        asignarAleatorioBtn.addEventListener('click', function() {
            if (!profesorSelect.value) {
                alert('Por favor seleccione un profesor primero');
                return;
            }
            generarHorarioAleatorio();
        });

        // Prevenir envío del formulario si no es válido
        document.getElementById('horarioForm').addEventListener('submit', function(event) {
            verificarDisponibilidad();
            if (!formValido) {
                event.preventDefault();
                alert('Por favor, corrija los horarios en conflicto antes de crear el horario.');
            }
        });
    });
    </script>
</body>
</html>