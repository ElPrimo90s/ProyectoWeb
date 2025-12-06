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

<style>
    /* Contenedor de comidas agregadas */
#added-foods-container {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 20px;
    margin-top: 20px;
    padding: 10px;
}

/* Asegurar que el estado vacío se oculte cuando hay comidas */
.meal-content .empty-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 40px;
    text-align: center;
}

.meal-content .empty-state i {
    font-size: 48px;
    color: #ccc;
    margin-bottom: 20px;
}

/* Tarjetas de comida */
.food-card {
    background: white;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    transition: transform 0.2s, box-shadow 0.2s;
}

.food-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.food-card h4 {
    margin: 0 0 15px 0;
    color: #333;
    font-size: 18px;
    font-weight: 600;
}

.nutri-mini {
    margin-bottom: 15px;
}

.nutri-mini p {
    margin: 5px 0;
    font-size: 14px;
    color: #666;
}

.nutri-mini strong {
    color: #333;
}

/* Botones de agregar y eliminar */
.add-food-btn, .remove-food-btn {
    width: 100%;
    padding: 10px;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
}

.add-food-btn {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.add-food-btn:hover {
    transform: scale(1.05);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
}

.remove-food-btn {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    color: white;
}

.remove-food-btn:hover {
    transform: scale(1.05);
    box-shadow: 0 4px 12px rgba(245, 87, 108, 0.4);
}

/* Cambio */

/* Selector de fecha */
.date-selector-section {
    background: white;
    padding: 20px;
    border-radius: 15px;
    margin-bottom: 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.date-controls {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 15px;
    margin-bottom: 15px;
}

.date-nav-btn {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s;
}

.date-nav-btn:hover {
    transform: scale(1.1);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
}

.date-display {
    display: flex;
    align-items: center;
    gap: 10px;
    background: #f8f9fa;
    padding: 10px 20px;
    border-radius: 25px;
    font-weight: 600;
    color: #333;
}

.date-display i {
    color: #667eea;
}

#fecha-seleccionada {
    border: none;
    background: transparent;
    font-weight: 600;
    color: #333;
    cursor: pointer;
    font-size: 16px;
}

#fecha-texto {
    font-size: 14px;
    color: #666;
}

.quick-dates {
    display: flex;
    gap: 10px;
    justify-content: center;
}

.quick-date-btn {
    background: white;
    border: 2px solid #e0e0e0;
    padding: 8px 20px;
    border-radius: 20px;
    cursor: pointer;
    font-weight: 500;
    color: #666;
    transition: all 0.3s;
}

.quick-date-btn:hover {
    border-color: #667eea;
    color: #667eea;
}

.quick-date-btn.active {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-color: transparent;
}
</style>
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
            <div id="added-foods-container"></div>
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

    <script>
// ==========================
//   CARGAR COMIDAS DESDE BD
// ==========================
function cargarComidas(etiqueta = "") {
    fetch(`obtener_comidas.php?etiqueta=${encodeURIComponent(etiqueta)}`)
        .then(res => res.json())
        .then(data => {
            // El resto de la lógica para mostrar comidas en el modal
            if (data.length === 0) {
                // Necesitas una función mostrarVacio para el modal si la tienes
                // Aquí solo mostramos resultados si hay
                return;
            }
            mostrarResultados(data);
        })
        .catch(err => {
            console.log("Error al cargar comidas:", err);
            // mostrarVacio(); // Si tienes un estado vacío para el modal
        });
}

// ==========================
//   MOSTRAR RESULTADOS (EN EL MODAL DE BÚSQUEDA)
// ==========================
function mostrarResultados(lista) {
    const resultsContainer = document.querySelector(".results-grid-food");
    const resultsHeader = document.querySelector(".results-header-food");

    resultsContainer.innerHTML = "";
    resultsHeader.textContent = `${lista.length} comidas encontradas`;

    lista.forEach(p => {
        const card = `
            <div class="food-card">
                <h4>${p.nombre}</h4>
                <div class="nutri-mini">
                    <p><strong>Calorías:</strong> ${p.calorias} kcal</p>
                    <p><strong>Proteína:</strong> ${p.proteinas} g</p>
                    <p><strong>Carbs:</strong> ${p.carbohidratos} g</p>
                    <p><strong>Grasas:</strong> ${p.grasas} g</p>
                    <p><strong>Etiqueta:</strong> ${p.etiqueta}</p>
                </div>
                <button class="add-food-btn" data-id="${p.id_comida}">Agregar</button>
            </div>
        `;
        resultsContainer.innerHTML += card;
    });
}

