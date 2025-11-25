<?php
session_start();
if (!isset($_SESSION["id_usuario"])) {
    header("Location: RegistroTest.html");
    exit();
}

$id_usuario = $_SESSION["id_usuario"];
$peso_actual = $_POST["peso_actual"];
$fecha_registro = date("Y-m-d H:i:s"); // fecha actual

// Conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "12345678";
$dbname = "fitness_app";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Insertar nuevo peso en la tabla progreso
$sql = "INSERT INTO progreso (id_usuario, fecha_registro, peso_actual) 
        VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("isd", $id_usuario, $fecha_registro, $peso_actual);

if ($stmt->execute()) {
    // Opcional: actualizar también peso_actual en tabla usuarios
    $sql2 = "UPDATE usuarios SET peso_inicial = ? WHERE id_usuario = ?";
    $stmt2 = $conn->prepare($sql2);
    $stmt2->bind_param("di", $peso_actual, $id_usuario);
    $stmt2->execute();

    header("Location: perfil.php"); // Volver al perfil
    exit();
} else {
    echo "Error al guardar peso: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>