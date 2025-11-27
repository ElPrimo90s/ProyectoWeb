<?php
session_start();
header("Content-Type: application/json");

if (!isset($_SESSION["id_usuario"])) {
    echo json_encode([]);
    exit();
}

$servername = "localhost";
$username   = "root";
$password   = "12345678";
$dbname     = "fitness_app";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    echo json_encode([]);
    exit();
}

// Filtrar por etiqueta si se envía (Balanceado, Vegetariano, etc.)
$etiqueta = isset($_GET['etiqueta']) ? $_GET['etiqueta'] : "";

if ($etiqueta) {
    $stmt = $conn->prepare("SELECT * FROM comidas WHERE etiqueta = ?");
    $stmt->bind_param("s", $etiqueta);
} else {
    $stmt = $conn->prepare("SELECT * FROM comidas");
}

$stmt->execute();
$result = $stmt->get_result();
$comidas = [];
while ($row = $result->fetch_assoc()) {
    $comidas[] = $row;
}

echo json_encode($comidas);