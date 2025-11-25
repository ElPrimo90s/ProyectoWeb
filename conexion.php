<?php
$servername = "localhost";
$username = "root"; // por defecto
$password = "12345678"; // vacío en AppServ por defecto
$dbname = "fitness_app";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}
// echo "Conexión exitosa";
?>
