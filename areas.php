
<?php 
session_start();
if(!isset($_SESSION['id_usuario'])){
    header("location:main.php");
} else {
?>
<?php include './header.php'; ?>
<?php require './conexion.php'; ?>
<?php
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "DELETE FROM AREAS WHERE id_area=$id";
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
                    <h1 class="m-0 text-dark">Administrar Áreas</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Administrar Áreas</li>
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
                            <h3 class="card-title">Lista de Áreas</h3>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <table id="example1" class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>ID Área</th>
                                        <th>Nombre</th>
                                        <th class="no-export">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $sql = "SELECT * FROM AREAS";
                                    $resul = mysqli_query($cn, $sql);
                                    ?>
                                    <?php while ($row = mysqli_fetch_array($resul)) { ?>
                                        <tr>
                                            <th scope="row"><?php echo $row['id_area'] ?></th>
                                            <td><?php echo $row['nombre'] ?></td>
                                            <td>
                                                <a href="edit_areas.php?id=<?php echo $row['id_area'] ?>" class="btn btn-success btn-sm">Editar</a>
                                                <a href="areas.php?id=<?php echo $row['id_area'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Seguro que desea eliminar esta área?');">Eliminar</a>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
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