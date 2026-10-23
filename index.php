<?php 
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: main.php");
    exit();
    
}
?>
<?php include './header.php'; ?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">Menu Principal</h1>
                </div><!-- /.col -->
                
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <br><br><br><br><br><br>
                        <br><br><br><br><br>
                        <br><br><br>
                        <h1 style="text-align: center;">Bienvenido a la pagina de Asistencias :b</h1>
                        <br><br><br><br><br><br>
                        <br><br><br><br><br>
                        <br><br>
                    </div>


                </div>
                <!-- /.col-md-6 -->
                
                <!-- /.col-md-6 -->
            </div>
            <!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->




<?php include './footer.php'; ?>
