<?php
session_start();
header("Content-Type: application/json");

// Verificar sesión
if (!isset($_SESSION["id_usuario"])) {
    echo json_encode([]);
    exit();
}

// Obtener el momento (Desayuno, Almuerzo, etc.)
$momento = isset($_GET["momento"]) ? $_GET["momento"] : "";
if (empty($momento)) {
    echo json_encode([]);
    exit();
}

$id_usuario = $_SESSION["id_usuario"];
$servername = "localhost";
$username   = "root";
$password   = "12345678";
$dbname     = "fitness_app";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    echo json_encode([]);
    exit();
}

// Consulta:
// 1. Selecciona el ID del registro (id_comida_usuario) y todos los datos de la comida (c.*)
// 2. Filtra por el id_usuario, la fecha de hoy (CURDATE()) y el momento (Desayuno/Almuerzo/etc.)
$sql = "SELECT cu.id_comida_usuario, c.* FROM comidas_usuario cu
        JOIN comidas c ON cu.id_comida = c.id_comida
        WHERE cu.id_usuario = ? 
        AND cu.momento = ? 
        AND cu.fecha = CURDATE()";

$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $id_usuario, $momento);
$stmt->execute();
$result = $stmt->get_result();

$comidas_agregadas = [];
while ($row = $result->fetch_assoc()) {
    $comidas_agregadas[] = $row;
}

echo json_encode($comidas_agregadas);

$stmt->close();
$conn->close();
?>