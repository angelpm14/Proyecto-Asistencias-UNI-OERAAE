<?php 
session_start();
if(!isset($_SESSION['id_usuario'])){
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
    if (!empty($nombre)) {
        $sql = "INSERT INTO AREAS (nombre) VALUES ('$nombre')";
        $result = mysqli_query($cn, $sql);
        if ($result) {
            $msg = "Área registrada correctamente.";
        } else {
            $msg = "No se pudo registrar el área.";
        }
    } else {
        $msg = "Por favor, ingrese el nombre del área.";
    }
}
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">Nueva Área</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Nueva Área</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="content">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="card shadow-sm">
                        <div class="card-header bg-primary text-white">
                            <h3 class="card-title">Ingresar nombre de Área</h3>
                        </div>
                        <form method="POST" action="">
                            <div class="card-body">
                                <?php if (!empty($msg)) { ?>
                                    <div class="alert alert-info"><?php echo $msg; ?></div>
                                <?php } ?>
                                <div class="form-group">
                                    <label for="nombre">Nombre del Área</label>
                                    <input type="text" name="nombre" class="form-control" id="nombre" placeholder="Ingresar nombre de área" required>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" name="enviar" class="btn btn-primary">Registrar</button>
                                <a href="areas.php" class="btn btn-outline-danger">Cancelar</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include './footer.php'; ?>