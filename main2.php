<?php
session_start();
require './conexion.php';

// Opcional: establecer zona horaria del servidor como fallback
date_default_timezone_set('America/Lima');

if (isset($_POST['registrar'])) {
    $dni = trim($_POST['dni'] ?? '');
    $tipo = $_POST['tipo_asistencia'] ?? '';

    // Hora enviada desde la PC del usuario (campo hidden). Formato esperado: "YYYY-MM-DD HH:MM:SS"
    $hora_pc_raw = $_POST['hora_pc'] ?? null;
    $use_hora_pc = false;
    $now = date('Y-m-d H:i:s'); // fallback: servidor

    // Validar formato hora_pc (simple regex)
    if ($hora_pc_raw && preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $hora_pc_raw)) {
        $now = $hora_pc_raw;
        $use_hora_pc = true;
    } // si no es válido seguimos con la hora del servidor

    if ($dni === '' || $tipo === '' || $tipo === '----') {
        $mensaje = [
            'title' => 'Error',
            'type' => 'error',
            'text' => 'Por favor, ingrese DNI y seleccione tipo de asistencia.'
        ];
    } else {
        // Buscar id_miembro por DNI
        $sql_miembro = "SELECT id_miembro FROM MIEMBROS WHERE dni = ?";
        $stmt = mysqli_prepare($cn, $sql_miembro);
        mysqli_stmt_bind_param($stmt, "s", $dni);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $id_miembro);
        mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);

        if (!$id_miembro) {
            // DNI no encontrado
            $mensaje = [
                'title' => 'Error',
                'type' => 'error',
                'text' => 'DNI no registrado en miembros.'
            ];
        } else {
            // Usar la fecha (parte día) basada en $now (que puede venir del cliente)
            $fechaDia = substr($now, 0, 10); // YYYY-MM-DD
            $today_start = $fechaDia . ' 00:00:00';
            $today_end = $fechaDia . ' 23:59:59';

            if ($tipo === 'HORA DE ENTRADA') {
                // Verificar si ya existe entrada para hoy sin salida
                $sql_check = "SELECT id_regist FROM REGIST_ASIS WHERE id_miembro = ? AND entrada BETWEEN ? AND ? AND salida IS NULL";
                $stmt_check = mysqli_prepare($cn, $sql_check);
                mysqli_stmt_bind_param($stmt_check, "iss", $id_miembro, $today_start, $today_end);
                mysqli_stmt_execute($stmt_check);
                mysqli_stmt_store_result($stmt_check);
                $count = mysqli_stmt_num_rows($stmt_check);
                mysqli_stmt_close($stmt_check);

                if ($count > 0) {
                    // Ya tiene entrada sin salida hoy
                    $mensaje = [
                        'title' => 'Error',
                        'type' => 'error',
                        'text' => 'Ya registró su hora de entrada y no ha registrado salida aún.'
                    ];
                } else {
                    // --- CAMBIO: buscar primero la fila placeholder de hoy (creada por regist_asist.php) ---
                    $sql_placeholder = "SELECT id_regist FROM REGIST_ASIS WHERE id_miembro = ? AND fecha = ? AND entrada IS NULL LIMIT 1";
                    $stmt_ph = mysqli_prepare($cn, $sql_placeholder);
                    mysqli_stmt_bind_param($stmt_ph, "is", $id_miembro, $fechaDia);
                    mysqli_stmt_execute($stmt_ph);
                    mysqli_stmt_bind_result($stmt_ph, $id_regist_placeholder);
                    $tiene_placeholder = mysqli_stmt_fetch($stmt_ph);
                    mysqli_stmt_close($stmt_ph);

                    if ($tiene_placeholder) {
                        // Actualizar la fila placeholder existente (esto conserva 'fecha')
                        $sql_update_entrada = "UPDATE REGIST_ASIS SET entrada = ?, dni = ? WHERE id_regist = ?";
                        $stmt_upd = mysqli_prepare($cn, $sql_update_entrada);
                        mysqli_stmt_bind_param($stmt_upd, "ssi", $now, $dni, $id_regist_placeholder);
                        $exito = mysqli_stmt_execute($stmt_upd);
                        mysqli_stmt_close($stmt_upd);
                    } else {
                        // No había placeholder -> insertar incluyendo fecha
                        $sql_insert = "INSERT INTO REGIST_ASIS (id_miembro, fecha, entrada, dni) VALUES (?, ?, ?, ?)";
                        $stmt_insert = mysqli_prepare($cn, $sql_insert);
                        mysqli_stmt_bind_param($stmt_insert, "isss", $id_miembro, $fechaDia, $now, $dni);
                        $exito = mysqli_stmt_execute($stmt_insert);
                        mysqli_stmt_close($stmt_insert);
                    }
                    // --- FIN CAMBIO ---

                    if ($exito) {
                        $mensaje = [
                            'title' => 'Correcto',
                            'type' => 'success',
                            'text' => 'Hora de entrada registrada correctamente.'
                        ];
                    } else {
                        $mensaje = [
                            'title' => 'Error',
                            'type' => 'error',
                            'text' => 'Error al registrar hora de entrada.'
                        ];
                    }
                }
            } elseif ($tipo === 'HORA DE SALIDA') {
                // Buscar registro de entrada sin salida para hoy (según la fecha enviada)
                $sql_select = "SELECT id_regist FROM REGIST_ASIS WHERE id_miembro = ? AND entrada BETWEEN ? AND ? AND salida IS NULL ORDER BY entrada DESC LIMIT 1";
                $stmt_select = mysqli_prepare($cn, $sql_select);
                mysqli_stmt_bind_param($stmt_select, "iss", $id_miembro, $today_start, $today_end);
                mysqli_stmt_execute($stmt_select);
                mysqli_stmt_bind_result($stmt_select, $id_regist);
                $found = mysqli_stmt_fetch($stmt_select);
                mysqli_stmt_close($stmt_select);

                if ($found) {
                    // Actualizar salida con la hora de la PC (o fallback)
                    $sql_update = "UPDATE REGIST_ASIS SET salida = ? WHERE id_regist = ?";
                    $stmt_update = mysqli_prepare($cn, $sql_update);
                    mysqli_stmt_bind_param($stmt_update, "si", $now, $id_regist);
                    $exito = mysqli_stmt_execute($stmt_update);
                    mysqli_stmt_close($stmt_update);

                    if ($exito) {
                        $mensaje = [
                            'title' => 'Correcto',
                            'type' => 'success',
                            'text' => 'Hora de salida registrada correctamente.'
                        ];
                    } else {
                        $mensaje = [
                            'title' => 'Error',
                            'type' => 'error',
                            'text' => 'Error al registrar hora de salida.'
                        ];
                    }
                } else {
                    // No hay entrada sin salida para hoy (según la fecha del cliente)
                    $mensaje = [
                        'title' => 'Error',
                        'type' => 'error',
                        'text' => 'No se encontró registro de entrada sin salida para hoy. Primero registre su entrada.'
                    ];
                }
            } else {
                $mensaje = [
                    'title' => 'Error',
                    'type' => 'error',
                    'text' => 'Tipo de asistencia inválido.'
                ];
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Registro de Asistencia</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- PNotify CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/pnotify/3.2.1/pnotify.css">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="css/all.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <!-- icheck bootstrap -->
    <link rel="stylesheet" href="css/icheck-bootstrap.min.css">
    <!-- AdminLTE -->
    <link rel="stylesheet" href="css/adminlte.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">

    <style>
        .register-box {
            width: 400px;
            margin: 7% auto;
        }
    </style>
</head>
<body class="register-page" style="min-height: 586.781px;">
<div class="register-box">
    <div class="register-logo">
        <a href="https://portal.uni.edu.pe/"><b>UNIVERSIDAD NACIONAL DE INGENIERIA - UNI</b></a>
    </div>

    <div class="card">
        <div class="card-body register-card-body">

            <p class="login-box-msg">Registra tu asistencia o salida</p>
            <h2 id="fecha" style="color: #007bff; font-size: 2em; font-family: 'Source Sans Pro', sans-serif; text-align: center;"></h2>

            <!-- Agregado: input hidden para enviar la hora de la PC -->
            <form action="" method="post" onsubmit="return prepararHoraYValidar()">
                <input type="hidden" name="hora_pc" id="hora_pc">

                <div class="input-group mb-3">
                    <input type="text" class="form-control" name="dni" id="dni" placeholder="DNI" maxlength="8" required>
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-user"></span>
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-12">
                        <select class="form-control" name="tipo_asistencia" id="tipo_asistencia" required>
                            <option value="" disabled selected>Seleccionar tipo de asistencia...</option>
                            <option value="HORA DE ENTRADA">HORA DE ENTRADA</option>
                            <option value="HORA DE SALIDA">HORA DE SALIDA</option>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <button type="submit" name="registrar" class="btn btn-primary btn-block">Registrar</button>
                    </div>
                </div>
            </form>

            <a href="main.php" class="text-center d-block mt-3">ENTRAR AL SISTEMA</a>
        </div>
    </div>
</div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Bootstrap 4 -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="js/adminlte.min.js"></script>
<!-- PNotify JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/pnotify/3.2.1/pnotify.js"></script>

<script>
    // Mostrar fecha y hora actual y mantener hora_pc actualizada cada segundo
    function actualizarFechaHora() {
        let fecha = new Date();
        let fechaStr = fecha.toLocaleDateString();
        let horaStr = fecha.toLocaleTimeString();
        document.getElementById("fecha").innerHTML = `
            <span style="font-weight:bold;">${fechaStr}</span>
            <br>
            <span style="font-size:1.2em;">${horaStr}</span>
        `;

        // Rellenar el campo hidden con el formato YYYY-MM-DD HH:MM:SS
        const año = fecha.getFullYear();
        const mes = String(fecha.getMonth() + 1).padStart(2, '0');
        const dia = String(fecha.getDate()).padStart(2, '0');
        const hora = String(fecha.getHours()).padStart(2, '0');
        const minuto = String(fecha.getMinutes()).padStart(2, '0');
        const segundo = String(fecha.getSeconds()).padStart(2, '0');

        const fechaHora = `${año}-${mes}-${dia} ${hora}:${minuto}:${segundo}`;
        document.getElementById('hora_pc').value = fechaHora;
    }
    setInterval(actualizarFechaHora, 1000);
    actualizarFechaHora();

    // Validaciones del formulario (DNI + tipo) y asegurarse que hora_pc esté lista
    function validarFormulario() {
        let dni = document.getElementById('dni').value.trim();
        let tipo = document.getElementById('tipo_asistencia').value;

        if (dni === '') {
            alert('Por favor, ingrese su DNI.');
            document.getElementById('dni').focus();
            return false;
        }
        if (dni.length !== 8 || !/^\d{8}$/.test(dni)) {
            alert('El DNI debe tener 8 dígitos numéricos.');
            document.getElementById('dni').focus();
            return false;
        }
        if (tipo === '') {
            alert('Por favor, seleccione el tipo de asistencia.');
            document.getElementById('tipo_asistencia').focus();
            return false;
        }
        return true;
    }

    // Función que se ejecuta al enviar: asegura hora_pc y valida
    function prepararHoraYValidar() {
        // actualizar hora antes de enviar (por si el usuario tardó)
        actualizarFechaHora();
        return validarFormulario();
    }

    // Mostrar notificación si hay mensaje PHP
    <?php if (isset($mensaje)) : ?>
        new PNotify({
            title: "<?php echo $mensaje['title']; ?>",
            type: "<?php echo $mensaje['type']; ?>",
            text: "<?php echo $mensaje['text']; ?>",
            styling: "bootstrap3"
        });
    <?php endif; ?>
</script>
</body>
</html>