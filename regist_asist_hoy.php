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
    $turno_hora_ts = strtotime($hora_inicio_turno ?? '08:00:00');
    $margen = 5 * 60; // 5 minutos de tolerancia

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
    $check_sql = "SELECT id_regist FROM REGIST_ASIS WHERE id_miembro = ? AND fecha = CURDATE() LIMIT 1";
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
        $sql_insert = "INSERT INTO REGIST_ASIS (id_miembro, fecha, entrada, salida, justifc) 
                    VALUES (?, CURDATE(), ?, ?, ?)";
        $stmt_insert = mysqli_prepare($cn, $sql_insert);
        mysqli_stmt_bind_param($stmt_insert, "iiss", $id_miembro, $entrada_db, $salida_db, $justifc_db);
        mysqli_stmt_execute($stmt_insert);
        mysqli_stmt_close($stmt_insert);
    }
}

// --- Procesar edición de justificación ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editar'])) {
    $id_regist = intval($_POST['id_regist']);
    $entrada = $_POST['entrada'] ?? null;
    $salida = $_POST['salida'] ?? null;
    $justifc = trim($_POST['justifc'] ?? '');

    $entrada_db = $entrada ? date('Y-m-d H:i:s', strtotime($entrada)) : NULL;
    $salida_db = $salida ? date('Y-m-d H:i:s', strtotime($salida)) : NULL;
    $justifc_db = empty($justifc) ? NULL : $justifc;

    // Actualizar o insertar el registro de asistencia
    if ($id_regist) {
        // Si ya existe, actualizamos
        $sql_update = "UPDATE REGIST_ASIS SET entrada = ?, salida = ?, justifc = ? WHERE id_regist = ?";
        $stmt_update = mysqli_prepare($cn, $sql_update);
        mysqli_stmt_bind_param($stmt_update, "sssi", $entrada_db, $salida_db, $justifc_db, $id_regist);
        mysqli_stmt_execute($stmt_update);
        mysqli_stmt_close($stmt_update);
    } else {
        // Si no existe, creamos un nuevo registro
        $sql_insert = "INSERT INTO REGIST_ASIS (id_miembro, entrada, salida, justifc) 
                       VALUES (?, ?, ?, ?)";
        $stmt_insert = mysqli_prepare($cn, $sql_insert);
        mysqli_stmt_bind_param($stmt_insert, "iiss", $_POST['id_miembro'], $entrada_db, $salida_db, $justifc_db);
        mysqli_stmt_execute($stmt_insert);
        mysqli_stmt_close($stmt_insert);
    }

    header("Location: regist_asist_hoy.php?msg=editado");
    exit();
}

// Mensaje
$msg = '';
if (isset($_GET['msg']) && $_GET['msg'] === 'editado') {
    $msg = '<div class="alert alert-success">Registro actualizado correctamente.</div>';
}

?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <h1 class="m-0 text-dark">Administrar Registro de Asistencias - Hoy</h1>
            <?php echo $msg; ?>
        </div>
    </div>
    <div class="content">
        <div class="container-fluid">
            <div class="card shadow-sm">
                <div class="card-body table-responsive">
                    <table id="tablaAsistencias" class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Miembro</th>
                                <th>DNI</th>
                                <th>Turno</th>
                                <th>Hora Entrada</th>
                                <th>Hora Salida</th>
                                <th>Duración</th>
                                <th>Estado</th>
                                <th>Justificación</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        $miembros_sql = "SELECT m.*, t.hora_inicio AS turno_hora_inicio, t.nombre AS turno_nombre
                                         FROM MIEMBROS m
                                         LEFT JOIN TURNO t ON m.id_turno = t.id_turno
                                         ORDER BY m.apellido, m.nombre";
                        $miembros_res = mysqli_query($cn, $miembros_sql);

                        while ($m = mysqli_fetch_assoc($miembros_res)) {
                            $id_m = (int)$m['id_miembro'];

                            // Obtener el registro de hoy (si existe)
                            $stmt_today = mysqli_prepare($cn, "SELECT id_regist, entrada, salida, justifc FROM REGIST_ASIS WHERE id_miembro = ? AND fecha = CURDATE() ORDER BY entrada DESC LIMIT 1");
                            mysqli_stmt_bind_param($stmt_today, "i", $id_m);
                            mysqli_stmt_execute($stmt_today);
                            $res_today = mysqli_stmt_get_result($stmt_today);
                            $reg = mysqli_fetch_assoc($res_today);

                            $estado = "Faltó"; // Por defecto
                            $turno_hora = $m['turno_hora_inicio'] ?? '08:00:00';
                            if ($reg) {
                                $estado = estadoLlegada($reg['entrada'], $turno_hora);
                            }

                            // Calcular duración
                            $duracion = '-';
                            if ($reg && $reg['entrada'] && $reg['salida']) {
                                $entrada_ts = new DateTime($reg['entrada']);
                                $salida_ts = new DateTime($reg['salida']);
                                $intervalo = $salida_ts->diff($entrada_ts);
                                $segundos_totales = ($intervalo->days * 86400) + ($intervalo->h * 3600) + ($intervalo->i * 60) + $intervalo->s;
                                $horas = floor($segundos_totales / 3600);
                                $minutos = floor(($segundos_totales % 3600) / 60);
                                $segundos = $segundos_totales % 60;
                                $duracion = sprintf("%02d:%02d:%02d", $horas, $minutos, $segundos);
                            }

                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($m['id_miembro']); ?></td>
                                <td><?php echo htmlspecialchars($m['nombre'] . ' ' . $m['apellido']); ?></td>
                                <td><?php echo htmlspecialchars($m['dni']); ?></td>
                                <td><?php echo htmlspecialchars($m['turno_nombre'] ?? '-'); ?></td>
                                <td><?php echo ($reg && $reg['entrada']) ? date('d/m/Y H:i:s', strtotime($reg['entrada'])) : '-'; ?></td>
                                <td><?php echo ($reg && $reg['salida']) ? date('d/m/Y H:i:s', strtotime($reg['salida'])) : '-'; ?></td>
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
                                    <?php if ($reg && $reg['justifc']) : ?>
                                        <span class="badge badge-success">Justificado</span>
                                    <?php else: ?>
                                        <span class="badge badge-info">No justificado</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php
                        } // end while miembros
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include './footer.php'; ?>

<script>
    $(document).ready(function() {
        $('#tablaAsistencias').DataTable({
            language: {
                url: "//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json"
            },
            order: [[4, "desc"]],
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: '<i class="fas fa-file-excel"></i> Excel',
                    className: 'btn btn-success',
                    title: 'Asistencias_Hoy_' + new Date().toISOString().slice(0,10)
                },
                {
                    extend: 'pdfHtml5',
                    text: '<i class="fas fa-file-pdf"></i> PDF',
                    className: 'btn btn-danger',
                    title: 'Asistencias_Hoy_' + new Date().toISOString().slice(0,10),
                    orientation: 'landscape',
                    pageSize: 'A4'
                }
            ]
        });
    });
</script>
