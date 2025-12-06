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

<style>
    /* Contenedor de ejercicios agregados */
#added-exercises-container {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 20px;
    margin-top: 20px;
    padding: 10px;
}

/* Estado vacío de ejercicios */
.empty-ex-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 40px;
    text-align: center;
}

.empty-ex-state i {
    font-size: 48px;
    color: #ccc;
    margin-bottom: 20px;
}

/* Tarjetas de ejercicio */
.exercise-card {
    background: white;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    transition: transform 0.2s, box-shadow 0.2s;
}

.exercise-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.exercise-card h4 {
    margin: 0 0 15px 0;
    color: #333;
    font-size: 18px;
    font-weight: 600;
}

.exercise-card p {
    margin: 5px 0;
    font-size: 14px;
    color: #666;
}

.exercise-card strong {
    color: #333;
}

/* Botones de agregar y eliminar ejercicios */
.add-exercise-btn, .remove-exercise-btn {
    width: 100%;
    padding: 10px;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
    margin-top: 10px;
}

.add-exercise-btn {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.add-exercise-btn:hover {
    transform: scale(1.05);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
}

.remove-exercise-btn {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    color: white;
}

.remove-exercise-btn:hover {
    transform: scale(1.05);
    box-shadow: 0 4px 12px rgba(245, 87, 108, 0.4);
}


/* Botones de filtro */
.filter-btn {
    background: white;
    border: 2px solid #e0e0e0;
    padding: 10px 20px;
    border-radius: 20px;
    cursor: pointer;
    font-weight: 500;
    color: #666;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.filter-btn:hover {
    border-color: #667eea;
    color: #667eea;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(102, 126, 234, 0.2);
}

.filter-btn.active {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-color: transparent;
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
}

.filter-btn i {
    font-size: 14px;
}

/* Sección de filtros */
.filter-section {
    margin-bottom: 20px;
}

.filter-section h4 {
    margin-bottom: 10px;
    color: #333;
    font-size: 16px;
    font-weight: 600;
}

.filter-buttons {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
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
                <div class="macro-ex-card duracion"><i class="far fa-clock"></i> Series <strong>0 </strong></div>
                <div class="macro-ex-card calorias"><i class="fas fa-fire"></i> Repeticiones <strong>0 </strong></div>
                
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
            <div id="added-exercises-container"></div>
        </div>

                   <h4> <span id="suggest-day-text"></span></h4>
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

    <script>


// ==========================
// MODAL: ABRIR Y CERRAR
// ==========================
const addExModal = document.getElementById("add-exercise-modal");
const openModalBtns = [document.getElementById("add-ex-btn"), document.getElementById("empty-add-ex-btn")];
const closeModalBtn = document.getElementById("close-modal-btn");

openModalBtns.forEach(btn => {
    btn.addEventListener("click", () => {
        const dia = document.querySelector(".day-tab.active").getAttribute("data-day");
        document.getElementById("modal-title").textContent = "Agregar ejercicio a " + dia;
        addExModal.classList.add("open");
        cargarEjercicios(); // carga todos inicialmente
    });
});



closeModalBtn.addEventListener("click", () => {
    addExModal.classList.remove("open");
});
        // ==========================
// FILTROS Y BÚSQUEDA DE EJERCICIOS
// ==========================
function cargarEjercicios(filtroCuerpo = "", filtroEquipo = "", search = "") {
    fetch(`obtener_ejercicios.php?cuerpo=${encodeURIComponent(filtroCuerpo)}&equipo=${encodeURIComponent(filtroEquipo)}&search=${encodeURIComponent(search)}`)
        .then(res => res.json())
        .then(data => mostrarResultadosModal(data))
        .catch(err => console.log("Error al cargar ejercicios:", err));
}

// Mostrar ejercicios en modal
function mostrarResultadosModal(lista) {
    const container = document.querySelector(".results-grid");
    const header = document.querySelector(".results-header");
    container.innerHTML = "";
    header.textContent = `${lista.length} ejercicios encontrados`;

    if(lista.length === 0){
        container.innerHTML = `<div class="empty-search-results">
            <i class="fas fa-search-minus"></i>
            <h4>No se encontraron ejercicios</h4>
            <p>Intenta ajustar tus filtros o busca por un nombre diferente.</p>
        </div>`;
        return;
    }

    lista.forEach(ej => {
        const card = document.createElement("div");
        card.classList.add("exercise-card");
        card.innerHTML = `
            <h4>${ej.nombre}</h4>
            <p><strong>Grupo muscular:</strong> ${ej.grupo_muscular}</p>
            <p><strong>Dificultad:</strong> ${ej.dificultad}</p>
            <p><strong>Tipo:</strong> ${ej.tipo_equipo}</p>
            <button class="add-exercise-btn" data-id="${ej.id_ejercicio}">Agregar</button>
        `;
        container.appendChild(card);
    });
}

// ==========================
// FILTROS: PARTE DEL CUERPO Y EQUIPO
// ==========================

let filtroActualCuerpo = "";
let filtroActualEquipo = "";
let busquedaActual = "";

// Obtener todas las secciones de filtros del modal
const filterSections = document.querySelectorAll("#add-exercise-modal .filter-section");

// Filtros de "Tipo de Equipo" (primera sección)
if(filterSections[0]) {
    filterSections[0].querySelectorAll(".filter-btn").forEach(btn => {
        btn.addEventListener("click", () => {
            // Remover active de todos los botones de esta sección
            filterSections[0].querySelectorAll(".filter-btn").forEach(b => {
                b.classList.remove("active");
            });
            
            // Activar el botón clickeado
            btn.classList.add("active");
            
            // Obtener el filtro (eliminar iconos si hay)
            let textoEquipo = btn.textContent.trim();
            
            // Mapear texto del botón a valores de la BD
            const mapaEquipo = {
                "Todos": "",
                "Casa": "Casa",
                "Máquinas": "Máquinas",
                "Poleas": "Poleas",
                "Peso Libre": "Peso Libre",
                "Cardio": "Cardio"
            };
            
            filtroActualEquipo = mapaEquipo[textoEquipo] || "";
            
            // Recargar ejercicios con filtros
            cargarEjercicios(filtroActualCuerpo, filtroActualEquipo, busquedaActual);
        });
    });
}

// Filtros de "Parte del Cuerpo" (segunda sección)
if(filterSections[1]) {
    filterSections[1].querySelectorAll(".filter-btn").forEach(btn => {
        btn.addEventListener("click", () => {
            // Remover active de todos los botones de esta sección
            filterSections[1].querySelectorAll(".filter-btn").forEach(b => {
                b.classList.remove("active");
            });
            
            // Activar el botón clickeado
            btn.classList.add("active");
            
            // Obtener el filtro
            filtroActualCuerpo = btn.textContent.trim() === "Todos" ? "" : btn.textContent.trim();
            
            // Recargar ejercicios con filtros
            cargarEjercicios(filtroActualCuerpo, filtroActualEquipo, busquedaActual);
        });
    });
}

// Barra de búsqueda
const searchInput = document.querySelector("#add-exercise-modal .search-bar input");
if(searchInput) {
    searchInput.addEventListener("input", (e) => {
        busquedaActual = e.target.value.trim();
        cargarEjercicios(filtroActualCuerpo, filtroActualEquipo, busquedaActual);
    });
}



// ==========================
// AGREGAR EJERCICIO A DETALLE_RUTINA
// ==========================
document.addEventListener("click", function(e){
    if(e.target.classList.contains("add-exercise-btn")){
        const id_ejercicio = e.target.getAttribute("data-id");
        const dia = document.querySelector(".day-tab.active").getAttribute("data-day");

        fetch("registrar_ejercicio_rutina.php", {
            method: "POST",
            headers: {"Content-Type": "application/x-www-form-urlencoded"},
            body: `id_ejercicio=${id_ejercicio}&dia=${encodeURIComponent(dia)}`
        })
        .then(res => res.json())
        .then(data => {
            if(data.success){
                cargarEjerciciosAgregados();
                actualizarTotales();
                alert("Ejercicio agregado ✅");
            } else {
                alert(data.msg);
            }
        });
    }
});

// ==========================
// CARGAR EJERCICIOS AGREGADOS POR DÍA
// ==========================
function cargarEjerciciosAgregados() {
    const dia = document.querySelector(".day-tab.active").getAttribute("data-day");

    fetch(`obtener_ejercicios_usuario.php?dia=${encodeURIComponent(dia)}`)
        .then(res => res.json())
        .then(data => mostrarEjerciciosAgregados(data))
        .catch(err => console.log("Error al cargar ejercicios agregados:", err));
}


function mostrarEjerciciosAgregados(lista) {
    const container = document.getElementById("added-exercises-container");
    const emptyState = document.querySelector(".empty-ex-state");
    
    // Limpiar contenedor
    container.innerHTML = "";

    if(lista.length === 0){
        emptyState.style.display = "flex";
        container.style.display = "none";
        
        const activeDay = document.querySelector(".day-tab.active");
        if(activeDay) {
            document.getElementById("empty-day-text").textContent = activeDay.getAttribute("data-day");
        }
        return;
    }

    emptyState.style.display = "none";
    container.style.display = "grid";

    lista.forEach(ej => {
        const card = document.createElement("div");
        card.classList.add("exercise-card");
        card.innerHTML = `
            <h4>${ej.nombre}</h4>
            <p><strong>Grupo muscular:</strong> ${ej.grupo_muscular}</p>
            <p><strong>Día:</strong> ${ej.dia_semana}</p>
            <p><strong>Series:</strong> ${ej.series}</p>
            <p><strong>Repeticiones:</strong> ${ej.repeticiones}</p>
            <button class="remove-exercise-btn" data-id="${ej.id_detalle}">Eliminar</button>
        `;
        container.appendChild(card);
    });
}

// ==========================
// ELIMINAR EJERCICIO
// ==========================
document.addEventListener("click", function(e){
    if(e.target.classList.contains("remove-exercise-btn")){
        const id = e.target.getAttribute("data-id");
        fetch("eliminar_ejercicio_rutina.php", {
            method: "POST",
            headers: {"Content-Type": "application/x-www-form-urlencoded"},
            body: `id=${id}`
        })
        .then(res => res.json())
        .then(data => {
            if(data.success){
                cargarEjerciciosAgregados();
                actualizarTotales();
            }
        });
    }
});

// ==========================
// ACTUALIZAR TOTALES
// ==========================
function actualizarTotales() {
    const dia = document.querySelector(".day-tab.active").getAttribute("data-day");

    fetch(`totales_rutina.php?dia=${encodeURIComponent(dia)}`)
        .then(res => res.json())
        .then(data => {
            document.querySelector(".macro-ex-card.ejercicios strong").textContent = data.total_ejercicios;
            document.querySelector(".macro-ex-card.duracion strong").textContent = data.total_series;
            document.querySelector(".macro-ex-card.calorias strong").textContent = data.total_repeticiones;

            // Actualizar contador de tabs
            document.querySelectorAll(".day-tab").forEach(tab => {
                if(tab.getAttribute("data-day") === dia){
                    tab.querySelector(".count").textContent = data.total_ejercicios;
                }
            });
        });
}

// ==========================
// CAMBIAR DÍA ACTIVO
// ==========================
document.querySelectorAll(".day-tab").forEach(tab => {
    tab.addEventListener("click", () => {
        document.querySelectorAll(".day-tab").forEach(t => t.classList.remove("active"));
        tab.classList.add("active");
        document.getElementById("day-title").textContent = "Ejercicios de " + tab.textContent.trim();
       // document.getElementById("suggest-day-text").textContent = tab.textContent.trim();
        cargarEjerciciosAgregados();
        actualizarTotales();
    });
});

// ==========================
// INICIALIZAR
// ==========================
window.addEventListener("load", () => {
    cargarEjerciciosAgregados();
    actualizarTotales();
});

    </script>
</body>
</html>