<?php
session_start();
require "conexion.php";

$id_usuario = $_SESSION["id_usuario"];

// Comidas del usuario + macros reales
$sql = "SELECT 
            SUM(c.proteinas) AS total_proteinas,
            SUM(c.carbohidratos) AS total_carbohidratos,
            SUM(c.grasas) AS total_grasas
        FROM comidas_usuario cu
        INNER JOIN comidas c ON cu.id_comida = c.id_comida
        WHERE cu.id_usuario = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

echo json_encode($data);
?>
