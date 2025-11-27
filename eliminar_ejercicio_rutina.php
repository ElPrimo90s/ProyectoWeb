<?php
session_start();
header('Content-Type: application/json');
if(!isset($_SESSION["id_usuario"])) exit(json_encode(["success"=>false,"msg"=>"Usuario no logueado"]));

$id = $_POST['id'];

$servername = "localhost";
$username = "root";
$password = "12345678";
$dbname = "fitness_app";
$conn = new mysqli($servername, $username, $password, $dbname);
if($conn->connect_error) exit(json_encode(["success"=>false,"msg"=>"Error DB"]));

$conn->query("DELETE FROM detalle_rutina WHERE id_detalle='$id'");
echo json_encode(["success"=>true]);
?>
