<?php 
session_start();
if(!isset($_SESSION['id_usuario'])){
    header("location:main.php");
    exit();
} 

include './header.php';
require './conexion.php';

$msg = '';
if (isset($_POST['enviar'])) {
    $nom     = mysqli_real_escape_string($cn, $_POST['nombre']);
    $ape     = mysqli_real_escape_string($cn, $_POST['apellido']);
    $dni     = mysqli_real_escape_string($cn, $_POST['dni']);
    $rol     = intval($_POST['rol']);      
    $ema     = mysqli_real_escape_string($cn, $_POST['email']);
    $tel     = mysqli_real_escape_string($cn, $_POST['telefono']);
    $dir     = mysqli_real_escape_string($cn, $_POST['direccion']);
    $sexo    = mysqli_real_escape_string($cn, $_POST['sexo']); // varchar
    $area    = intval($_POST['area']);     
    $turno   = intval($_POST['turno']);
    $usu     = mysqli_real_escape_string($cn, $_POST['usuario']);
    $contra  = mysqli_real_escape_string($cn, $_POST['contra']);

    if (!empty($nom) && !empty($ape) && !empty($dni) && $rol > 0 && !empty($ema) && !empty($tel) && !empty($dir) && $sexo != '' && $area > 0 && $turno > 0 && !empty($usu) && !empty($contra)) {
        
        // Encriptamos la contraseña
        $hashContra = password_hash($contra, PASSWORD_DEFAULT);

        $sql = "INSERT INTO usuarios 
                (nombre, apellido, dni, id_rol, email, telefono, direccion, sexo, id_area, id_turno, usuario, contra) 
                VALUES 
                ('$nom', '$ape', '$dni', $rol, '$ema', '$tel', '$dir', '$sexo', $area, $turno, '$usu', '$hashContra')";

        $result = mysqli_query($cn, $sql);
        if ($result) {
            $msg = "✅ Usuario registrado correctamente.";
        } else {
            $msg = "❌ Error al registrar: " . mysqli_error($cn);
        }
    } else {
        $msg = "⚠️ Por favor, complete todos los campos.";
    }
}
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">Nuevo Usuario</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="usuarios.php">Usuarios</a></li>
                        <li class="breadcrumb-item active">Nuevo Usuario</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <div class="content">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card shadow-sm">
                        <div class="card-header bg-primary text-white">
                            <h3 class="card-title">Ingresar datos del Usuario</h3>
                        </div>
                        <form role="form" method="POST" action="" onsubmit="return validarFormulario()">
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
                                    <label for="dni">DNI</label>
                                    <input type="text" class="form-control" name="dni" id="dni" maxlength="8" placeholder="Ingresar DNI" required>
                                </div>
                                <div class="form-group">
                                    <label for="rol">Rol</label>
                                    <select name="rol" class="custom-select" required>
                                        <option value="" disabled selected>Seleccionar rol...</option>
                                        <?php
                                        $sql_roles = "SELECT id_rol, nombre FROM rol";
                                        $res_roles = mysqli_query($cn, $sql_roles);
                                        while ($row = mysqli_fetch_assoc($res_roles)) {
                                            echo "<option value='".$row['id_rol']."'>".$row['nombre']."</option>";
                                        }
                                        ?>
                                    </select>
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
                                    <label for="area">Área</label>
                                    <select name="area" class="custom-select" required>
                                        <option value="" disabled selected>Seleccionar área...</option>
                                        <?php
                                        $sql_area = "SELECT id_area, nombre FROM AREAS";
                                        $res_area = mysqli_query($cn, $sql_area);
                                        while ($row = mysqli_fetch_assoc($res_area)) {
                                            echo "<option value='".$row['id_area']."'>".$row['nombre']."</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="turno">Turno</label>
                                    <select name="turno" class="custom-select" required>
                                        <option value="" disabled selected>Seleccionar turno...</option>
                                        <?php
                                        $sql_turno = "SELECT id_turno, nombre FROM turno";
                                        $res_turno = mysqli_query($cn, $sql_turno);
                                        while ($row = mysqli_fetch_assoc($res_turno)) {
                                            echo "<option value='".$row['id_turno']."'>".$row['nombre']."</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="usuario">Usuario</label>
                                    <input type="text" class="form-control" name="usuario" id="usuario" placeholder="Ingresar usuario" required>
                                </div>
                                <div class="form-group">
                                    <label for="contra">Contraseña</label>
                                    <input type="password" name="contra" class="form-control" id="contra" placeholder="Ingresar Contraseña" required>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" name="enviar" class="btn btn-primary">Enviar</button>
                                <a href="usuarios.php" class="btn btn-outline-danger">Cancelar</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include './footer.php'; ?>

<script>
function validarFormulario() {
    var campos = ['nombre','apellido','dni','email','telefono','direccion','usuario','contra'];
    for (let i = 0; i < campos.length; i++) {
        if(document.getElementById(campos[i]).value === ""){
            alert("Por favor, complete todos los campos.");
            return false;
        }
    }
    return true;
}
</script>
