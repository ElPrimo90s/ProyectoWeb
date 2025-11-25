<?php
// ============================
// CONFIGURACIÓN DE CONEXIÓN
// ============================
$servername = "localhost";
$username = "root";        // Cambiar si tu usuario es diferente
$password = "12345678";    // Cambiar tu contraseña
$dbname = "fitness_app";   // Cambiar al nombre de tu BD

$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// ============================
// PROCESAR FORMULARIO
// ============================
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email       = $_POST["email"];
    $user_name   = $_POST["username"];
    $pass        = $_POST["password"];
    $pass2       = $_POST["confirm-password"];

    // 1️⃣ Validar que coincidan las contraseñas
    if ($pass !== $pass2) {
        die("<h3>Las contraseñas no coinciden</h3>");
    }

    // 2️⃣ Validar que el correo no esté registrado
    $query = "SELECT id_usuario FROM usuarios WHERE correo = '$email'";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        die("<h3>El correo ya está registrado</h3>");
    }

    // 3️⃣ Encriptar contraseña
    $pass_hashed = password_hash($pass, PASSWORD_DEFAULT);

    // 4️⃣ Insertar en BD
    $sql = "INSERT INTO usuarios (nombre, correo, contrasena)
            VALUES ('$user_name', '$email', '$pass_hashed')";

    if ($conn->query($sql) === TRUE) {

        // 5️⃣ Iniciar sesión con el usuario recién creado
        session_start();
        $id_usuario = $conn->insert_id; // ID del nuevo usuario
        $_SESSION['id_usuario'] = $id_usuario;
        $_SESSION['nombre']     = $user_name;
        $_SESSION['correo']     = $email;

        // 6️⃣ Redirigir al perfil
        header("Location: perfil.php");
        exit();
    } else {
        echo "Error al crear cuenta: " . $conn->error;
    }
}

$conn->close();
?>