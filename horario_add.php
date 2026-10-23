<?php 
session_start();
if (!isset($_SESSION['id_usuario'])) {
    header("location:main.php");
    exit();
} 
?>

<?php include './header.php'; ?>
<?php require './conexion.php'; ?>

<?php
$msg = '';
if (isset($_POST['enviar'])) {
    $nombre = mysqli_real_escape_string($cn, $_POST['nombre']);
    $hora_inicio = mysqli_real_escape_string($cn, $_POST['hora_inicio']);
    $hora_fin = mysqli_real_escape_string($cn, $_POST['hora_fin']);

    // Validar que no esté vacío
    if (!empty($nombre) && !empty($hora_inicio) && !empty($hora_fin)) {
        $sql = "INSERT INTO TURNO (nombre, hora_inicio, hora_fin) VALUES ('$nombre', '$hora_inicio', '$hora_fin')";
        $result = mysqli_query($cn, $sql);
        if ($result) {
            $msg = "Horario registrado correctamente.";
        } else {
            $msg = "No se pudo registrar el horario.";
        }
    } else {
        $msg = "Por favor, complete todos los campos.";
    }
}
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">Administrar Horarios</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Administrar Horarios</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <div class="content">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <!-- left column -->
                <div class="col-md-8">
                    <!-- jquery validation -->
                    <div class="card shadow-sm">
                        <div class="card-header bg-primary text-white">
                            <h3 class="card-title">Ingresar Horario</h3>
                        </div>
                        <!-- /.card-header -->
                        <!-- form start -->
                        <form role="form" id="quickForm" method="POST" action="" onsubmit="return validarFormulario()">
                            <div class="card-body">
                                <?php if (!empty($msg)) { ?>
                                    <div class="alert alert-info"><?php echo $msg; ?></div>
                                <?php } ?>
                                <div class="form-group">
                                    <label for="nombre">Nombre del Turno</label>
                                    <input type="text" name="nombre" class="form-control" id="nombre" placeholder="Ingresar nombre del turno" required>
                                </div>
                                <div class="form-group">
                                    <label for="hora_inicio">Hora de Inicio</label>
                                    <input type="time" class="form-control" name="hora_inicio" id="hora_inicio" required>
                                </div>
                                <div class="form-group">
                                    <label for="hora_fin">Hora de Fin</label>
                                    <input type="time" class="form-control" name="hora_fin" id="hora_fin" required>
                                </div>
                            </div>
                            <!-- /.card-body -->
                            <div class="card-footer">
                                <button type="submit" name="enviar" class="btn btn-primary">Registrar Horario</button>
                                <a href="horario.php" class="btn btn-outline-danger">Cancelar</a>
                            </div>
                        </form>
                    </div>
                    <!-- /.card -->
                </div>
                <!--/.col (left) -->
            </div>
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->

<?php include './footer.php'; ?>

<script>
function validarFormulario() {
    var nombre = document.getElementById('nombre').value;
    var hora_inicio = document.getElementById('hora_inicio').value;
    var hora_fin = document.getElementById('hora_fin').value;

    if (nombre === "" || hora_inicio === "" || hora_fin === "") {
        alert("Por favor, complete todos los campos.");
        return false;
    }
    return true;
}
</script>
