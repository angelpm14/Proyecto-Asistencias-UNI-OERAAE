<?php 
session_start();
if(!isset($_SESSION['id_usuario'])){
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
    $nom = mysqli_real_escape_string($cn, $_GET['nombre']);

    if (!empty($nom)) {
        $sql = "UPDATE areas SET nombre='$nom' WHERE id_area='$id'";
        $result = mysqli_query($cn, $sql);
        if ($result) {
            $msg = "Área modificada correctamente.";
        } else {
            $msg = "No se pudo modificar el área.";
        }
    } else {
        $msg = "Por favor, complete todos los campos.";
    }
}

$id = $_GET['id'];
$sql = "SELECT * FROM areas WHERE id_area='$id'";
$result = mysqli_query($cn, $sql);
$area = mysqli_fetch_array($result);
?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">Editar Área</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Editar Área</li>
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
                            <h3 class="card-title">Editar datos del Área</h3>
                        </div>
                        <!-- form start -->
                        <form role="form" method="GET" action="" onsubmit="return validarFormulario()">
                            <div class="card-body">
                                <?php if (!empty($msg)) { ?>
                                    <div class="alert alert-info"><?php echo $msg; ?></div>
                                <?php } ?>
                                <input type="hidden" name="id" value="<?php echo $id ?>"/>
                                <div class="form-group">
                                    <label for="nombre">Nombre del Área</label>
                                    <input type="text" name="nombre" class="form-control" id="nombre" placeholder="Ingresar nombre del área" value="<?php echo $area['nombre'] ?>" required>
                                </div>
                            </div>
                            <!-- /.card-body -->
                            <div class="card-footer">
                                <button type="submit" name="modificar" class="btn btn-primary">Modificar</button>
                                <a href="areas.php" class="btn btn-outline-danger">Cancelar</a>
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

<?php
