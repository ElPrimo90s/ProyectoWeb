<?php
session_start();

if (!isset($_SESSION["id_usuario"])) {
    header("Location: RegistroTest.html");
    exit();
}

$nombre = $_SESSION["nombre"];
$correo = $_SESSION["correo"];

// ----------------- CONEXIÓN A BD -----------------
$servername = "localhost";
$username   = "root";
$password   = "12345678";      // tu contraseña
$dbname     = "fitness_app";

$conn = new mysqli($servername, $username, $password, $dbname);

// Validar conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// ----------------- OBTENER DATOS EXTRA -----------------
$id = $_SESSION["id_usuario"];

$sql = "
    SELECT edad, sexo, altura, peso_inicial
    FROM usuarios 
    WHERE id_usuario = $id
";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row         = $result->fetch_assoc();
    $edad        = $row["edad"];
    $sexo        = $row["sexo"];
    $altura      = $row["altura"];
    $pesoActual  = $row["peso_inicial"];
} else {
    $edad       = "N/D";
    $sexo       = "N/D";
    $altura     = "N/D";
    $pesoActual = "N/D";
}

/* ==========================================
   OBTENER ÚLTIMO OBJETIVO DEL USUARIO
   ========================================== */
$sql_obj = "
    SELECT peso_objetivo, tipo_meta, dias_entrenamiento
    FROM objetivos_usuario
    WHERE id_usuario = $id
    ORDER BY id_objetivo DESC
    LIMIT 1
";
$result_obj = $conn->query($sql_obj);

if ($result_obj->num_rows > 0) {
    $obj = $result_obj->fetch_assoc();
    $metaTipo       = $obj["tipo_meta"];
    $metaPeso       = $obj["peso_objetivo"];
    $metaDias       = $obj["dias_entrenamiento"];
} else {
    $metaTipo = "N/D";
    $metaPeso = "N/D";
    $metaDias = "N/D";
}

/* ==========================================
   OBTENER ÚLTIMO IMC
   ========================================== */
$sql_imc = "
    SELECT imc_valor, categoria
    FROM imc
    WHERE id_usuario = $id
    ORDER BY id_imc DESC
    LIMIT 1
";
$result_imc = $conn->query($sql_imc);

if ($result_imc->num_rows > 0) {
    $imcData       = $result_imc->fetch_assoc();
    $imcValor      = $imcData["imc_valor"];
    $imcCategoria  = $imcData["categoria"];
} else {
    $imcValor     = "--";
    $imcCategoria = "--";
}

/* ==========================================
   OBTENER ÚLTIMO PESO REGISTRADO
   ========================================== */
$sql_peso = "
     SELECT peso_actual 
    FROM progreso
    WHERE id_usuario = $id
    ORDER BY id_progreso DESC
    LIMIT 1
";
$result_peso = $conn->query($sql_peso);

if ($result_peso->num_rows > 0) {
    $pesoActual = $result_peso->fetch_assoc()["peso_actual"];
} else {
    // Si no hay registros de progreso, usar peso inicial
    $pesoActual = $row["peso_inicial"];
}


/* ==========================================
   CALCULAR PROGRESO HACIA LA META
   ========================================== */
$progresoActual = 0;
$totalObjetivo  = 0;
$porcentaje     = 0;

