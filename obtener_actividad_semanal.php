<?php
session_start();
require "conexion.php";

$id_usuario = $_SESSION["id_usuario"];

// Consulta: cuántos ejercicios agregados por día
$sql = "SELECT r.dia_semana, COUNT(d.id_detalle) AS ejercicios_realizados
        FROM rutinas r
        LEFT JOIN detalle_rutina d ON r.id_rutina = d.id_rutina
        WHERE r.id_usuario = ?
        GROUP BY r.dia_semana
        ORDER BY FIELD(r.dia_semana, 'Lunes','Martes','Miércoles','Jueves','Viernes','Sábado','Domingo')";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$result = $stmt->get_result();

$data = [];

while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);
?>
