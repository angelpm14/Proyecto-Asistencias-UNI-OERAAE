<?php
ob_start();  // Inicia el almacenamiento en búfer de salida
session_start();
if (!isset($_SESSION['id_usuario'])) {
    header("Location: main.php");
    exit();
}

require './conexion.php';
include './header.php';

$msg = '';

// 1. Validar que el ID del miembro sea válido
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    header("Location: miembros.php");
    exit();
}

// 2. Procesar el formulario cuando se envía (método POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idPost = intval($_POST['id']);

    if (isset($_POST['modificar'])) {
        $nombre       = trim($_POST['nombre']);
        $apellido     = trim($_POST['apellido']);
        $dni          = trim($_POST['dni']);
        $email        = trim($_POST['email'] ?? '');  // Esto asegura que no haya un '0' como valor
        $telefono     = trim($_POST['telefono']);
        $direccion    = trim($_POST['direccion']);
        $especialidad = trim($_POST['especialidad']);
        $hors_acum    = floatval(str_replace(',', '.', trim($_POST['hors_acum'])));
        $hors_neces   = floatval(str_replace(',', '.', trim($_POST['hors_neces'])));
        $rol          = intval($_POST['rol']);
        $area         = intval($_POST['area']);
        $turno        = intval($_POST['turno']);

        // Verificar si el email se ha cambiado, si no, mantén el email anterior
        if (empty($email)) {
            $email = $miembro['email']; // Mantener el valor original del email si no se cambia
        }

        // Preparar la consulta de actualización
        $sql_update = "UPDATE MIEMBROS SET
               nombre = ?, apellido = ?, dni = ?, id_rol = ?, id_area = ?,
               email = ?, telefono = ?, direccion = ?, especialidad = ?,
               id_turno = ?, hors_acum = ?, hors_neces = ?
               WHERE id_miembro = ?";

        $stmt = mysqli_prepare($cn, $sql_update);

        // Asegúrate de vincular todos los parámetros correctamente
        mysqli_stmt_bind_param($stmt, "sssiiissssddi", 
            $nombre, $apellido, $dni, $rol, $area, $email, $telefono,
            $direccion, $especialidad, $turno, $hors_acum, $hors_neces, $idPost
        );

        // Ejecutar la consulta y verificar si tuvo éxito
        if (mysqli_stmt_execute($stmt)) {
            // Redirigir a la lista de miembros después de la modificación
            header("Location: miembros.php?msg=" . urlencode("Miembro modificado correctamente."));
            exit();
        } else {
            $msg = "Error al modificar el miembro: " . mysqli_stmt_error($stmt);
        }
        mysqli_stmt_close($stmt);
    }
}

// 3. Obtener los datos actuales del miembro para mostrarlos en el formulario
$sql = "SELECT m.*, r.nombre AS rol_nombre, a.nombre AS area_nombre, t.nombre AS turno_nombre
        FROM MIEMBROS m
        LEFT JOIN ROL r ON m.id_rol = r.id_rol
        LEFT JOIN AREAS a ON m.id_area = a.id_area
        LEFT JOIN TURNO t ON m.id_turno = t.id_turno
        WHERE m.id_miembro = ? LIMIT 1";

$stmt = mysqli_prepare($cn, $sql);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$miembro = mysqli_fetch_assoc($result);

if (!$miembro) {
    header("Location: miembros.php?msg=" . urlencode("Miembro no encontrado"));
    exit();
}

