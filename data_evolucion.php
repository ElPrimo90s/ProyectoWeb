<?php
session_start();
if (!isset($_SESSION["id_usuario"])) {
    http_response_code(403);
    echo json_encode([]);
    exit();
}

$id = $_SESSION["id_usuario"];

// Conexión
$conn = new mysqli("localhost", "root", "12345678", "fitness_app");
if ($conn->connect_error) die(json_encode([]));

// Obtener registros de peso del usuario
$sql = "SELECT fecha_registro, peso_actual FROM progreso WHERE id_usuario = $id ORDER BY fecha_registro ASC";
$result = $conn->query($sql);

$fechas = [];
$pesos  = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $fechas[] = $row["fecha_registro"];
        $pesos[]  = (float)$row["peso_actual"];
    }
}

$conn->close();

// Devolver JSON
echo json_encode([
    "fechas" => $fechas,
    "pesos" => $pesos
]);
?>