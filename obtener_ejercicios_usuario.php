<?php
session_start();
if(!isset($_SESSION["id_usuario"])) exit();

$usuario = $_SESSION["id_usuario"];
$dia = isset($_GET['dia']) ? $_GET['dia'] : "Lunes";

$servername = "localhost";
$username = "root";
$password = "12345678";
$dbname = "fitness_app";
$conn = new mysqli($servername, $username, $password, $dbname);
if($conn->connect_error) die("Error: " . $conn->connect_error);

$sql = "SELECT dr.id_detalle, e.nombre, e.grupo_muscular, e.tipo_equipo, dr.series, dr.repeticiones, r.dia_semana
        FROM detalle_rutina dr
        JOIN rutinas r ON dr.id_rutina=r.id_rutina
        JOIN ejercicios e ON dr.id_ejercicio=e.id_ejercicio
        WHERE r.id_usuario='$usuario' AND r.dia_semana='$dia'";
        
$result = $conn->query($sql);
$ejercicios = [];
while($row = $result->fetch_assoc()) $ejercicios[] = $row;

echo json_encode($ejercicios);
?>
