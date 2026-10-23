<?php 
session_start();
if (!isset($_SESSION['id_usuario'])) {
    header("location:main.php");
    exit();
} 
?>

<?php require './conexion.php'; ?>
<?php include './header.php'; ?>

<?php
$msg = '';
if (isset($_GET['modificar'])) {
    $id = mysqli_real_escape_string($cn, $_GET['id']);
    $nombre = mysqli_real_escape_string($cn, $_GET['nombre']);
    $hora_inicio = mysqli_real_escape_string($cn, $_GET['hora_inicio']);
    $hora_fin = mysqli_real_escape_string($cn, $_GET['hora_fin']);

    if (!empty($nombre) && !empty($hora_inicio) && !empty($hora_fin)) {
        $sql = "UPDATE TURNO SET nombre='$nombre', hora_inicio='$hora_inicio', hora_fin='$hora_fin' WHERE id_turno='$id'";
        $result = mysqli_query($cn, $sql);
        if ($result) {
            $msg = "Horario modificado correctamente.";
        } else {
            $msg = "No se pudo modificar el horario.";
        }
    } else {
        $msg = "Por favor, complete todos los campos.";
    }
}

$id = $_GET['id'];
$sql = "SELECT * FROM TURNO WHERE id_turno='$id'";
$result = mysqli_query($cn, $sql);
$turno = mysqli_fetch_array($result);
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">Editar Horario</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Editar Horario</li>
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
                    <div class="card shadow-sm">
                        <div class="card-header bg-primary text-white">
                            <h3 class="card-title">Editar datos del Horario</h3>
                        </div>
                        <!-- form start -->
                        <form role="form" method="GET" action="" onsubmit="return validarFormulario()">
                            <div class="card-body">
                                <?php if (!empty($msg)) { ?>
                                    <div class="alert alert-info"><?php echo $msg; ?></div>
                                <?php } ?>
                                <input type="hidden" name="id" value="<?php echo $id ?>"/>
                                <div class="form-group">
                                    <label for="nombre">Nombre del Turno</label>
                                    <input type="text" name="nombre" class="form-control" id="nombre" placeholder="Ingresar nombre del turno" value="<?php echo $turno['nombre'] ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="hora_inicio">Hora de Inicio</label>
                                    <input type="time" class="form-control" name="hora_inicio" id="hora_inicio" value="<?php echo $turno['hora_inicio'] ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="hora_fin">Hora de Fin</label>
                                    <input type="time" class="form-control" name="hora_fin" id="hora_fin" value="<?php echo $turno['hora_fin'] ?>" required>
                                </div>
                            </div>
                            <!-- /.card-body -->
                            <div class="card-footer">
                                <button type="submit" name="modificar" class="btn btn-primary">Modificar</button>
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
