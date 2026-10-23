
<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require './conexion.php';

if (isset($_SESSION['tipo_usuario'])) {
    $tipo_usuario = $_SESSION['tipo_usuario'];
} else {
    $tipo_usuario = 'Admin';
}
?>
<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta http-equiv="x-ua-compatible" content="ie=edge">

        <title>SISTEM. ASISTENCIAS</title>

        <!-- Font Awesome Icons -->
        <link rel="stylesheet" href="css/all.min.css">
        <link rel="stylesheet" href="css/dataTables.bootstrap4.min.css">
        <link rel="stylesheet" href="css/responsive.bootstrap4.min.css">
        <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">
        <link rel="stylesheet" href="css/adminlte.min.css">
        
        <!-- Theme style -->
        <link rel="stylesheet" href="css/adminlte.min.css">
        <!-- Google Font: Source Sans Pro -->
        <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
    </head>
    <body class="hold-transition sidebar-mini">
        <div class="wrapper">

            <!-- Navbar -->
            <nav class="main-header navbar navbar-expand navbar-dark navbar-lightblue">
                <!-- Left navbar links -->
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
                    </li>
                    <li class="nav-item d-none d-sm-inline-block">
                        <a href="index.php" class="nav-link">Home</a>
                    </li>
                </ul>

                <!-- Right navbar links -->
                <ul class="navbar-nav ml-auto">     
                    <li class="nav-item">
                        <a class="btn btn-primary"  data-slide="true" href="login.php" role="button">
                           <i class="fa-solid fa-right-from-bracket"></i>Cerrar Sesión</a>
                    </li>
                </ul>
            </nav>
            <!-- /.navbar -->

            <!-- Main Sidebar Container -->
            <aside class="main-sidebar sidebar-dark-primary elevation-4">
                <!-- Brand Logo -->
                <a href="index.php" class="brand-link navbar-lightblue">
                    <img src="img/LOGO-UNI.png" alt="UNI" class="brand-image img-circle elevation-3" style="opacity: .8" _mstalt="183703" _msthash="27">
                    <span class="brand-text font-weight-light" _msttexthash="107003" _msthash="28" style="color: white">SISTEM. ASISTENCIAS</span>
                </a>

                <!-- Sidebar -->
                <div class="sidebar">
                    <!-- Sidebar user panel (optional) -->
                    <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                        <div class="image">
                            <img src="img/usuario.png" class="img-circle elevation-2" alt="Imagen de usuario" _mstalt="128609" _msthash="29">
                        </div>
                        <div class="info">
                            <a href="#" class="d-block"><?php echo $tipo_usuario; ?></a>
                        </div>
                    </div>

                    <!-- Sidebar Menu -->
                    <nav class="mt-2">
                        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                            <!-- Add icons to the links using the .nav-icon class
                                 with font-awesome or any other icon font library -->
                            <?php if ($tipo_usuario == 'Admin'): ?>
                                <li class="nav-item has-treeview">
                                    <a href="#" class="nav-link">
                                        <i class="nav-icon fas fa-table"></i>
                                        <p>
                                            REGISTRO ASISTENCIAS
                                            <i class="fas fa-angle-left right"></i>
                                        </p>
                                    </a>
                                    <ul class="nav nav-treeview" style="display: none;">
                                        <li class="nav-item">
                                            <a href="regist_asist_hoy.php" class="nav-link">
                                                <i class="nav-icon far fa-circle text-info"></i>
                                                <p>Asistencias Hoy</p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="regist_asist.php" class="nav-link">
                                                <i class="nav-icon far fa-circle text-info"></i>
                                                <p>Asistencias General</p>
                                            </a>
                                        </li>
                                        <!--
                                        <li class="nav-item">
                                            <a href="estudiantes_add.php" class="nav-link">
                                                <i class="nav-icon far fa-circle text-info"></i>
                                                <p>Agregar Usuarios</p>
                                            </a>
                                        </li>
                                       -->
                                    </ul>
                                </li>
                                <li class="nav-item has-treeview">
                                    <a href="#" class="nav-link">
                                        <i class="nav-icon fas fa-table"></i>
                                        <p>
                                            Usuarios
                                            <i class="fas fa-angle-left right"></i>
                                        </p>
                                    </a>
                                    <ul class="nav nav-treeview" style="display: none;">
                                        <li class="nav-item">
                                            <a href="usuarios.php" class="nav-link">
                                                <i class="nav-icon far fa-circle text-info"></i>
                                                <p>Administrar Usuarios</p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="usuarios_add.php" class="nav-link">
                                                <i class="nav-icon far fa-circle text-info"></i>
                                                <p>Agregar Usuarios</p>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                                <li class="nav-item has-treeview">
                                    <a href="#" class="nav-link">
                                        <i class="nav-icon fas fa-table"></i>
                                        <p>
                                            Areas
                                            <i class="fas fa-angle-left right"></i>
                                        </p>
                                    </a>
                                    <ul class="nav nav-treeview" style="display: none;">
                                        <li class="nav-item">
                                            <a href="areas.php" class="nav-link">
                                                <i class="nav-icon far fa-circle text-info"></i>
                                                <p>Administrar Areas</p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="areas_add.php" class="nav-link">
                                                <i class="nav-icon far fa-circle text-info"></i>
                                                <p>Agregar areas</p>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                                <li class="nav-item has-treeview">
                                    <a href="#" class="nav-link menu-open">
                                        <i class="nav-icon fas fa-table"></i>
                                        <p>
                                            Miembros
                                            <i class="fas fa-angle-left right"></i>
                                        </p>
                                    </a>
                                    <ul class="nav nav-treeview" style="display: none;">
                                        <li class="nav-item">
                                            <a href="miembros.php" class="nav-link">
                                                <i class="nav-icon far fa-circle text-info"></i>
                                                <p>Administrar Miembros</p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="miembros_add.php" class="nav-link">
                                                <i class="nav-icon far fa-circle text-info"></i>
                                                <p>Agregar Miembros</p>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                                <li class="nav-item has-treeview">
                                    <a href="#" class="nav-link">
                                        <i class="nav-icon far fa-calendar-alt"></i>
                                        <p>
                                            Horarios
                                            <i class="fas fa-angle-left right"></i>
                                        </p>
                                    </a>
                                    <ul class="nav nav-treeview" style="display: none;">
                                        <li class="nav-item">
                                            <a href="horario.php" class="nav-link">
                                                <i class="nav-icon far fa-circle text-info"></i>
                                                <p>Administrar Horarios</p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="horario_add.php" class="nav-link">
                                                <i class="nav-icon far fa-circle text-info"></i>
                                                <p>Agregar Horarios</p>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                                <!--
                                <li class="nav-item">
                                    <a href="Busqueda.php" class="nav-link">
                                        <i class="fas fa-search"></i>
                                        <p>
                                            Busqueda
                                        </p>
                                    </a>
                                </li>
                            -->   
                            <?php endif; ?>
                            <?php if ($tipo_usuario == 'Estudiante'): ?>
                                <li class="nav-item">
                                    <a href="#" class="nav-link">
                                        <i class="nav-icon fas fa-th"></i>
                                        <p>
                                            Estado de Solicitudes 
                                            <i class="fas fa-angle-left right"></i>
                                        </p>
                                    </a>
                                    <ul class="nav nav-treeview" style="display: none;">
                                        <li class="nav-item">
                                            <a href="Solicitud_info.php" class="nav-link">
                                                <i class="far fa-circle nav-icon"></i>
                                                <p>Estado Solicitud</p>
                                            </a>
                                        </li>
                                         <li class="nav-item">
                                            <a href="Solicitud_add.php" class="nav-link">
                                                <i class="far fa-circle nav-icon"></i>
                                                <p>Nueva Solicitud</p>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                                <li class="nav-item">
                                    <a href="#" class="nav-link">
                                        <i class="nav-icon fas fa-th"></i>
                                        <p>
                                           Mi Perfil
                                           <i class="fas fa-angle-left right"></i>
                                        </p>
                                    </a> 
                                    <ul class="nav nav-treeview" style="display: none;">
                                        <li class="nav-item">
                                            <a href="verperfil.php" class="nav-link">
                                                <i class="far fa-circle nav-icon"></i>
                                                <p>Ver Perfil</p>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                    <!-- /.sidebar-menu -->
                </div>
                <!-- /.sidebar -->
            </aside>
