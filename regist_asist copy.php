<?php
session_start();
if (!isset($_SESSION['id_usuario'])) {
    header("location:main.php");
    exit();
}

include './header.php';
require './conexion.php';

// --- Función para determinar el estado de llegada ---
function estadoLlegada($entrada_datetime, $hora_inicio_turno) {
    if (!$entrada_datetime) return "Faltó";

    $entrada_hora_ts = strtotime(date('H:i:s', strtotime($entrada_datetime)));
    $turno_hora_ts = strtotime($hora_inicio_turno);
    $margen = 5 * 60; // 5 minutos

    return ($entrada_hora_ts <= $turno_hora_ts + $margen) ? "A tiempo" : "Tarde";
}

// --- Registrar automáticamente todos los miembros para el día de hoy si no existe su registro ---
$miembros_sql = "SELECT m.*, t.hora_inicio AS turno_hora_inicio, t.nombre AS turno_nombre
                 FROM MIEMBROS m
                 LEFT JOIN TURNO t ON m.id_turno = t.id_turno
                 ORDER BY m.apellido, m.nombre";
$miembros_res = mysqli_query($cn, $miembros_sql);

while ($m = mysqli_fetch_assoc($miembros_res)) {
    $id_miembro = $m['id_miembro'];
    $turno_hora = $m['turno_hora_inicio'] ?? '08:00:00';

    // Verificar si ya hay un registro de asistencia hoy
    $check_sql = "SELECT id_regist FROM REGIST_ASIS WHERE id_miembro = ? AND DATE(entrada) = CURDATE() LIMIT 1";
    $stmt_check = mysqli_prepare($cn, $check_sql);
    mysqli_stmt_bind_param($stmt_check, "i", $id_miembro);
    mysqli_stmt_execute($stmt_check);
    $res_check = mysqli_stmt_get_result($stmt_check);
    $row_check = mysqli_fetch_assoc($res_check);
    mysqli_stmt_close($stmt_check);

    // Si no existe un registro para hoy, crear uno
    if (!$row_check) {
        // Si faltó o llegó tarde
        $estado = "Faltó"; // Por defecto se marca como "Faltó" si no hay registro de entrada
        $entrada_db = NULL; // No hay entrada
        $salida_db = NULL; // No hay salida
        $justifc_db = NULL; // Inicialmente no hay justificación

        // Insertar registro de ausencia
        $sql_insert = "INSERT INTO REGIST_ASIS (id_miembro, entrada, salida, justifc) 
                       VALUES (?, ?, ?, ?)";
        $stmt_insert = mysqli_prepare($cn, $sql_insert);
        mysqli_stmt_bind_param($stmt_insert, "iiss", $id_miembro, $entrada_db, $salida_db, $justifc_db);
        mysqli_stmt_execute($stmt_insert);
        mysqli_stmt_close($stmt_insert);
    }
}

