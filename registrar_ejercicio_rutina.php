<?php
session_start();
header('Content-Type: application/json');
if(!isset($_SESSION["id_usuario"])) exit(json_encode(["success"=>false,"msg"=>"Usuario no logueado"]));

$usuario = $_SESSION["id_usuario"];
$id_ejercicio = $_POST['id_ejercicio'];
$dia = $_POST['dia'];

$servername = "localhost";
$username = "root";
$password = "12345678";
$dbname = "fitness_app";
$conn = new mysqli($servername, $username, $password, $dbname);
if($conn->connect_error) exit(json_encode(["success"=>false,"msg"=>"Error DB"]));

// Buscar o crear rutina del día
$sql = "SELECT id_rutina FROM rutinas WHERE id_usuario='$usuario' AND dia_semana='$dia'";
$result = $conn->query($sql);

if($result->num_rows > 0){
    $row = $result->fetch_assoc();
    $id_rutina = $row['id_rutina'];
} else {
    $fecha = date("Y-m-d");
    $conn->query("INSERT INTO rutinas (id_usuario,dia_semana,fecha_creacion) VALUES ('$usuario','$dia','$fecha')");
    $id_rutina = $conn->insert_id;
}

// Insertar detalle_rutina con valores por defecto
$conn->query("INSERT INTO detalle_rutina (id_rutina,id_ejercicio,series,repeticiones) VALUES ('$id_rutina','$id_ejercicio',3,12)");

echo json_encode(["success"=>true]);
?>
