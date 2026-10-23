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
    $nom = mysqli_real_escape_string($cn, $_POST['nombre']);
    $ape = mysqli_real_escape_string($cn, $_POST['apellido']);
    $ema = mysqli_real_escape_string($cn, $_POST['email']);
    $tel = mysqli_real_escape_string($cn, $_POST['telefono']);
    $dir = mysqli_real_escape_string($cn, $_POST['direccion']);
    $sexo = mysqli_real_escape_string($cn, $_POST['sexo']);
    $carre = mysqli_real_escape_string($cn, $_POST['carrera']);
    $contra = mysqli_real_escape_string($cn, $_POST['contra']);

    if (!empty($nom) && !empty($ape) && !empty($ema) && !empty($tel) && !empty($dir) && $sexo != '' && $carre != '' && !empty($contra)) {
        $sql = "INSERT INTO usuarios VALUES (NULL, '$nom', '$ape', 'Estudiante', '$ema', '$tel', '$dir', '$sexo', '$carre', '$contra')";
        $result = mysqli_query($cn, $sql);
        if ($result) {
            $msg = "Se registró correctamente.";
        } else {
            $msg = "No se pudo registrar.";
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
                    <h1 class="m-0 text-dark">Nuevo Estudiante</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Nuevo Estudiante</li>
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
                            <h3 class="card-title">Ingresar datos de Estudiante</h3>
                        </div>
                        <!-- /.card-header -->
                        <!-- form start -->
                        <form role="form" id="quickForm" method="POST" action="" onsubmit="return validarFormulario()">
                            <div class="card-body">
                                <?php if (!empty($msg)) { ?>
                                    <div class="alert alert-info"><?php echo $msg; ?></div>
                                <?php } ?>
                                <div class="form-group">
                                    <label for="nombre">Nombres</label>
                                    <input type="text" name="nombre" class="form-control" id="nombre" placeholder="Ingresar nombre" required>
                                </div>
                                <div class="form-group">
                                    <label for="apellido">Apellidos</label>
                                    <input type="text" class="form-control" name="apellido" id="apellido" placeholder="Ingresar apellido" required>
                                </div>
                                <div class="form-group">
                                    <label for="email">Correo</label>
                                    <input type="email" class="form-control" name="email" id="email" placeholder="Ingresar Correo" required>
                                </div>
                                <div class="form-group">
                                    <label for="telefono">Teléfono</label>
                                    <input type="tel" class="form-control" name="telefono" id="telefono" placeholder="Ingresar Teléfono" required>
                                </div>
                                <div class="form-group">
                                    <label for="direccion">Dirección</label>
                                    <input type="text" class="form-control" name="direccion" id="direccion" placeholder="Ingresar Dirección" required>
                                </div>
                                <div class="form-group">
                                    <label for="sexo">Sexo</label>
                                    <select name="sexo" class="custom-select" required>
                                        <option value="" disabled selected>Seleccionar</option>
                                        <option value="Masculino">Hombre</option>
                                        <option value="Femenino">Mujer</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="carrera">Carrera</label>
                                    <select name="carrera" class="custom-select" required>
                                        <option value="" disabled selected>Seleccionar carrera...</option>
                                        <option value="Enfermeria Tecnica">Enfermería Técnica</option>
                                        <option value="Industria Alimentarias">Industrias Alimentarias</option>
                                        <option value="Desarrollo de Sistemas de Informacion">Desarrollo de Sistemas de Información</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="contra">Contraseña</label>
                                    <input type="password" name="contra" class="form-control" id="contra" placeholder="Ingresar Contraseña" required>
                                </div>
                            </div>
                            <!-- /.card-body -->
                            <div class="card-footer">
                                <button type="submit" name="enviar" class="btn btn-primary">Enviar</button>
                                <a href="estudiantes.php" class="btn btn-outline-danger">Cancelar</a>
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
    var apellido = document.getElementById('apellido').value;
    var email = document.getElementById('email').value;
    var telefono = document.getElementById('telefono').value;
    var direccion = document.getElementById('direccion').value;
    var sexo = document.getElementById('sexo').value;
    var carrera = document.getElementById('carrera').value;
    var contra = document.getElementById('contra').value;

    if (nombre === "" || apellido === "" || email === "" || telefono === "" || direccion === "" || sexo === "" || carrera === "" || contra === "") {
        alert("Por favor, complete todos los campos.");
        return false;
    }
    return true;
}
</script>
