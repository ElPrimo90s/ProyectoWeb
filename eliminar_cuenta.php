<?php
session_start();

// Verificar que el usuario esté logueado
if (!isset($_SESSION["id_usuario"])) {
    header("Location: RegistroTest.html");
    exit();
}

$id_usuario = $_SESSION["id_usuario"];

// Conexión a la base de datos
$servername = "localhost";
$username   = "root";
$password   = "12345678";
$dbname     = "fitness_app";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Iniciar transacción para eliminar todo relacionado al usuario
$conn->begin_transaction();

try {
    // 1. Eliminar registros de progreso
    $conn->query("DELETE FROM progreso WHERE id_usuario = $id_usuario");
    
    // 2. Eliminar IMC
    $conn->query("DELETE FROM imc WHERE id_usuario = $id_usuario");
    
    // 3. Eliminar objetivos
    $conn->query("DELETE FROM objetivos_usuario WHERE id_usuario = $id_usuario");
    
    // 4. Eliminar comidas del usuario
    $conn->query("DELETE FROM comidas_usuario WHERE id_usuario = $id_usuario");
    
    // 5. Eliminar detalles de rutina (primero obtener las rutinas)
    $rutinas = $conn->query("SELECT id_rutina FROM rutinas WHERE id_usuario = $id_usuario");
    while ($rutina = $rutinas->fetch_assoc()) {
        $id_rutina = $rutina['id_rutina'];
        $conn->query("DELETE FROM detalle_rutina WHERE id_rutina = $id_rutina");
    }
    
    // 6. Eliminar rutinas
    $conn->query("DELETE FROM rutinas WHERE id_usuario = $id_usuario");
    
    // 7. Finalmente, eliminar el usuario
    $conn->query("DELETE FROM usuarios WHERE id_usuario = $id_usuario");
    
    // Confirmar transacción
    $conn->commit();
    
    // Destruir sesión
    session_destroy();
    
    // Cerrar conexión
    $conn->close();
    
    // Redirigir a página de inicio con mensaje
    header("Location: RegistroTest.html?mensaje=cuenta_eliminada");
    exit();
    
} catch (Exception $e) {
    // Si hay error, revertir cambios
    $conn->rollback();
    $conn->close();
    
    // Redirigir con error
    header("Location: perfil.php?error=no_se_pudo_eliminar");
    exit();
}
?>