<?php
session_start();
header("Content-Type: application/json");

if (!isset($_SESSION["id_usuario"])) {
    echo json_encode(["success" => false, "msg" => "Usuario no logueado"]);
    exit();
}

if (!isset($_POST["id_comida"]) || !isset($_POST["momento"])) {
    echo json_encode(["success" => false, "msg" => "Datos incompletos"]);
    exit();
}

$id_usuario = $_SESSION["id_usuario"];
$id_comida  = $_POST["id_comida"];
$momento    = $_POST["momento"];

$servername = "localhost";
$username   = "root";
$password   = "12345678";
$dbname     = "fitness_app";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    echo json_encode(["success" => false, "msg" => "Error de conexión"]);
    exit();
}

// Verificar si ya existe la comida para ese momento
$stmt = $conn->prepare("SELECT * FROM comidas_usuario WHERE id_usuario = ? AND id_comida = ? AND momento = ?");
$stmt->bind_param("iis", $id_usuario, $id_comida, $momento);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo json_encode(["success" => false, "msg" => "Ya agregaste esta comida en este momento"]);
    exit();
}

// Insertar comida
$stmt = $conn->prepare("INSERT INTO comidas_usuario (id_usuario, id_comida, fecha, momento) VALUES (?, ?, CURDATE(), ?)");
$stmt->bind_param("iis", $id_usuario, $id_comida, $momento);
if ($stmt->execute()) {
    echo json_encode(["success" => true, "msg" => "Comida agregada"]);
} else {
    echo json_encode(["success" => false, "msg" => "Error al agregar comida"]);
}