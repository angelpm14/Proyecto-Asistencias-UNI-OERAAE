<?php
session_start();
if (!isset($_SESSION['id_usuario'])) {
    header("Location: main.php");
    exit();
}

require './conexion.php';
include './header.php';

// Mensaje para feedback
$msg = '';

// 1) Validar ID
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    header("Location: usuarios.php");
    exit();
}

// 2) Procesar POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['eliminar'])) {
        $idPost = intval($_POST['id']);
        $del_sql = "DELETE FROM usuarios WHERE id_usu = '$idPost'";
        if (mysqli_query($cn, $del_sql)) {
            header("Location: usuarios.php?msg=" . urlencode("Usuario eliminado"));
            exit();
        } else {
            $msg = "Error al eliminar: " . mysqli_error($cn);
        }
    }

    if (isset($_POST['modificar'])) {
        $idPost   = intval($_POST['id']);
        $nombre   = mysqli_real_escape_string($cn, $_POST['nombre']);
        $apellido = mysqli_real_escape_string($cn, $_POST['apellido']);
        $dni      = mysqli_real_escape_string($cn, $_POST['dni']);
        $email    = mysqli_real_escape_string($cn, $_POST['email']);
        $telefono = mysqli_real_escape_string($cn, $_POST['telefono']);
        $direccion= mysqli_real_escape_string($cn, $_POST['direccion']);
        $sexo     = mysqli_real_escape_string($cn, $_POST['sexo']);
        $rol      = intval($_POST['rol']);     // ahora ID
        $area     = intval($_POST['area']);    // ahora ID
        $turno    = intval($_POST['turno']);   // ahora ID
        $usuario  = mysqli_real_escape_string($cn, $_POST['usuario']);
        $contra   = mysqli_real_escape_string($cn, $_POST['contra']);

        if ($nombre===''||$apellido===''||$dni===''||$email===''||$telefono===''||$direccion===''||$sexo===''||$rol<=0||$area<=0||$turno<=0||$usuario===''||$contra==='') {
            $msg = "Por favor complete todos los campos.";
        } else {
            $update = "UPDATE usuarios SET
                        nombre='$nombre',
                        apellido='$apellido',
                        dni='$dni',
                        id_rol='$rol',
                        id_area='$area',
                        id_turno='$turno',
                        email='$email',
                        telefono='$telefono',
                        direccion='$direccion',
                        sexo='$sexo',
                        usuario='$usuario',
                        contra='$contra'
                       WHERE id_usu='$idPost'";
            if (mysqli_query($cn, $update)) {
                $msg = "Usuario modificado correctamente.";
            } else {
                $msg = "Error al modificar: " . mysqli_error($cn);
            }
        }
    }
}

// 3) Obtener datos actuales con JOINs para mostrar nombres
$sql = "SELECT u.*, r.nombre AS rol_nombre, a.nombre AS area_nombre, t.nombre AS turno_nombre
        FROM usuarios u
        LEFT JOIN rol r ON u.id_rol = r.id_rol
        LEFT JOIN areas a ON u.id_area = a.id_area
        LEFT JOIN turno t ON u.id_turno = t.id_turno
        WHERE u.id_usu = '$id' LIMIT 1";
$res = mysqli_query($cn, $sql);
$usuario = mysqli_fetch_assoc($res);
if (!$usuario) {
    header("Location: usuarios.php?msg=" . urlencode("Usuario no encontrado"));
    exit();
}

