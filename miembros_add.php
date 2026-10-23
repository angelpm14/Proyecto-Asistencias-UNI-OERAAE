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
    $nom          = trim($_POST['nombre']);
    $ape          = trim($_POST['apellido']);
    $dni          = trim($_POST['dni']);
    $rol          = intval($_POST['rol']);
    $ema          = trim($_POST['email']);
    $tel          = trim($_POST['telefono']);
    $dir          = trim($_POST['direccion']);
    $area         = intval($_POST['area']);
    $turno        = intval($_POST['turno']);
    $especialidad = trim($_POST['especialidad']);
    $acum         = floatval(str_replace(',', '.', trim($_POST['hors_acum'])));
    $neces        = floatval(str_replace(',', '.', trim($_POST['hors_neces'])));

    // Usamos sentencias preparadas para mayor seguridad
    $sql = "INSERT INTO miembros 
            (nombre, apellido, dni, id_rol, id_area, email, telefono, direccion, especialidad, id_turno, hors_acum, hors_neces) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($cn, $sql);
    mysqli_stmt_bind_param($stmt, "sssiissssssd", 
        $nom, $ape, $dni, $rol, $area, $ema, $tel, $dir, $especialidad, $turno, $acum, $neces
    );

    if (mysqli_stmt_execute($stmt)) {
        $msg = "Miembro registrado correctamente.";
    } else {
        $msg = "Error al registrar: " . mysqli_stmt_error($stmt);
    }
    mysqli_stmt_close($stmt);
}
?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <h1 class="m-0 text-dark">Nuevo Miembro</h1>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card shadow-sm">
                        <div class="card-header bg-primary text-white">
                            <h3 class="card-title">Ingresar datos del Miembro</h3>
                        </div>
                        <form role="form" method="POST" action="">
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
                                    <label for="area">Área</label>
                                    <select name="area" class="custom-select" required>
                                        <option value="" disabled selected>Seleccionar área...</option>
                                        <?php
                                        $sql_area = "SELECT id_area, nombre FROM areas";
                                        $res_area = mysqli_query($cn, $sql_area);
                                        while ($row = mysqli_fetch_assoc($res_area)) {
                                            echo "<option value='".$row['id_area']."'>".$row['nombre']."</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="especialidad">Especialidad</label>
                                    <input type="text" class="form-control" name="especialidad" id="especialidad" placeholder="Ingresar especialidad" required>
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
                                    <label for="hors_acum">Horas acumuladas</label>
                                    <input type="text" class="form-control" name="hors_acum" id="hors_acum" placeholder="Ej: 0" value="0" readonly>
                                </div>
                                <div class="form-group">
                                    <label for="hors_neces">Horas necesarias</label>
                                    <input type="text" class="form-control" name="hors_neces" id="hors_neces" placeholder="Ej: 1815h" required>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" name="enviar" class="btn btn-primary">Enviar</button>
                                <a href="miembros.php" class="btn btn-outline-danger">Cancelar</a>
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
    var campos = ['nombre','apellido','dni','email','telefono','direccion','especialidad','hors_neces'];
    for (let i = 0; i < campos.length; i++) {
        if(document.getElementById(campos[i]).value.trim() === ""){
            alert("Por favor, complete todos los campos obligatorios.");
            document.getElementById(campos[i]).focus();
            return false;
        }
    }
    return true;
}
</script>