// --- Procesar edición --- 
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editar'])) {
    $id_regist = intval($_POST['id_regist']);
    $entrada = $_POST['entrada'] ?? null;
    $salida = $_POST['salida'] ?? null;
    $justifc = trim($_POST['justifc'] ?? '');  // Cambié 'justificacion' por 'justifc'

    $entrada_db = $entrada ? date('Y-m-d H:i:s', strtotime($entrada)) : null;
    $salida_db = $salida ? date('Y-m-d H:i:s', strtotime($salida)) : null;
    $justifc_db = empty($justifc) ? null : $justifc;

    // Actualizar registro
    $sql_update = "UPDATE REGIST_ASIS SET entrada = ?, salida = ?, justifc = ? WHERE id_regist = ?";
    $stmt = mysqli_prepare($cn, $sql_update);
    mysqli_stmt_bind_param($stmt, "sssi", $entrada_db, $salida_db, $justifc_db, $id_regist);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    // --- Recalcular horas acumuladas del miembro --- 
    $sql_calc = "SELECT id_miembro, TIMESTAMPDIFF(SECOND, entrada, salida) AS segundos
                 FROM REGIST_ASIS 
                 WHERE id_regist = ?";
    $stmt_calc = mysqli_prepare($cn, $sql_calc);
    mysqli_stmt_bind_param($stmt_calc, "i", $id_regist);
    mysqli_stmt_execute($stmt_calc);
    $res_calc = mysqli_stmt_get_result($stmt_calc);
    $row_calc = mysqli_fetch_assoc($res_calc);
    mysqli_stmt_close($stmt_calc);

    if ($row_calc && $row_calc['segundos'] !== null) {
        $id_miembro = $row_calc['id_miembro'];

        // Recalcular total en horas decimales
        $sql_total = "SELECT TIMESTAMPDIFF(SECOND, entrada, salida) AS segundos 
                      FROM REGIST_ASIS 
                      WHERE id_miembro = ? AND entrada IS NOT NULL AND salida IS NOT NULL";
        $stmt_total = mysqli_prepare($cn, $sql_total);
        mysqli_stmt_bind_param($stmt_total, "i", $id_miembro);
        mysqli_stmt_execute($stmt_total);
        $res_total = mysqli_stmt_get_result($stmt_total);

        $segundos_totales = 0;
        while ($row_t = mysqli_fetch_assoc($res_total)) {
            $segundos_totales += $row_t['segundos'];
        }
        mysqli_stmt_close($stmt_total);

        // Convertir segundos a horas y redondear
        $horas_totales = round($segundos_totales / 3600, 2);

        // Actualizar horas acumuladas en la base de datos
        $sql_update_m = "UPDATE MIEMBROS SET hors_acum = ? WHERE id_miembro = ?";
        $stmt_update_m = mysqli_prepare($cn, $sql_update_m);
        mysqli_stmt_bind_param($stmt_update_m, "di", $horas_totales, $id_miembro);
        mysqli_stmt_execute($stmt_update_m);
        mysqli_stmt_close($stmt_update_m);
    }

    header("Location: regist_asist.php?msg=editado");
    exit();
}


// --- Mensaje de confirmación ---
$msg = '';
if (isset($_GET['msg']) && $_GET['msg'] === 'editado') {
    $msg = '<div class="alert alert-success">Registro actualizado correctamente.</div>';
}

// --- Obtener registros (histórico) ---
$sql = "SELECT ra.*, m.nombre, m.apellido, m.dni AS dni_miembro, 
               t.nombre AS turno_nombre, t.hora_inicio AS turno_hora_inicio
        FROM REGIST_ASIS ra
        LEFT JOIN MIEMBROS m ON ra.id_miembro = m.id_miembro
        LEFT JOIN TURNO t ON m.id_turno = t.id_turno
        ORDER BY ra.entrada DESC";

