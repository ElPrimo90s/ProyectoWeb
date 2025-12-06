<?php
session_start();
header("Content-Type: application/json");

// Verificar sesión
if (!isset($_SESSION["id_usuario"])) {
    echo json_encode(["success" => false, "msg" => "Usuario no logueado"]);
    exit();
}

// Verificar que se envió el ID
if (!isset($_POST["id"])) {
    echo json_encode(["success" => false, "msg" => "ID no proporcionado"]);
    exit();
}

$id_usuario = $_SESSION["id_usuario"];
$id_registro = $_POST["id"];

$servername = "localhost";
$username   = "root";
$password   = "12345678";
$dbname     = "fitness_app";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    echo json_encode(["success" => false, "msg" => "Error de conexión"]);
    exit();
}

// Eliminar solo si pertenece al usuario (seguridad)
$stmt = $conn->prepare("DELETE FROM comidas_usuario WHERE id_registro = ? AND id_usuario = ?");
$stmt->bind_param("ii", $id_registro, $id_usuario);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo json_encode(["success" => true, "msg" => "Comida eliminada correctamente"]);
    } else {
        echo json_encode(["success" => false, "msg" => "No se encontró la comida o no tienes permiso"]);
    }
} else {
    echo json_encode(["success" => false, "msg" => "Error al eliminar: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>