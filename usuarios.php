<?php
session_start();
if (!isset($_SESSION['id_usuario'])) {
    header("location:main.php");
} else {
?>
<?php include './header.php'; ?>
<?php require './conexion.php'; ?>
<?php
// Eliminar usuario si se pasa un id por GET
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $sql = "DELETE FROM USUARIOS WHERE id_usu=$id";
    mysqli_query($cn, $sql);
}
?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">Administrar Usuarios</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Administrar Usuarios</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Lista de Usuarios</h3>
                        </div>
                        <div class="card-body">
                            <table id="example1" class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nombre</th>
                                        <th>Apellido</th>
                                        <th>DNI</th>
                                        <th>Rol</th>
                                        <th>Email</th>
                                        <th>Teléfono</th>
                                        <th>Dirección</th>
                                        <th>Sexo</th>
                                        <th>Área</th>
                                        <th>Usuario</th>
                                        <th class="no-export">Contraseña</th>
                                        <th class="no-export">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    // Consulta con JOIN para traer rol y área por nombre
                                    $sql = "SELECT u.id_usu, u.nombre, u.apellido, u.dni,
                                                   r.nombre AS rol_nombre, 
                                                   u.email, u.telefono, u.direccion, u.sexo, 
                                                   a.nombre AS area_nombre, 
                                                   u.usuario, u.contra
                                            FROM USUARIOS u
                                            LEFT JOIN ROL r ON u.id_rol = r.id_rol
                                            LEFT JOIN AREAS a ON u.id_area = a.id_area";
                                    $resul = mysqli_query($cn, $sql);
                                    ?>
                                    <?php while ($row = mysqli_fetch_assoc($resul)) { ?>
                                        <tr>
                                            <th scope="row"><?php echo $row['id_usu'] ?></th>
                                            <td><?php echo $row['nombre'] ?></td>
                                            <td><?php echo $row['apellido'] ?></td>
                                            <td><?php echo $row['dni'] ?></td>
                                            <td><?php echo $row['rol_nombre'] ?></td>
                                            <td><?php echo $row['email'] ?></td>
                                            <td><?php echo $row['telefono'] ?></td>
                                            <td><?php echo $row['direccion'] ?></td>
                                            <td><?php echo $row['sexo'] ?></td>
                                            <td><?php echo $row['area_nombre'] ?></td>
                                            <td><?php echo $row['usuario'] ?></td>
                                            <td><?php echo $row['contra'] ?></td>
                                            <td>
                                                <a href="edit_usuarios.php?id=<?php echo $row['id_usu']; ?>" class="btn btn-success btn-sm">Editar</a>
                                                <a href="usuarios.php?id=<?php echo $row['id_usu'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Seguro que desea eliminar este usuario?');">Eliminar</a>
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
    </div>
</div>
<?php include './footer.php'; ?>
<?php } ?>