if ($metaTipo != "N/D" && $metaPeso != "N/D" && $pesoActual != "N/D") {

   if ($metaTipo == "Bajar") {
    $totalObjetivo = $pesoInicial - $metaPeso;          // kilos totales a perder
    $progresoActual = $pesoInicial - $pesoActual;      // kilos ya perdidos
} elseif ($metaTipo == "Subir") {
    $totalObjetivo = $metaPeso - $pesoInicial;         // kilos totales a ganar
    $progresoActual = $pesoActual - $pesoInicial;      // kilos ya ganados
} else { // Mantener
    $totalObjetivo = 1; // para evitar división por cero
    $progresoActual = 1;
}

// Limitar porcentaje entre 0 y 100
$porcentaje = ($totalObjetivo != 0) ? ($progresoActual / $totalObjetivo) * 100 : 0;
if ($porcentaje < 0) $porcentaje = 0;
if ($porcentaje > 100) $porcentaje = 100;
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Montserrat:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="estilos.css">
</head>

<style>
    .delete-account-card {
    border: 2px solid red;
    background-color: #fff;
    padding: 20px;
    border-radius: 12px;
    max-width: 300px;
    margin: 20px auto;
    text-align: center;
}

.delete-account-card h4 {
    margin-top: 0;
    color: red;
}

.delete-btn {
    background-color: red;
    color: #fff;
    border: none;
    padding: 10px 18px;
    border-radius: 10px;
    cursor: pointer;
    font-weight: 600;
    margin-top: 10px;
    transition: opacity 0.2s, transform 0.2s;
}

.delete-btn:hover {
    opacity: 0.9;
    transform: translateY(-2px);
}

.modal {
    display: none; /* oculto por defecto */
    position: fixed;
    top: 0; left: 0;
    width: 100%; height: 100%;
    background-color: rgba(0,0,0,0.5);
    justify-content: center;
    align-items: center;
    z-index: 1000;
}

.modal-content {
    background: #fff;
    padding: 30px;
    border-radius: 12px;
    max-width: 400px;
    text-align: center;
}

.modal-buttons {
    display: flex;
    justify-content: space-around;
    margin-top: 20px;
}

.cancel-btn {
    background-color: #ccc;
    color: #333;
    border: none;
    padding: 10px 18px;
    border-radius: 10px;
    cursor: pointer;
    font-weight: 600;
}


.update-weight-section {
    margin-top: 20px;
    text-align: center;
}

.update-btn {
    background: linear-gradient(90deg, #e9a5dd 0%, #d6eaff 100%);
    color: #123;
    border: none;
    padding: 10px 20px;
    border-radius: 12px;
    cursor: pointer;
    font-weight: 600;
    transition: transform 0.2s, box-shadow 0.2s, opacity 0.2s;
}

.update-btn:hover {
    transform: translateY(-2px);
    opacity: 0.95;
}

.modal input[type="number"] {
    width: 80%;
    padding: 10px;
    border-radius: 10px;
    border: 1px solid rgba(100,110,130,0.12);
    margin-top: 10px;
    margin-bottom: 10px;
}

#graficaMacros,
#graficaActividad {
    max-width: 300px;
    max-height: 300px;
    margin: 0 auto; /* centrar */
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
        <a href="comidas.php" class="nav-link"><i class="fas fa-utensils"></i> Comidas</a>
        <button class="active"><i class="fas fa-user"></i> Perfil</button>
    </div>

    <div class="dashboard-container">
        
        <div class="grid-layout">
            
            <div class="joji-card profile-card">
                <div class="avatar">
                    <?php echo strtoupper(substr($nombre, 0, 1)); ?>
                </div>
                <div class="name"><?php echo htmlspecialchars($nombre); ?></div>
                <div class="email"><?php echo htmlspecialchars($correo); ?></div>
                
                <a href="configuracion_perfil.php" class="edit-btn">
                    Completar Perfil
                </a>
<div class="profile-details">
                    <div>
                        <span>Peso actual</span>
                        <span class="not-set"><?php echo $pesoActual; ?></span>
                    </div>
                    <div>
                        <span>Altura</span>
                        <span class="not-set"><?php echo $altura; ?></span>
                    </div>
                    <div>
                        <span>Objetivo</span>
                        <span class="not-set"><?php 
            if ($metaTipo != "N/D") {
                echo $metaTipo . " - " . $metaPeso . "kg (" . $metaDias . " días/sem)";
            } else {
                echo "Sin objetivo";
            }
        ?></span>
                    </div>
                    <div>
                        <span>IMC</span>
                        <span class="imc-badge empty"><?php 
            if ($imcValor !== "--") {
                echo $imcValor . " (" . $imcCategoria . ")";
            } else {
                echo "--";
            }
        ?></span>
                    </div>
                </div>
            </div>

            <div class="joji-card stats-card">
                <h3>Logros y Estadísticas</h3>
                <p style="color: var(--joji-secondary-text); font-size: 14px; margin-bottom: 25px;">Tu progreso en los últimos 30 días</p>

                <div class="stats-grid">
                    <div class="stat-mini-card tren empty-data">
                        <span class="label">Entrenamientos</span>
                        <span class="value">0 completados</span>
                    </div>
                    <div class="stat-mini-card meta empty-data">
                        <span class="label">Meta Semanal</span>
                        <span class="value"><?php 
        if ($metaDias != "N/D") {
            echo "0/" . $metaDias . " días";
        } else {
            echo "0/0 días";
        }
    ?></span>
                    </div>
                    <div class="stat-mini-card cal empty-data">
                        <span class="label">Calorías</span>
                        <span class="value">0 quemadas</span>
                    </div>
                </div>

                <div class="progress-section">
                    <div class="streak empty-data">
                        <div class="days">0</div>
                        <div class="label">Días de Racha</div>
                    </div>
                    <div class="progress-bar-container">
                        <div class="progress-label">Progreso hacia el objetivo</div>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: <?= $porcentaje ?>%;"></div>
                        </div>
                        <p style="font-size: 14px; margin-top: 10px; font-weight: 500; color: #555;">
                            <?php
        if ($metaTipo != "N/D") {

            if ($metaTipo == "Bajar") {
                echo $progresoActual . " kg perdidos / " . $totalObjetivo . " kg objetivo";
            }

            if ($metaTipo == "Subir") {
                echo $progresoActual . " kg ganados / " . $totalObjetivo . " kg objetivo";
            }

            if ($metaTipo == "Mantener") {
                echo "Objetivo mantener peso";
            }

        } else {
            echo "Completa tu perfil para establecer una meta";
        }
    ?>
                        </p>
                    </div>

                    <div class="update-weight-section">
    <button id="btn-update-weight" class="update-btn">Actualizar Peso</button>
</div>
                </div>

            </div>
        </div>

        <div class="chart-grid">
            
            <div class="joji-card chart-card">
    <h3>Evolución de Peso</h3>
    <canvas id="pesoChart" width="400" height="200"></canvas>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Llamada AJAX para obtener los datos
fetch('data_evolucion.php')
.then(response => response.json())
.then(data => {
    const ctx = document.getElementById('pesoChart').getContext('2d');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: data.fechas,
            datasets: [{
                label: 'Peso (kg)',
                data: data.pesos,
                fill: false,
                borderColor: 'rgb(75, 192, 192)',
                tension: 0.2,
                pointBackgroundColor: 'rgb(75, 192, 192)'
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: true } },
            scales: { y: { beginAtZero: false } }
        }
    });
});
</script>

            <div class="joji-card chart-card">
    <h3>Actividad Semanal</h3>
    <canvas id="graficaActividad"></canvas>