// 4. Obtener las opciones para los menús desplegables (selects)
$roles_options = mysqli_query($cn, "SELECT id_rol, nombre FROM ROL");
$areas_options = mysqli_query($cn, "SELECT id_area, nombre FROM AREAS");
$turnos_options = mysqli_query($cn, "SELECT id_turno, nombre FROM TURNO");
?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <h1 class="m-0 text-dark">Editar Miembro</h1>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card shadow-sm">
                        <div class="card-header bg-primary text-white">
                            <h3 class="card-title">Datos del Miembro: <?php echo htmlspecialchars($miembro['nombre'] . ' ' . $miembro['apellido']); ?></h3>
                        </div>
                        <form method="POST" action="">
                            <div class="card-body">
                                <?php if ($msg) { ?>
                                    <div class="alert alert-info"><?php echo $msg; ?></div>
                                <?php } ?>

                                <input type="hidden" name="id" value="<?php echo (int)$miembro['id_miembro']; ?>" />
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label>Nombres</label>
                                        <input type="text" name="nombre" class="form-control" value="<?php echo htmlspecialchars($miembro['nombre']); ?>" required>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>Apellidos</label>
                                        <input type="text" name="apellido" class="form-control" value="<?php echo htmlspecialchars($miembro['apellido']); ?>" required>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label>DNI</label>
                                        <input type="text" name="dni" class="form-control" value="<?php echo htmlspecialchars($miembro['dni']); ?>" required>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>Correo</label>
                                        <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($miembro['email']); ?>" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Dirección</label>
                                    <input type="text" name="direccion" class="form-control" value="<?php echo htmlspecialchars($miembro['direccion']); ?>" required>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label>Teléfono</label>
                                        <input type="text" name="telefono" class="form-control" value="<?php echo htmlspecialchars($miembro['telefono']); ?>" required>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>Especialidad</label>
                                        <input type="text" name="especialidad" class="form-control" value="<?php echo htmlspecialchars($miembro['especialidad']); ?>" required>
                                    </div>
                                </div>
                                <hr>
                                <div class="form-row">
                                    <div class="form-group col-md-4">
                                        <label>Rol</label>
                                        <select name="rol" class="custom-select" required>
                                            <?php while ($r = mysqli_fetch_assoc($roles_options)) { ?>
                                                <option value="<?php echo $r['id_rol']; ?>" <?php if($miembro['id_rol']==$r['id_rol']) echo 'selected'; ?>>
                                                    <?php echo htmlspecialchars($r['nombre']); ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>Área</label>
                                        <select name="area" class="custom-select" required>
                                            <?php while ($a = mysqli_fetch_assoc($areas_options)) { ?>
                                                <option value="<?php echo $a['id_area']; ?>" <?php if($miembro['id_area']==$a['id_area']) echo 'selected'; ?>>
                                                    <?php echo htmlspecialchars($a['nombre']); ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>Turno</label>
                                        <select name="turno" class="custom-select" required>
                                            <?php while ($t = mysqli_fetch_assoc($turnos_options)) { ?>
                                                <option value="<?php echo $t['id_turno']; ?>" <?php if($miembro['id_turno']==$t['id_turno']) echo 'selected'; ?>>
                                                    <?php echo htmlspecialchars($t['nombre']); ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label>Horas Acumuladas</label>
                                        <input type="number" step="0.01" id="hors_acum" name="hors_acum" 
                                            class="form-control" 
                                            value="<?php echo number_format((float)$miembro['hors_acum'], 2, '.', ''); ?>" readonly>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label>Horas Necesarias</label>
                                        <input type="number" step="0.01" id="hors_neces" name="hors_neces" 
                                            class="form-control" 
                                            value="<?php echo number_format((float)$miembro['hors_neces'], 2, '.', ''); ?>">
                                    </div>
                                </div>
                                <!-- Muestra valores en tiempo real -->
                                <div class="form-group">
                                    <label>Progreso</label>
                                    <div id="progresoTexto" class="mb-2 alert alert-secondary p-2">
                                        <?php 
                                            echo "Acumuladas: " . number_format((float)$miembro['hors_acum'], 2, '.', '') . 
                                                " / Necesarias: " . number_format((float)$miembro['hors_neces'], 2, '.', ''); 
                                        ?>
                                    </div>

                                    <!-- Barra de progreso -->
                                    <div class="progress">
                                        <div id="barraProgreso" 
                                            class="progress-bar" 
                                            role="progressbar" 
                                            style="width: 0%;" 
                                            aria-valuenow="0" 
                                            aria-valuemin="0" 
                                            aria-valuemax="100">
                                            0%
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" name="modificar" class="btn btn-primary">Modificar Miembro</button>
                                <a href="miembros.php" class="btn btn-secondary">Volver a la Lista</a>
                                <button type="submit" name="eliminar" class="btn btn-danger float-right" onclick="return confirm('ATENCIÓN:\n¿Está seguro que desea eliminar a este miembro de forma permanente?');">Eliminar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include './footer.php'; ?>
<?php
ob_end_flush();  // Envía todo lo almacenado en el búfer
?>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const acum = document.getElementById("hors_acum");
    const neces = document.getElementById("hors_neces");
    const progresoTexto = document.getElementById("progresoTexto");
    const barraProgreso = document.getElementById("barraProgreso");

    function actualizarProgreso() {
        let valAcum = parseFloat(acum.value) || 0;
        let valNeces = parseFloat(neces.value) || 0;

        // Texto
        progresoTexto.textContent = `Acumuladas: ${valAcum.toFixed(2)} / Necesarias: ${valNeces.toFixed(2)}`;

        // Porcentaje
        let porcentaje = (valNeces > 0) ? (valAcum / valNeces) * 100 : 0;
        if (porcentaje > 100) porcentaje = 100;

        // Barra
        barraProgreso.style.width = porcentaje.toFixed(2) + "%";
        barraProgreso.setAttribute("aria-valuenow", porcentaje.toFixed(2));
        barraProgreso.textContent = porcentaje.toFixed(2) + "%";

        // Colores dinámicos
        if (porcentaje < 50) {
            barraProgreso.className = "progress-bar bg-warning";
        } else if (porcentaje < 100) {
            barraProgreso.className = "progress-bar bg-info";
        } else {
            barraProgreso.className = "progress-bar bg-success";
        }
    }

    // Inicializa al cargar
    actualizarProgreso();

    // Escucha cambios
    acum.addEventListener("input", actualizarProgreso);
    neces.addEventListener("input", actualizarProgreso);
});
</script>
