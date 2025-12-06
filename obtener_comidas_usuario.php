<?php
session_start();
header("Content-Type: application/json");

// Habilitar errores para debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Verificar sesión
if (!isset($_SESSION["id_usuario"])) {
    echo json_encode(["error" => "No hay sesión activa"]);
    exit();
}

// Obtener el momento (Desayuno, Almuerzo, etc.)
$momento = isset($_GET["momento"]) ? trim($_GET["momento"]) : "";
if (empty($momento)) {
    echo json_encode(["error" => "Momento vacío"]);
    exit();
}

$id_usuario = $_SESSION["id_usuario"];
$servername = "localhost";
$username   = "root";
$password   = "12345678";
$dbname     = "fitness_app";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    echo json_encode(["error" => "Error de conexión: " . $conn->connect_error]);
    exit();
}

// Consulta mejorada con manejo de errores
$sql = "SELECT cu.id_registro, c.* 
        FROM comidas_usuario cu
        JOIN comidas c ON cu.id_comida = c.id_comida
        WHERE cu.id_usuario = ? 
        AND cu.momento = ? 
        AND cu.fecha = CURDATE()";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(["error" => "Error en prepare: " . $conn->error]);
    exit();
}

$stmt->bind_param("is", $id_usuario, $momento);
if (!$stmt->execute()) {
    echo json_encode(["error" => "Error en execute: " . $stmt->error]);
    exit();
}

$result = $stmt->get_result();
$comidas_agregadas = [];

while ($row = $result->fetch_assoc()) {
    $comidas_agregadas[] = $row;
}

echo json_encode($comidas_agregadas);

$stmt->close();
$conn->close();
?>