$res = mysqli_query($cn, $sql);
?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <h1 class="m-0 text-dark">Historial de Asistencias</h1>
            <?php echo $msg; ?>
        </div>
    </div>
    <div class="content">
        <div class="container-fluid">
            <div class="card shadow-sm">
                <div class="card-body table-responsive">
                    <table id="tablaHistorial" class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Miembro</th>
                                <th>DNI</th>
                                <th>Turno</th>
                                <th>Fecha Entrada</th>
                                <th>Fecha Salida</th>
                                <th>Duración</th>
                                <th>Estado</th>
                                <th>Justificación</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = mysqli_fetch_assoc($res)):
                                $estado = estadoLlegada($row['entrada'], $row['turno_hora_inicio'] ?? '08:00:00');
                                $entrada_val = $row['entrada'] ? date('Y-m-d\TH:i', strtotime($row['entrada'])) : '';
                                $salida_val = $row['salida'] ? date('Y-m-d\TH:i', strtotime($row['salida'])) : '';

                                // Calcular duración
                                $duracion = '-';
                                if ($row['entrada'] && $row['salida']) {
                                    $entrada_ts = new DateTime($row['entrada']);
                                    $salida_ts = new DateTime($row['salida']);
                                    $intervalo = $salida_ts->diff($entrada_ts);
                                    $segundos_totales = ($intervalo->days * 86400) + ($intervalo->h * 3600) + ($intervalo->i * 60) + $intervalo->s;
                                    $horas = floor($segundos_totales / 3600);
                                    $minutos = floor(($segundos_totales % 3600) / 60);
                                    $segundos = $segundos_totales % 60;
                                    $duracion = sprintf("%02d:%02d:%02d", $horas, $minutos, $segundos);
                                }
                            ?>
                            <tr>
                                <td><?php echo $row['id_regist']; ?></td>
                                <td><?php echo htmlspecialchars($row['nombre'] . ' ' . $row['apellido']); ?></td>
                                <td><?php echo htmlspecialchars($row['dni_miembro']); ?></td>
                                <td><?php echo htmlspecialchars($row['turno_nombre']); ?></td>
                                <td><?php echo $row['entrada'] ? date('d/m/Y H:i:s', strtotime($row['entrada'])) : '-'; ?></td>
                                <td><?php echo $row['salida'] ? date('d/m/Y H:i:s', strtotime($row['salida'])) : '-'; ?></td>
                                <td><?php echo $duracion; ?></td>
                                <td>
                                    <?php
                                    $badge_class = 'badge-secondary';
                                    if ($estado === 'A tiempo') $badge_class = 'badge-success';
                                    elseif ($estado === 'Tarde') $badge_class = 'badge-warning';
                                    elseif ($estado === 'Faltó') $badge_class = 'badge-danger';
                                    ?>
                                    <span class="badge <?php echo $badge_class; ?>"><?php echo $estado; ?></span>
                                </td>
                                <td>
                                    <?php if ($row['justifc']) : ?>
                                        <span class="badge badge-success">Justificado</span>
                                    <?php else: ?>
                                        <span class="badge badge-info">No justificado</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php
                                    // Si el estado es "Faltó" o "Tarde" y no está justificado, mostrar el botón de "Justificar"
                                    if ($estado === 'Faltó' || $estado === 'Tarde') {
                                        if (empty($row['justifc'])) {
                                            echo '<a href="edit_regist_asist.php?id=' . $row['id_regist'] . '" class="btn btn-sm btn-success">Justificar</a>';
                                        } else {
                                            // Si está justificado, mostrar el botón "Editar Justificación"
                                            echo '<a href="edit_regist_asist.php?id=' . $row['id_regist'] . '" class="btn btn-sm btn-warning">Editar Justificación</a>';
                                        }
                                    } else {
                                        echo '<span class="badge badge-success">No necesario</span>';
                                    }
                                    ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Editar -->
<div class="modal fade" id="modalEditar" tabindex="-1" role="dialog" aria-labelledby="modalEditarLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form method="POST" id="formEditar">
            <input type="hidden" name="id_regist" id="id_regist">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEditarLabel">Editar Registro de Asistencia</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="entrada">Hora de Entrada</label>
                        <input type="datetime-local" class="form-control" name="entrada" id="entrada">
                    </div>
                    <div class="form-group">
                        <label for="salida">Hora de Salida</label>
                        <input type="datetime-local" class="form-control" name="salida" id="salida">
                    </div>
                    <div class="form-group">
                        <label for="justifc">Justificación</label>
                        <textarea class="form-control" name="justifc" id="justifc" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="editar" class="btn btn-success">Guardar Cambios</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </form>
    </div>
</div>

<?php include './footer.php'; ?>

<script>
    $(document).ready(function() {
        $('#tablaHistorial').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json"
            },
            "order": [[4, "desc"]]
        });

        $('.btnEditar').click(function() {
            const id = $(this).data('id');
            const entrada = $(this).data('entrada');
            const salida = $(this).data('salida');
            const justifc = $(this).data('justifc');

            $('#id_regist').val(id);
            $('#entrada').val(entrada);
            $('#salida').val(salida);
            $('#justifc').val(justifc);

            $('#modalEditar').modal('show');
        });
    });
</script>
