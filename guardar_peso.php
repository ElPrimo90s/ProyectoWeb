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

// ===============================================
// 🔒 1. Verificar si han pasado 7 días desde la última actualización
// ===============================================
$sql_last = "SELECT fecha_registro FROM progreso WHERE id_usuario = ? ORDER BY fecha_registro DESC LIMIT 1";
$stmt_last = $conn->prepare($sql_last);
$stmt_last->bind_param("i", $id_usuario);
$stmt_last->execute();
$result_last = $stmt_last->get_result();

if ($result_last->num_rows > 0) {
    $row = $result_last->fetch_assoc();
    $ultima_fecha = strtotime($row["fecha_registro"]);
    $una_semana = strtotime("+7 days", $ultima_fecha);

    if (time() < $una_semana) {
        echo "<script>
            alert('Todavía no han pasado 7 días desde tu última actualización de peso 😭🙏🔥');
            window.location.href = 'perfil.php';
        </script>";
        exit();
    }
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