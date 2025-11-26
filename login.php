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

    $correo = isset($_POST['correo']) ? trim($_POST['correo']) : '';
    $contrasena = isset($_POST['contrasena']) ? $_POST['contrasena'] : '';

    if ($correo === '' || $contrasena === '') {
        mostrarAlerta("Campos Vacíos", "Por favor completa correo y contraseña.", "warning");
        exit();
    }

    // Consulta preparada
    $stmt = $conn->prepare("SELECT id_usuario, nombre, contrasena FROM usuarios WHERE correo = ?");
    if (!$stmt) {
        mostrarAlerta("Error", "Error en la consulta SQL.", "error");
        exit();
    }

    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows === 1) {

        $user = $result->fetch_assoc();

        // Verificar contraseña
        if (password_verify($contrasena, $user['contrasena'])) {

            // Guardar sesión
            $_SESSION['id_usuario'] = $user['id_usuario'];
            $_SESSION['nombre'] = $user['nombre'];
            $_SESSION['correo'] = $correo;

            // 🔥 Notificación de éxito
            mostrarAlerta("Bienvenido", "Inicio de sesión exitoso 😎🔥", "success", "perfil.php");
            exit();

        } else {
            mostrarAlerta("Error", "Contraseña incorrecta 😭", "error");
            exit();
        }

    } else {
        mostrarAlerta("Cuenta no encontrada", "No existe una cuenta con ese correo.", "error");
        exit();
    }

    $stmt->close();
}

$conn->close();


// ========================================
// 🔥 FUNCIÓN PARA MOSTRAR SWEETALERT2 🔥
// ========================================
function mostrarAlerta($titulo, $mensaje, $icono, $redirect = "RegistroTest.html") {
    echo "
    <html>
    <head>
        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    </head>
    <body>
        <script>
            Swal.fire({
                title: '$titulo',
                text: '$mensaje',
                icon: '$icono',
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.href = '$redirect';
            });
        </script>
    </body>
    </html>";
}
?>