// ==========================
//   FILTRO POR ETIQUETA
// ==========================
document.querySelectorAll(".filter-btn-food").forEach(btn => {
    btn.addEventListener("click", () => {
        document.querySelectorAll(".filter-btn-food").forEach(b => b.classList.remove("active"));
        btn.classList.add("active");

        const etiqueta = btn.textContent === "Todos" ? "" : btn.textContent;
        cargarComidas(etiqueta);
    });
});


// ==========================
//   ACTUALIZAR TOTALES (Resumen Nutricional)
// ==========================
function actualizarTotales() {
    const momento = document.querySelector(".tab.active").textContent.trim();

    fetch(`totales_momento.php?momento=${encodeURIComponent(momento)}`)
        .then(res => res.json())
        .then(data => {
            document.querySelector(".nutri-card.calorias .value").textContent = data.calorias + " kcal";
            document.querySelector(".nutri-card.proteina .value").textContent = data.proteinas + " g";
            document.querySelector(".nutri-card.carbohidratos .value").textContent = data.carbohidratos + " g";
            document.querySelector(".nutri-card.grasas .value").textContent = data.grasas + " g";
        });
}


// Escuchar cambios de momento
document.querySelectorAll(".tab").forEach(tab => {
    tab.addEventListener("click", () => {
        document.querySelectorAll(".tab").forEach(t => t.classList.remove("active"));
        tab.classList.add("active");

        // Actualizar totales para el momento seleccionado
        actualizarTotales();

        // Actualizar el título y recargar la lista de comidas agregadas
        document.getElementById("meal-title").textContent = "Mis " + tab.textContent.trim();
        cargarComidasAgregadas(); // <--- Llamada esencial al cambiar de pestaña
    });
});

// ==========================
//   CARGAR TODAS AL ABRIR MODAL
// ==========================
document.getElementById("search-meal-btn").addEventListener("click", () => {
    // Aquí puedes abrir el modal
    document.getElementById("search-food-modal").classList.add("open"); 
    cargarComidas();
});
document.getElementById("empty-search-meal-btn").addEventListener("click", () => {
    // Aquí puedes abrir el modal
    document.getElementById("search-food-modal").classList.add("open"); 
    cargarComidas();
});

// Cerrar Modal
document.getElementById("close-food-modal-btn").addEventListener("click", () => {
    document.getElementById("search-food-modal").classList.remove("open");
});


// ==========================
//   SECCIÓN DE COMIDAS AGREGADAS
// ==========================

// Crear contenedor de comidas agregadas dentro de la sección meal-content
//let addedFoodsContainer = document.getElementById("added-foods-container");
//if(!addedFoodsContainer){
//    addedFoodsContainer = document.createElement("div");
 //   addedFoodsContainer.id = "added-foods-container";
 //   // Asegúrate de agregarlo DESPUÉS del empty-state si quieres que se muestre en su lugar
 //   document.querySelector(".meal-content").appendChild(addedFoodsContainer);
//}

// Función para cargar comidas agregadas según momento
function cargarComidasAgregadas() {
    const momento = document.querySelector(".tab.active").textContent.trim();
    console.log("🔍 Cargando comidas para momento:", momento);
    
    const container = document.getElementById("added-foods-container");
    const emptyState = document.querySelector(".empty-state");
    
    console.log("📦 Container existe:", container !== null);
    console.log("📦 Empty state existe:", emptyState !== null);
    
    fetch(`obtener_comidas_usuario.php?momento=${encodeURIComponent(momento)}`) 
        .then(res => {
            console.log("📡 Respuesta recibida, status:", res.status);
            return res.json();
        })
        .then(data => {
            console.log("📊 Datos recibidos:", data);
            console.log("📊 Tipo de datos:", typeof data);
            console.log("📊 Es array:", Array.isArray(data));
            console.log("📊 Cantidad de comidas:", data.length);
            
            if(data.length > 0) {
                console.log("✅ Primera comida:", data[0]);
            }
            
            mostrarComidasAgregadas(data);
        })
        .catch(err => {
            console.error("❌ Error al cargar comidas agregadas:", err);
        });
}

