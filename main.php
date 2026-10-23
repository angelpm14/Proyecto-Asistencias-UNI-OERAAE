<?php session_start(); ?>
<?php require './conexion.php'; ?>


<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>Login</title>
        <!-- Tell the browser to be responsive to screen width -->
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- Font Awesome -->
        <link rel="stylesheet" href="css/all.min.css">
        <!-- Ionicons -->
        <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
        <!-- icheck bootstrap -->
        <link rel="stylesheet" href="css/icheck-bootstrap.min.css">
        <!-- Theme style -->
        <link rel="stylesheet" href="css/adminlte.min.css">
        <!-- Google Font: Source Sans Pro -->
        <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
    </head>
<body class="hold-transition lockscreen">
    <!-- Automatic element centering -->
    <div class="lockscreen-wrapper">
    <div class="lockscreen-logo">
        <a href="https://portal.uni.edu.pe/"><b>UNIVERSIDAD NACIONAL DE INGENIERIA - UNI</b> </a>
    </div>
    <!-- User name -->
    <div class="lockscreen-name">ADMINISTRADOR</div>

    <!-- START LOCK SCREEN ITEM -->
    <div class="lockscreen-item">
        <!-- lockscreen image -->
        <div class="lockscreen-image">
        <img src="img/LOGO-UNI.png" alt="User Image">
        </div>
        <!-- /.lockscreen-image -->

        <!-- lockscreen credentials (contains the form) -->
        <form class="lockscreen-credentials" action="" method="post">
            <div class="input-group">
                <input type="password" name="contra" class="form-control" placeholder="password">

                <div class="input-group-append">
                <button type="submit" name="enviar" class="btn btn-primary btn-block"><i class="fas fa-arrow-right text-muted"></i></button>
                </div>
            </div>
        </form>
        <!-- /.lockscreen credentials -->
       <?php
            if (isset($_POST['enviar'])) {
                //$usuario = $_POST['usuario'] == 'admin';
                $clave = $_POST['contra'];

                if ($clave == '') {
                    echo "<p>falta dato</p>";
                } else {
                    $sql = "SELECT * FROM usuarios WHERE contra='$clave'";
                    $result = mysqli_query($cn, $sql);

                    if ($result && mysqli_num_rows($result) > 0) {
                        $row = mysqli_fetch_assoc($result);
                        $_SESSION['id_usuario'] = $row['id_usu'];
                        $_SESSION['usuario'] = 'admin';
                        //$_SESSION['tipo_usuario'] = $row['rol'];
                        header("Location: index.php");
                        exit();
                    } else {
                        echo "<p>Usuario o clave incorrecta</p>";
                    }
                }
            }
        ?> 
    </div>
    <!-- /.lockscreen-item -->
    <div class="help-block text-center">
        INGRESE SU CONTRASEÑA PARA INICIAR SESIÓN
    </div>
    <div class="text-center">
        <a href="login.php">O REGISTRESE SU ASISTENCIA COMO PRACTICANTE O TRABAJADOR</a>
    </div>
    <div class="lockscreen-footer text-center">
        Copyright © 2025-2025 <b><a href="#" class="text-black">BY ANGEL PROSPERO</a></b><br>
        TODOS LOS DERECHOS RESERVADOS
    </div>
    </div>
    <!-- /.center -->

    <!-- jQuery -->
    <script src="../../plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="../../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>


</body>
        
</html>

