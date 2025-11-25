<?php
session_start();

if (!isset($_SESSION["id_usuario"])) {
    header("Location: RegistroTest.html");
    exit();
}

// Obtener datos del usuario desde la sesión
$nombre = $_SESSION["nombre"];
$correo = $_SESSION["correo"];
$id     = $_SESSION["id_usuario"];

// ----------------- CONEXIÓN A BD -----------------
$servername = "localhost";
$username   = "root";
$password   = "12345678";
$dbname     = "fitness_app";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ejercicios</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="estilos.css">
    <script src="script.js"></script>
</head>
<body>

    <div class="header">
        <div class="logo">Fitness</div>
        <div class="user-info">
            Hola, <?php echo htmlspecialchars($nombre); ?>
            <a href="logout.php" class="logout">Salir &rarr;</a>
        </div>
    </div>

    <div class="nav-tabs">
        <a href="#" class="nav-link active"><i class="fas fa-dumbbell"></i> Ejercicios</a>
        <a href="comidas.php" class="nav-link"><i class="fas fa-utensils"></i> Comidas</a>
        <a href="perfil.php" class="nav-link"><i class="fas fa-user"></i> Perfil</a>
    </div>

    <div class="dashboard-container">
        
        <div class="joji-card routine-plan">
            <h3>Rutina Semanal</h3>
            <p>Organiza tus ejercicios por día de la semana</p>

            <div class="macro-ex-grid">
                <div class="macro-ex-card ejercicios"><i class="fas fa-running"></i> Ejercicios <strong>0</strong></div>
                <div class="macro-ex-card duracion"><i class="far fa-clock"></i> Duración <strong>0 min</strong></div>
                <div class="macro-ex-card calorias"><i class="fas fa-fire"></i> Calorías <strong>0 cal</strong></div>
                <div class="macro-ex-card sugerencias"><div><i class="fas fa-lightbulb"></i> Sugerencias</div><strong>0</strong></div>
            </div>
        </div>

        <div class="day-tabs">
            <div class="day-list" id="day-tab-list">
                <button class="day-tab active" data-day="Lunes"><span class="count">0</span> Lun</button>
                <button class="day-tab" data-day="Martes"><span class="count">0</span> Mar</button>
                <button class="day-tab" data-day="Miércoles"><span class="count">0</span> Mié</button>
                <button class="day-tab" data-day="Jueves"><span class="count">0</span> Jue</button>
                <button class="day-tab" data-day="Viernes"><span class="count">0</span> Vie</button>
                <button class="day-tab" data-day="Sábado"><span class="count">0</span> Sáb</button>
                <button class="day-tab" data-day="Domingo"><span class="count">0</span> Dom</button>
            </div>
            
            <button class="add-ex-btn" id="add-ex-btn">+ Agregar Ejercicio</button>
        </div>

        <div class="joji-card day-content">
            <h4 id="day-title">Ejercicios de Lunes</h4>
            
            <div class="empty-ex-state">
                <i class="fas fa-dumbbell"></i>
                <p>No hay ejercicios programados</p>
                <p style="margin-top: 5px; color: #aaa;">Agrega ejercicios para comenzar tu rutina de <span id="empty-day-text">Lunes</span></p>
                <button class="empty-add-btn" id="empty-add-ex-btn">+ Agregar primer ejercicio</button>
            </div>
        </div>
        
        <div class="suggestions-section">
            <div class="suggestions-header">
                <i class="fas fa-venus"></i>
                <div>
                    <h4>Ejercicios Sugeridos para <span id="suggest-day-text">Lunes</span></h4>
                    <p>Ejercicios recomendados que complementan tu rutina actual</p>
                </div>
            </div>

            <div class="suggestion-empty-state">
                <i class="fas fa-robot"></i>
                <h5>Sin Sugerencias Disponibles</h5>
                <p>Aún no tenemos suficientes datos o la funcionalidad de recomendaciones no está conectada.</p>
            </div>
        </div>
        
    </div>
    
    <div class="modal-overlay" id="add-exercise-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modal-title">Agregar ejercicio a Lunes</h3>
                <button class="close-btn" id="close-modal-btn">&times;</button>
            </div>
            
            <div class="search-bar">
                <i class="fas fa-search"></i>
                <input type="text" placeholder="Buscar ejercicio (ej: Press Banca, Sentadillas...)">
            </div>
            
            <div class="filter-section">
                <h4>Tipo de Equipo</h4>
                <div class="filter-buttons">
                    <button class="filter-btn active">Todos</button>
                    <button class="filter-btn"><i class="fas fa-home"></i> Casa</button>
                    <button class="filter-btn"><i class="fas fa-cog"></i> Máquinas</button>
                    <button class="filter-btn"><i class="fas fa-arrows-alt-v"></i> Poleas</button>
                    <button class="filter-btn"><i class="fas fa-weight-hanging"></i> Peso Libre</button>
                    <button class="filter-btn"><i class="fas fa-heartbeat"></i> Cardio</button>
                </div>
            </div>
            
            <div class="filter-section">
                <h4>Parte del Cuerpo</h4>
                <div class="filter-buttons">
                    <button class="filter-btn active">Todos</button>
                    <button class="filter-btn">Piernas</button>
                    <button class="filter-btn">Pecho</button>
                    <button class="filter-btn">Core</button>
                    <button class="filter-btn">Cardio</button>
                    <button class="filter-btn">Brazos</button>
                    <button class="filter-btn">Espalda</button>
                    <button class="filter-btn">Hombros</button>
                </div>
            </div>
            
            <div class="results-container">
                <p class="results-header">0 ejercicios encontrados</p>
                <div class="results-grid">
                    <div class="empty-search-results">
                        <i class="fas fa-search-minus"></i>
                        <h4>No se encontraron ejercicios</h4>
                        <p>Intenta ajustar tus filtros o busca por un nombre diferente.</p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</body>
</html>