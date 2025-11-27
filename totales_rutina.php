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

$sql = "SELECT COUNT(*) as total_ejercicios, SUM(series) as total_series, SUM(repeticiones) as total_repeticiones
        FROM detalle_rutina dr
        JOIN rutinas r ON dr.id_rutina=r.id_rutina
        WHERE r.id_usuario='$usuario' AND r.dia_semana='$dia'";

$result = $conn->query($sql);
$data = $result->fetch_assoc();

echo json_encode([
    "total_ejercicios" => $data['total_ejercicios'] ?: 0,
    "total_series" => $data['total_series'] ?: 0,
    "total_repeticiones" => $data['total_repeticiones'] ?: 0
]);
?>
