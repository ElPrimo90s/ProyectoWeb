document.addEventListener('DOMContentLoaded', () => {
    // ============================================
    // CÓDIGO PARA EJERCICIOS (solo si existen los elementos)
    // ============================================
    const dayTabList = document.getElementById('day-tab-list');
    
    if (dayTabList) {
        const tabs = document.querySelectorAll('#day-tab-list .day-tab');
        const dayTitle = document.getElementById('day-title');
        const emptyDayText = document.getElementById('empty-day-text');
        const suggestDayText = document.getElementById('suggest-day-text');
        const addExBtn = document.getElementById('add-ex-btn');
        const emptyAddExBtn = document.getElementById('empty-add-ex-btn');
        
        // --- Elementos del Modal ---
        const modal = document.getElementById('add-exercise-modal');
        const closeModalBtn = document.getElementById('close-modal-btn');
        const modalTitle = document.getElementById('modal-title');
        let currentDay = 'Lunes'; 

        // Función para actualizar el contenido del día
        const updateDayContent = (dayName) => {
            currentDay = dayName;
            if (dayTitle) dayTitle.textContent = `Ejercicios de ${dayName}`;
            if (emptyDayText) emptyDayText.textContent = dayName;
            if (suggestDayText) suggestDayText.textContent = dayName;
            if (addExBtn) addExBtn.textContent = `+ Agregar Ejercicio`;
            if (emptyAddExBtn) emptyAddExBtn.textContent = `+ Agregar primer ejercicio`;
            if (modalTitle) modalTitle.textContent = `Agregar ejercicio a ${dayName}`;
        };

        // Event Listeners para las pestañas de días
        tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                tabs.forEach(t => t.classList.remove('active'));
                tab.classList.add('active');
                const dayName = tab.getAttribute('data-day');
                updateDayContent(dayName);
            });
        });
        
        // Inicializar el contenido con el día activo
        const initialDayTab = document.querySelector('#day-tab-list .day-tab.active');
        if (initialDayTab) {
            updateDayContent(initialDayTab.getAttribute('data-day'));
        } else if (tabs.length > 0) {
            updateDayContent(tabs[0].getAttribute('data-day'));
        }

        // --- Control del Modal ---
        if (addExBtn && modal) {
            addExBtn.addEventListener('click', () => {
                modal.style.display = 'flex';
            });
        }
        
        if (emptyAddExBtn && modal) {
            emptyAddExBtn.addEventListener('click', () => {
                modal.style.display = 'flex';
            });
        }

        // Cerrar modal con el botón X
        if (closeModalBtn && modal) {
            closeModalBtn.addEventListener('click', () => {
                modal.style.display = 'none';
            });
        }

        // Cerrar modal al hacer clic fuera del contenido
        if (modal) {
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    modal.style.display = 'none';
                }
            });
        }
    }
    
    // ============================================
    // CÓDIGO PARA COMIDAS (solo si existen los elementos)
    // ============================================
    const mealTabList = document.getElementById('meal-tab-list');
    
    if (mealTabList) {
        const tabs = document.querySelectorAll('#meal-tab-list .tab');
        const mealTitle = document.getElementById('meal-title');
        const emptyStateIcon = document.getElementById('empty-state-icon');
        const emptyStateText1 = document.getElementById('empty-state-text-1');
        const emptySearchMealBtn = document.getElementById('empty-search-meal-btn');
        const searchMealBtn = document.getElementById('search-meal-btn');
        const modal = document.getElementById('search-food-modal');
        const closeModalBtn = document.getElementById('close-food-modal-btn');
        const modalTitle = document.getElementById('modal-food-title');
        let currentMealName = 'Desayunos'; 

        const updateMealContent = (mealName, mealIcon) => {
            currentMealName = mealName;
            const mealNamePlural = mealName.endsWith('s') ? mealName : mealName + 's';
           
            if (mealTitle) mealTitle.textContent = `Mis ${mealNamePlural}`;
            if (emptyStateIcon) emptyStateIcon.className = `fas fa-${mealIcon}`;
            if (emptyStateText1) emptyStateText1.textContent = `No has agregado ${mealNamePlural}`;
            
            const searchButtonText = `+ Buscar ${mealNamePlural}`;
            if (emptySearchMealBtn) emptySearchMealBtn.textContent = searchButtonText;
            if (searchMealBtn) searchMealBtn.textContent = searchButtonText;
            if (modalTitle) modalTitle.textContent = `Buscar ${mealNamePlural}`;
        };
        
        tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                tabs.forEach(t => t.classList.remove('active'));
                tab.classList.add('active');
                const mealName = tab.getAttribute('data-meal');
                const mealIcon = tab.getAttribute('data-icon');
                updateMealContent(mealName, mealIcon);
            });
        });
        
        const initialTab = document.querySelector('#meal-tab-list .tab.active');
        if (initialTab) {
            updateMealContent(initialTab.getAttribute('data-meal'), initialTab.getAttribute('data-icon'));
        }
        
        if (searchMealBtn && modal) {
            searchMealBtn.addEventListener('click', () => {
                modal.style.display = 'flex';
            });
        }
        
        if (emptySearchMealBtn && modal) {
            emptySearchMealBtn.addEventListener('click', () => {
                modal.style.display = 'flex';
            });
        }
        
        if (closeModalBtn && modal) {
            closeModalBtn.addEventListener('click', () => {
                modal.style.display = 'none';
            });
        }
        
        if (modal) {
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    modal.style.display = 'none';
                }
            });
        }
        
        const foodFilterButtons = document.querySelectorAll('.filter-btn-food');
        foodFilterButtons.forEach(btn => {
            btn.addEventListener('click', (e) => {
                foodFilterButtons.forEach(b => b.classList.remove('active'));
                e.target.classList.add('active');
            });
        });
    }
});

// ============================================
// FUNCIONES PARA COMIDAS (disponibles globalmente)
// ============================================
async function cargarComidas(filtro = "Todos") {
    const res = await fetch(`get_comidas.php?filtro=${filtro}`);
    const comidas = await res.json();

    const cont = document.querySelector(".results-grid-food");
    if (!cont) return;
    
    cont.innerHTML = "";

    comidas.forEach(c => {
        cont.innerHTML += `
        <div class="food-card">
            <img src="${c.url_imagen}">
            <h4>${c.nombre}</h4>
            <p>${c.calorias} kcal</p>
            <p>P:${c.proteinas} G:${c.grasas} C:${c.carbohidratos}</p>
            <span class="tag">${c.etiqueta}</span>
            <button onclick="agregarComida(${c.id_comida})">Agregar</button>
        </div>`;
    });
}

async function agregarComida(id_comida) {
    const momento = document.querySelector(".tab.active").dataset.meal;

    const formData = new FormData();
    formData.append("id_comida", id_comida);
    formData.append("momento", momento);

    let res = await fetch("registrar_comida_usuario.php", {
        method: "POST",
        body: formData
    });

    let data = await res.json();

    if (data.success) {
        cargarComidasUsuario(momento);
    }
}

async function cargarComidasUsuario(momento) {
    const res = await fetch(`get_comidas_usuario.php?momento=${momento}`);
    const comidas = await res.json();

    const cont = document.querySelector(".meal-content");
    if (!cont) return;
    
    cont.innerHTML = "";

    comidas.forEach(c => {
        cont.innerHTML += `
            <div class="user-food">
                <img src="${c.url_imagen}">
                <div>
                    <h4>${c.nombre}</h4>
                    <p>${c.calorias} kcal</p>
                </div>
            </div>`;
    });
}