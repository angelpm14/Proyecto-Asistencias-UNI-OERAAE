<?php session_start();
if(!isset($_SESSION['id_usuario'])){
    header("location:main.php");
} else {
  
 ?>
<?php include './header.php'; ?>
<?php require './conexion.php'; ?>
<?php
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "DELETE FROM usuarios WHERE id_usu=$id";
    mysqli_query($cn, $sql);
}
?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">Administrar Estudiantes</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Administrar Estudiantes</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Lista de Estudiantes</h3>
                        </div>
                        
                        <!-- /.card-header -->
                        <div class="card-body">
                            <table id="example1" class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>ID </th>
                                        <th>Nombres </th>
                                        <th>Apellidos</th>
                                        <th>Correo</th>
                                        <th>Telefono</th>
                                        <th>Direccion</th>
                                        <th>Sexo</th>
                                        <th>Carrera </th>
                                        <th>Contraseña</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $sql = "SELECT * FROM usuarios where rol='estudiante'";
                                    $resul = mysqli_query($cn, $sql)
                                    ?>
                                    <?php while ($row = mysqli_fetch_array($resul)) { ?>

                                        <tr>
                                            <th scope="row"><?php echo $row[0] ?></th>
                                            <td><?php echo $row[1] ?></td>
                                            <td><?php echo $row[2] ?></td>
                                            <td><?php echo $row[4] ?></td>
                                            <td><?php echo $row[5] ?></td>
                                            <td><?php echo $row[6] ?></td>
                                            <td><?php echo $row[7] ?></td>
                                            <td><?php echo $row[8] ?></td>
                                            <td><?php echo $row[9] ?></td>
                                            <td>
                                                <a href="edit.php?id=<?php echo $row['id_usu'] ?>" class="btn btn-success btn-sm">Editar</a>
                                                <a href="delete.php?id=<?php echo $row['id_usu'] ?>" class="btn btn-delete btn-sm">Eliminar</a>
                                            </td>
                                        </tr>

                                    <?php } ?>

                                </tbody>
                                <tfoot>

                                    </body>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->




<?php include './footer.php'; ?>
 <?php } ?>

