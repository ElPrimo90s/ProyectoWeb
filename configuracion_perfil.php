<?php
session_start();

// Si no hay sesión activa, redirigir al login o registro
if (!isset($_SESSION["id_usuario"])) {
    header("Location: RegistroTest.html"); // o Register.html según prefieras
    exit();
}

// Opcional: obtener datos del usuario para mostrar
$nombre = $_SESSION["nombre"];
$correo = $_SESSION["correo"];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Config del Perfil</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Montserrat:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="configperfil.css">
</head>
<body>
    <div class="logo-text">Fitness</div>

    <div class="form-card">
        <h3>Completar Datos del Perfil</h3>
        
        <form action="guardar_perfil.php" method="POST"> 
            
    <div class="input-group">
        <label for="edad">Edad:</label>
        <div class="input-full">
            <input type="number" id="edad" name="edad" placeholder="Ej: 25" required min="10" max="100">
        </div>
    </div>
    
    <div class="input-group">
        <label for="sexo">Género:</label>
        <div class="input-full">
            <select id="sexo" name="sexo" required>
                <option value="" disabled selected>Selecciona tu género</option>
                <option value="F">Femenino</option>
                <option value="M">Masculino</option>
                <option value="Otro">Otro / Prefiero no decir</option>
            </select>
        </div>
    </div>
    
    <div class="input-group">
        <label for="peso_inicial">Peso Actual:</label>
        <div class="input-with-unit">
            <input type="number" id="peso_inicial" name="peso_inicial" placeholder="Ej: 75.5" step="0.1" required>
            <span class="input-unit">kg</span>
        </div>
    </div>

    <div class="input-group">
        <label for="altura">Altura:</label>
        <div class="input-with-unit">
            <input type="number" id="altura" name="altura" placeholder="Ej: 175" required>
            <span class="input-unit">cm</span>
        </div>
    </div>

    <div class="input-group">
        <label for="peso_objetivo">Peso Objetivo:</label>
        <div class="input-with-unit">
            <input type="number" id="peso_objetivo" name="peso_objetivo" placeholder="Ej: 68" step="0.1" required>
            <span class="input-unit">kg</span>
        </div>
    </div>

    <div class="input-group">
        <label for="tipo_meta">Tipo de Meta:</label>
        <div class="input-full">
            <select id="tipo_meta" name="tipo_meta" required>
                <option value="" disabled selected>Selecciona tu meta</option>
                <option value="Bajar">Bajar de peso</option>
                <option value="Subir">Subir de peso</option>
                <option value="Mantener">Mantener peso</option>
            </select>
        </div>
    </div>

    <div class="input-group">
        <label for="dias_entrenamiento">Días de entrenamiento por semana:</label>
        <div class="input-full">
            <input type="number" id="dias_entrenamiento" name="dias_entrenamiento" placeholder="Ej: 4" required min="1" max="7">
        </div>
    </div>

    <div class="button-group">
        <input type="submit" value="Guardar Datos" class="save-btn">
        <a href="perfil.php" class="back-btn">Cancelar</a>
    </div>

</form>
    </div>
</body>
</html>