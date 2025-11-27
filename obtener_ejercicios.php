<?php
session_start();
if(!isset($_SESSION["id_usuario"])) exit();

$servername = "localhost";
$username = "root";
$password = "12345678";
$dbname = "fitness_app";
$conn = new mysqli($servername, $username, $password, $dbname);
if($conn->connect_error) die("Error: " . $conn->connect_error);

// Filtros
$cuerpo = isset($_GET['cuerpo']) ? $_GET['cuerpo'] : "";
$equipo = isset($_GET['equipo']) ? $_GET['equipo'] : "";
$search = isset($_GET['search']) ? $_GET['search'] : "";

$sql = "SELECT * FROM ejercicios WHERE 1=1";
if($cuerpo !== "" && $cuerpo !== "Todos") $sql .= " AND grupo_muscular='$cuerpo'";
if($equipo !== "" && $equipo !== "Todos") $sql .= " AND tipo_equipo='$equipo'";
if($search !== "") $sql .= " AND nombre LIKE '%$search%'";

$result = $conn->query($sql);
$ejercicios = [];
while($row = $result->fetch_assoc()) $ejercicios[] = $row;

echo json_encode($ejercicios);
?>
