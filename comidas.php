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
    <title>Comidas</title>
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
        <a href="ejercicios.php" class="nav-link"><i class="fas fa-dumbbell"></i> Ejercicios</a>
        <a href="#" class="nav-link active"><i class="fas fa-utensils"></i> Comidas</a>
        <a href="perfil.php" class="nav-link"><i class="fas fa-user"></i> Perfil</a>
    </div>



    <div class="dashboard-container">

<div class="plan-nutricional">
  <h3>Plan Nutricional de Hoy</h3>
  <p>Selecciona tus comidas para alcanzar tus objetivos nutricionales</p>

  <div class="nutri-container">
    <div class="nutri-card calorias">
      <span><i class="fa-solid fa-fire"></i> Calorías</span>
      <span class="value">0 kcal</span>
    </div>

    <div class="nutri-card proteina">
      <span><i class="fa-solid fa-seedling"></i> Proteína</span>
      <span class="value">0 g</span>
    </div>

    <div class="nutri-card carbohidratos">
      <span><i class="fa-solid fa-bread-slice"></i> Carbohidratos</span>
      <span class="value">0 g</span>
    </div>

    <div class="nutri-card grasas">
      <span><i class="fa-solid fa-droplet"></i> Grasas</span>
      <span class="value">0 g</span>
    </div>
  </div>
</div>
        <div class="meal-tabs">
            <div class="tab-list" id="meal-tab-list">
                <button class="tab active" data-meal="Desayuno" data-icon="coffee"><i class="fas fa-mug-hot"></i> Desayuno</button>
                <button class="tab" data-meal="Almuerzo" data-icon="sun"><i class="fas fa-sun"></i> Almuerzo</button>
                <button class="tab" data-meal="Cena" data-icon="moon"><i class="fas fa-moon"></i> Cena</button>
                <button class="tab" data-meal="Snack" data-icon="cookie-bite"><i class="fas fa-cookie-bite"></i> Snack</button>
            </div>

           

            <button class="search-btn" id="search-meal-btn">+ Buscar Comidas</button>

        </div>



        <div class="joji-card meal-content">

            <h4 id="meal-title">Mis Desayunos</h4>

           

            <div class="empty-state">

                <i class="fas fa-coffee" id="empty-state-icon"></i>

                <p id="empty-state-text-1">No has agregado Desayunos</p>

                <p style="margin-top: 5px; color: #aaa;">Busca y agrega comidas a tu plan nutricional</p>

                <button class="empty-search-btn" id="empty-search-meal-btn">+ Buscar Desayunos</button>

            </div>

        </div>

       

        <div class="suggestions-section-empty">

            <i class="fas fa-brain"></i>

            <h4>Sugerencias de Comidas en Desarrollo</h4>

            <p>Aún no tenemos suficientes datos o la base de recomendaciones no está conectada.</p>

            <p style="font-size: 12px; margin-top: 15px;">Una vez que completes tu perfil, verás aquí recomendaciones personalizadas.</p>

        </div>

       

    </div>

   

    <div class="modal-overlay" id="search-food-modal">

        <div class="modal-content">

            <div class="modal-header">

                <h3 id="modal-food-title">Buscar Desayunos</h3>

                <button class="close-btn" id="close-food-modal-btn">&times;</button>

            </div>

           

            <p style="color: var(--joji-secondary-text); font-size: 14px; margin-bottom: 20px;">

                Explora nuestra base de datos de comidas saludables

            </p>

           

            <div class="search-bar">

                <i class="fas fa-search"></i>

                <input type="text" placeholder="Buscar por nombre o ingrediente (ej: Pollo, Aguacate...)">

            </div>

           
            <div class="filter-section">
                <h4>Categoría Nutricional</h4>
                <div class="filter-buttons-food">
                    <button class="filter-btn-food active">Todos</button>
                    <button class="filter-btn-food">Balanceado</button>
                    <button class="filter-btn-food">Vegetariano</button>
                    <button class="filter-btn-food">Alto en Proteína</button>
                    <button class="filter-btn-food">Vegano</button>
                    <button class="filter-btn-food">Bajo en Carbos</button>
                </div>
            </div>

           

            <div class="results-container-food">
                <p class="results-header-food">0 comidas encontradas</p>
                <div class="results-grid-food">
                    <div class="empty-search-results-food">
                        <i class="fas fa-utensils"></i>
                        <h4>No se encontraron comidas</h4>
                        <p>Intenta ajustar tus filtros o busca por un nombre diferente.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>