// Función para mostrar comidas agregadas
function mostrarComidasAgregadas(lista) {
    console.log("🎨 Iniciando mostrarComidasAgregadas con:", lista.length, "comidas");
    
    const container = document.getElementById("added-foods-container");
    const emptyState = document.querySelector(".empty-state");
    
    console.log("🎨 Container encontrado:", container !== null);
    console.log("🎨 Empty state encontrado:", emptyState !== null);

    // Limpiar el contenedor
    container.innerHTML = "";

    if (lista.length === 0) {
        console.log("⚠️ Lista vacía, mostrando empty state");
        emptyState.style.display = "flex";
        container.style.display = "none";
        
        const activeTab = document.querySelector(".tab.active");
        if(activeTab){
            const iconClass = activeTab.getAttribute("data-icon");
            const momento = activeTab.textContent.trim();
            document.getElementById("empty-state-icon").className = `fas fa-${iconClass}`;
            document.getElementById("empty-state-text-1").textContent = `No has agregado ${momento}s`;
            document.getElementById("empty-search-meal-btn").textContent = `+ Buscar ${momento}s`;
        }
        return;
    } else {
        console.log("✅ Hay comidas, ocultando empty state y mostrando grid");
        emptyState.style.display = "none";
        container.style.display = "grid";
    }

    lista.forEach((comida, index) => {
        console.log(`➕ Agregando comida ${index + 1}:`, comida.nombre);
        
        const card = document.createElement("div");
        card.classList.add("food-card");
        card.innerHTML = `
            <h4>${comida.nombre}</h4>
            <div class="nutri-mini">
                <p><strong>Calorías:</strong> ${comida.calorias} kcal</p>
                <p><strong>Proteína:</strong> ${comida.proteinas} g</p>
                <p><strong>Carbs:</strong> ${comida.carbohidratos} g</p>
                <p><strong>Grasas:</strong> ${comida.grasas} g</p>
                <p><strong>Etiqueta:</strong> ${comida.etiqueta}</p>
            </div>
            <button class="remove-food-btn" data-id="${comida.id_registro}">Eliminar</button> 
        `;
        container.appendChild(card);
    });
    
    console.log("🎉 Comidas agregadas al DOM");
}

// Escuchar clicks para eliminar comida agregada (Mantenemos el bloque, aunque no sea funcional)
document.addEventListener("click", function(e){
    if(e.target.classList.contains("remove-food-btn")){
        const id = e.target.getAttribute("data-id");
        // Lógica de eliminación (Si el archivo PHP no existe, simplemente fallará aquí)
        fetch("eliminar_comida_usuario.php", {
            method: "POST",
            headers: {"Content-Type": "application/x-www-form-urlencoded"},
            body: `id=${id}`
        })
        .then(res => res.json())
        .then(data => {
            if(data.success){
                cargarComidasAgregadas();
                actualizarTotales();
            } else {
                alert("Error: " + data.msg);
            }
        })
        .catch(err => console.log("Error al intentar eliminar:", err));
    }
});

// Después de agregar comida, recargar la lista de agregadas
// Este bloque es crucial para que se muestre inmediatamente después de agregar
document.addEventListener("click", function(e){
    if(e.target.classList.contains("add-food-btn")){
        const id_comida = e.target.getAttribute("data-id");
        const momento = document.querySelector(".tab.active").textContent.trim();

        fetch("registrar_comida_usuario.php", {
            method: "POST",
            headers: {"Content-Type": "application/x-www-form-urlencoded"},
            body: `id_comida=${id_comida}&momento=${encodeURIComponent(momento)}`
        })
        .then(res => res.json())
        .then(data => {
            if(data.success){
                // alert("Comida agregada a " + momento); // Puedes quitar este alert si quieres
                actualizarTotales();
                cargarComidasAgregadas(); // <--- RECARGA la lista y la muestra
            } else {
                alert(data.msg);
            }
        })
        .catch(err => console.log(err));
    }
});

// Inicializar al cargar la página (Carga las comidas del momento activo y los totales)
window.addEventListener("load", () => {
    cargarComidasAgregadas();
    actualizarTotales();
});
</script>
</body>
</html>