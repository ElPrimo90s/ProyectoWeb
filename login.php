<?php
session_start();

// Configuración de conexión
$servername = "localhost";
$db_user = "root";
$db_pass = "12345678";
$dbname = "fitness_app";

$conn = new mysqli($servername, $db_user, $db_pass, $dbname);
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Tomar datos del formulario (usando los nombres que hay en la BD: correo, contrasena)
    $correo = isset($_POST['correo']) ? trim($_POST['correo']) : '';
    $contrasena = isset($_POST['contrasena']) ? $_POST['contrasena'] : '';

    if ($correo === '' || $contrasena === '') {
        // Mensaje corto y claro (puedes cambiar por redirección con GET error)
        die("Por favor completa correo y contraseña.");
    }

    // Preparar la consulta para evitar SQL Injection
    $stmt = $conn->prepare("SELECT id_usuario, nombre, contrasena FROM usuarios WHERE correo = ?");
    if (!$stmt) {
        die("Error en la consulta: " . $conn->error);
    }
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Verificar contraseña (la contraseña en la BD debe haber sido guardada con password_hash)
        if (password_verify($contrasena, $user['contrasena'])) {
            // Inicio de sesión exitoso
            $_SESSION['id_usuario'] = $user['id_usuario'];
            $_SESSION['nombre'] = $user['nombre'];
            $_SESSION['correo'] = $correo;

            // Redirigir (temporalmente a perfil.html si aún no es PHP)
            header("Location: perfil.php");
            exit();
        } else {
            // Contraseña incorrecta
            echo "Contraseña incorrecta.";
        }
    } else {
        // No existe el correo
        echo "No existe una cuenta con ese correo.";
    }

    $stmt->close();
}

$conn->close();
?>