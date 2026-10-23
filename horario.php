<?php
session_start();
if (!isset($_SESSION['id_usuario'])) {
    header("location:main.php");
    exit();
}

include './header.php';
require './conexion.php';

// Mensaje de éxito
$msg = '';
if (isset($_GET['msg']) && $_GET['msg'] === 'horario_eliminado') {
    $msg = '<div class="alert alert-success">Horario eliminado correctamente.</div>';
}

// Eliminar el turno (si el formulario de eliminación es enviado)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminar_id'])) {
    $id_turno = intval($_POST['eliminar_id']);

    // Eliminar el turno desde la base de datos
    $sql_delete = "DELETE FROM TURNO WHERE id_turno = ?";
    $stmt = mysqli_prepare($cn, $sql_delete);
    mysqli_stmt_bind_param($stmt, "i", $id_turno);
    if (mysqli_stmt_execute($stmt)) {
        header("Location: horarios.php?msg=horario_eliminado");
        exit();
    } else {
        $msg = '<div class="alert alert-danger">Error al eliminar el horario.</div>';
    }
}

// Obtener todos los horarios
$sql = "SELECT * FROM TURNO ORDER BY nombre";
$resul = mysqli_query($cn, $sql);
?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <h1 class="m-0 text-dark">Administrar Horarios</h1>
            <?php echo $msg; ?>
        </div>
    </div>
    <div class="content">
        <div class="container-fluid">
            <div class="card shadow-sm">
                <div class="card-body table-responsive">
                    <table id="example1" class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Turno</th>
                                <th>Hora de Entrada</th>
                                <th>Hora de Salida</th>
                                <th class="no-export">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = mysqli_fetch_array($resul)) { ?>
                                <tr>
                                    <td><?php echo $row['id_turno']; ?></td>
                                    <td><?php echo $row['nombre']; ?></td>
                                    <td><?php echo $row['hora_inicio']; ?></td>
                                    <td><?php echo $row['hora_fin']; ?></td>
                                    <td>
                                        <!-- Editar -->
                                        <a href="edit_horario.php?id=<?php echo $row['id_turno']; ?>" class="btn btn-success btn-sm">Editar</a>

                                        <!-- Eliminar (enlace con formulario POST oculto) -->
                                        <a href="horario.php" class="btn btn-danger btn-sm" onclick="if(confirm('¿Seguro que deseas eliminar este horario?')) { document.getElementById('eliminar-form-<?php echo $row['id_turno']; ?>').submit(); }">Eliminar</a>

                                        <!-- Formulario de eliminación (oculto) -->
                                        <form id="eliminar-form-<?php echo $row['id_turno']; ?>" action="horario.php" method="POST" style="display: none;">
                                            <input type="hidden" name="eliminar_id" value="<?php echo $row['id_turno']; ?>">
                                        </form>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include './footer.php'; ?>