// 4) Opciones de selects
$roles_options = mysqli_query($cn, "SELECT id_rol, nombre FROM rol");
$areas_options = mysqli_query($cn, "SELECT id_area, nombre FROM areas");
$turnos_options = mysqli_query($cn, "SELECT id_turno, nombre FROM turno");
?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <h1 class="m-0 text-dark">Editar Usuario</h1>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card shadow-sm">
                        <div class="card-header bg-primary text-white">
                            <h3 class="card-title">Editar datos del Usuario</h3>
                        </div>

                        <form method="POST">
                            <div class="card-body">
                                <?php if ($msg) { ?>
                                    <div class="alert alert-info"><?php echo $msg; ?></div>
                                <?php } ?>

                                <input type="hidden" name="id" value="<?php echo (int)$usuario['id_usu']; ?>" />

                                <div class="form-group">
                                    <label>DNI</label>
                                    <input type="text" name="dni" class="form-control" value="<?php echo htmlspecialchars($usuario['dni']); ?>" required>
                                </div>

                                <div class="form-group">
                                    <label>Nombres</label>
                                    <input type="text" name="nombre" class="form-control" value="<?php echo htmlspecialchars($usuario['nombre']); ?>" required>
                                </div>

                                <div class="form-group">
                                    <label>Apellidos</label>
                                    <input type="text" name="apellido" class="form-control" value="<?php echo htmlspecialchars($usuario['apellido']); ?>" required>
                                </div>

                                <div class="form-group">
                                    <label>Correo</label>
                                    <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($usuario['email']); ?>" required>
                                </div>

                                <div class="form-group">
                                    <label>Teléfono</label>
                                    <input type="text" name="telefono" class="form-control" value="<?php echo htmlspecialchars($usuario['telefono']); ?>" required>
                                </div>

                                <div class="form-group">
                                    <label>Dirección</label>
                                    <input type="text" name="direccion" class="form-control" value="<?php echo htmlspecialchars($usuario['direccion']); ?>" required>
                                </div>

                                <div class="form-group">
                                    <label>Sexo</label>
                                    <select name="sexo" class="custom-select" required>
                                        <option value="M" <?php if($usuario['sexo']=='M') echo 'selected'; ?>>Masculino</option>
                                        <option value="F" <?php if($usuario['sexo']=='F') echo 'selected'; ?>>Femenino</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>Rol</label>
                                    <select name="rol" class="custom-select" required>
                                        <?php while ($r = mysqli_fetch_assoc($roles_options)) { ?>
                                            <option value="<?php echo $r['id_rol']; ?>" <?php if($usuario['id_rol']==$r['id_rol']) echo 'selected'; ?>>
                                                <?php echo htmlspecialchars($r['nombre']); ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>Área</label>
                                    <select name="area" class="custom-select" required>
                                        <?php while ($a = mysqli_fetch_assoc($areas_options)) { ?>
                                            <option value="<?php echo $a['id_area']; ?>" <?php if($usuario['id_area']==$a['id_area']) echo 'selected'; ?>>
                                                <?php echo htmlspecialchars($a['nombre']); ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>Turno</label>
                                    <select name="turno" class="custom-select" required>
                                        <?php while ($t = mysqli_fetch_assoc($turnos_options)) { ?>
                                            <option value="<?php echo $t['id_turno']; ?>" <?php if($usuario['id_turno']==$t['id_turno']) echo 'selected'; ?>>
                                                <?php echo htmlspecialchars($t['nombre']); ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>Usuario</label>
                                    <input type="text" name="usuario" class="form-control" value="<?php echo htmlspecialchars($usuario['usuario']); ?>" required>
                                </div>

                                <div class="form-group">
                                    <label>Contraseña</label>
                                    <input type="password" name="contra" class="form-control" value="<?php echo htmlspecialchars($usuario['contra']); ?>" required>
                                </div>
                            </div>

                            <div class="card-footer">
                                <button type="submit" name="modificar" class="btn btn-primary">Modificar</button>
                                <button type="submit" name="eliminar" class="btn btn-danger" onclick="return confirm('¿Seguro que desea eliminar este usuario?');">Eliminar</button>
                                <a href="usuarios.php" class="btn btn-secondary">Volver</a>
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
    var campos = ["nombre","apellido","email","telefono","direccion","sexo","rol","area","contra"];
    for (var i=0;i<campos.length;i++){
        var el = document.getElementById(campos[i]);
        if (!el) continue;
        var val = el.value.trim();
        if (val === "") {
            alert("Por favor complete todos los campos.");
            el.focus();
            return false;
        }
    }
    return true;
}
</script>
