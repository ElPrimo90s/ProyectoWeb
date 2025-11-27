<?php
session_start();
header("Content-Type: application/json");

if (!isset($_SESSION["id_usuario"]) || !isset($_GET["momento"])) {
    echo json_encode(["calorias"=>0,"proteinas"=>0,"carbohidratos"=>0,"grasas"=>0]);
    exit();
}

$id_usuario = $_SESSION["id_usuario"];
$momento    = $_GET["momento"];

$servername = "localhost";
$username   = "root";
$password   = "12345678";
$dbname     = "fitness_app";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    echo json_encode(["calorias"=>0,"proteinas"=>0,"carbohidratos"=>0,"grasas"=>0]);
    exit();
}

$query = "SELECT SUM(c.calorias) AS calorias, SUM(c.proteinas) AS proteinas, 
          SUM(c.carbohidratos) AS carbohidratos, SUM(c.grasas) AS grasas
          FROM comidas_usuario cu
          JOIN comidas c ON cu.id_comida = c.id_comida
          WHERE cu.id_usuario = ? AND cu.momento = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("is", $id_usuario, $momento);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

echo json_encode([
    "calorias" => $data["calorias"] ?? 0,
    "proteinas" => $data["proteinas"] ?? 0,
    "carbohidratos" => $data["carbohidratos"] ?? 0,
    "grasas" => $data["grasas"] ?? 0
]);