<?php
$host = 'localhost';
$user = 'root';
$pass = ''; // pon aquí la contraseña real si tiene
$db   = 'proyectoasist';
$port = '3306'; // cambia segun tu puerto de la base de datos 3306 o 3307 o ¿?
$cn = mysqli_connect($host, $user, $pass, $db, $port);

if (!$cn) {
    die("Error en la conexión: " . mysqli_connect_error());
}
