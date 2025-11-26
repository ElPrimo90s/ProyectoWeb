<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// =====================================
// FUNCIÓN UNIVERSAL PARA NOTIFICACIONES
// =====================================
function noti($tipo, $mensaje, $redirect = null) {
    echo "
    <!DOCTYPE html>
    <html lang='es'>
    <head>
        <meta charset='UTF-8'>
        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    </head>
    <body>
    <script>
        Swal.fire({
            icon: '$tipo',
            text: '$mensaje',
        }).then(() => {
            " . ($redirect ? "window.location.href='$redirect';" : "window.history.back();") . "
        });
    </script>
    </body>
    </html>";
    exit();
}

// ============================
// CONFIGURACIÓN DE CONEXIÓN
// ============================
$servername = "localhost";
$username = "root";
$password = "12345678";
$dbname   = "fitness_app";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    noti("error", "Error de conexión con la base de datos.");
}

// ============================
// PROCESAR FORMULARIO
// ============================
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email     = trim($_POST["email"]);
    $user_name = trim($_POST["username"]);
    $pass      = $_POST["password"];
    $pass2     = $_POST["confirm-password"];

    // 1. Validar correo sintáctico
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        noti("error", "Correo inválido.");
    }

    // 2. Validar dominio MX o A (compatibilidad con Windows/XAMPP)
    $dominio = substr(strrchr($email, "@"), 1);

    if (!checkdnsrr($dominio, "MX") && !checkdnsrr($dominio, "A")) {
        noti("error", "El dominio del correo no existe o no recibe correos.");
    }

    // 3. Validar que las contraseñas coincidan
    if ($pass !== $pass2) {
        noti("error", "Las contraseñas no coinciden.");
    }

    // 4. Validar fuerza mínima
    $mayus   = preg_match('@[A-Z]@', $pass);
    $minus   = preg_match('@[a-z]@', $pass);
    $num     = preg_match('@[0-9]@', $pass);
    $special = preg_match('@[\W]@', $pass);

    if (!$mayus || !$minus || !$num || !$special || strlen($pass) < 8) {
        noti("error", "Contraseña débil. Requiere: 8+ caracteres, mayúscula, minúscula, número y símbolo.");
    }

    // 5. Validar que el correo NO esté registrado
    $stmt = $conn->prepare("SELECT id_usuario FROM usuarios WHERE correo = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $check = $stmt->get_result();

    if ($check->num_rows > 0) {
        noti("error", "El correo ya está registrado.");
    }

    // 6. Encriptar contraseña
    $pass_hashed = password_hash($pass, PASSWORD_DEFAULT);

    // 7. Insertar usuario en la BD
    $stmt2 = $conn->prepare("INSERT INTO usuarios (nombre, correo, contrasena) VALUES (?, ?, ?)");
    $stmt2->bind_param("sss", $user_name, $email, $pass_hashed);

    if ($stmt2->execute()) {

        // Iniciar sesión automáticamente
        session_start();
        $_SESSION['id_usuario'] = $stmt2->insert_id;
        $_SESSION['nombre']     = $user_name;
        $_SESSION['correo']     = $email;

        noti("success", "Cuenta creada con éxito 🎉", "perfil.php");

    } else {
        noti("error", "Error al crear la cuenta. Intenta más tarde.");
    }
}

$conn->close();
?>