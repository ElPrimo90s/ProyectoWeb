<?php
session_start();
include "conexion.php"; // conexión a tu base de datos

// Verifica que el usuario esté logeado
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.html");
    exit;
}

$id_usuario = $_SESSION['id_usuario'];

// ================================
// 1️⃣ RECIBIR DATOS DEL FORMULARIO
// ================================
$edad = $_POST['edad'];
$altura = $_POST['altura'];       // altura en metros
$peso_inicial = $_POST['peso_inicial'];
$sexo = $_POST['sexo'];

$peso_objetivo = $_POST['peso_objetivo'];
$tipo_meta = $_POST['tipo_meta'];             // ENUM: Bajar, Subir, Mantener
$dias_entrenamiento = $_POST['dias_entrenamiento'];

// ====================================================
// 2️⃣ GUARDAR DATOS BASE EN LA TABLA USUARIOS
// ====================================================
$update_usuario = $conn->prepare("
    UPDATE usuarios 
    SET edad=?, altura=?, peso_inicial=?, sexo=? 
    WHERE id_usuario=?
");

$update_usuario->bind_param(
    "iddsi",
    $edad,
    $altura,
    $peso_inicial,
    $sexo,
    $id_usuario
);

$update_usuario->execute();


// ====================================================
// 3️⃣ INSERTAR O ACTUALIZAR TABLA objetivos_usuario
// ====================================================

// Comprobamos si ya existe un registro de objetivos para este usuario
$check_obj = $conn->prepare("SELECT id_objetivo FROM objetivos_usuario WHERE id_usuario=?");
$check_obj->bind_param("i", $id_usuario);
$check_obj->execute();
$check_obj->store_result();

if ($check_obj->num_rows > 0) {
    // Ya existe → actualizar
    $update_obj = $conn->prepare("
        UPDATE objetivos_usuario 
        SET peso_objetivo=?, tipo_meta=?, dias_entrenamiento=?, fecha_objetivo=NOW()
        WHERE id_usuario=?
    ");

    $update_obj->bind_param(
        "isii",
        $peso_objetivo,
        $tipo_meta,
        $dias_entrenamiento,
        $id_usuario
    );

    $update_obj->execute();

} else {
    // No existe → insertar
    $insert_obj = $conn->prepare("
        INSERT INTO objetivos_usuario 
        (id_usuario, peso_objetivo, tipo_meta, dias_entrenamiento, fecha_objetivo)
        VALUES (?,?,?,?,NOW())
    ");

    $insert_obj->bind_param(
        "iisi",
        $id_usuario,
        $peso_objetivo,
        $tipo_meta,
        $dias_entrenamiento
    );

    $insert_obj->execute();
}


// ====================================================
// 4️⃣ CALCULAR IMC Y GUARDAR EN LA TABLA imc
// ====================================================

$altura_m = $altura / 100;
$imc = $peso_inicial / ($altura_m * $altura_m);

if ($imc < 18.5) {
    $categoria = "Bajo peso";
} elseif ($imc < 25) {
    $categoria = "Normal";
} elseif ($imc < 30) {
    $categoria = "Sobrepeso";
} else {
    $categoria = "Obesidad";
}

$insert_imc = $conn->prepare("
    INSERT INTO imc (id_usuario, imc_valor, categoria, fecha_calculo)
    VALUES (?,?,?,NOW())
");

$insert_imc->bind_param(
    "ids",
    $id_usuario,
    $imc,
    $categoria
);

$insert_imc->execute();


// ====================================================
// 5️⃣ REDIRECCIÓN FINAL AL PERFIL
// ====================================================
header("Location: perfil.php");
exit;
?>