</div>
<script>
// Cargar gráfica de actividad semanal
fetch("obtener_actividad_semanal.php")
    .then(res => res.json())
    .then(data => {

        // Extraer datos
        const dias = data.map(d => d.dia_semana);
        const totales = data.map(d => d.ejercicios_realizados);

        const ctx = document.getElementById("graficaActividad").getContext("2d");

        new Chart(ctx, {
            type: "bar",
            data: {
                labels: dias,
                datasets: [{
                    label: "Ejercicios realizados",
                    data: totales,
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: true } },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1 }
                    }
                }
            }
        });
    });
</script>
        </div>
        
        <div class="joji-card chart-card">
    <h3>Distribución de Macronutrientes</h3>
    <canvas id="graficaMacros"></canvas>
</div>
<script>
fetch("obtener_macros_totales.php")
    .then(res => res.json())
    .then(data => {

        const ctx = document.getElementById("graficaMacros").getContext("2d");

        new Chart(ctx, {
            type: "pie",
            data: {
                labels: ["Proteínas", "Carbohidratos", "Grasas"],
                datasets: [{
                    data: [
                        data.total_proteinas || 0,
                        data.total_carbohidratos || 0,
                        data.total_grasas || 0
                    ]
                }]
            }
        });
    });
</script>

    </div>

    <div class="delete-account-card">
    <h4>Eliminar Cuenta</h4>
    <p>Esta acción es irreversible. Ten cuidado.</p>
    <button id="btn-delete" class="delete-btn">Eliminar Cuenta</button>
</div>

<!-- Modal de confirmación -->
<div id="modal-delete" class="modal">
    <div class="modal-content">
        <h3>¿Estás seguro?</h3>
        <p>Se eliminará tu cuenta permanentemente.</p>
        <div class="modal-buttons">
            <button id="confirm-delete" class="delete-btn">Sí, eliminar</button>
            <button id="cancel-delete" class="cancel-btn">No</button>
        </div>
    </div>
</div>


<div id="modal-update-weight" class="modal">
    <div class="modal-content">
        <h3>Actualizar Peso</h3>
        <form action="guardar_peso.php" method="POST">
            <input type="number" name="peso_actual" placeholder="Peso en kg" step="0.1" required>
            <div class="modal-buttons">
                <button type="submit" class="update-btn">Guardar</button>
                <button type="button" id="cancel-weight" class="cancel-btn">Cancelar</button>
            </div>
        </form>
    </div>
</div>


<script>
const btnDelete = document.getElementById('btn-delete');
const modalDelete = document.getElementById('modal-delete');
const cancelDelete = document.getElementById('cancel-delete');

btnDelete.addEventListener('click', () => {
    modalDelete.style.display = 'flex';
});

cancelDelete.addEventListener('click', () => {
    modalDelete.style.display = 'none';
});

// Opcional: cerrar modal al hacer clic fuera
window.addEventListener('click', (e) => {
    if (e.target === modalDelete) {
        modalDelete.style.display = 'none';
    }
});


const btnUpdate = document.getElementById('btn-update-weight');
const modalUpdate = document.getElementById('modal-update-weight');
const cancelUpdate = document.getElementById('cancel-weight');

btnUpdate.addEventListener('click', () => {
    modalUpdate.style.display = 'flex';
});

cancelUpdate.addEventListener('click', () => {
    modalUpdate.style.display = 'none';
});

// Cerrar modal al hacer clic fuera
window.addEventListener('click', (e) => {
    if (e.target === modalUpdate) {
        modalUpdate.style.display = 'none';
    }
});




</script>




</body>
</html>
