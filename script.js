document.addEventListener('DOMContentLoaded', () => {
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
                dayTitle.textContent = `Ejercicios de ${dayName}`;
                emptyDayText.textContent = dayName;
                suggestDayText.textContent = dayName;
                addExBtn.textContent = `+ Agregar Ejercicio`;
                emptyAddExBtn.textContent = `+ Agregar primer ejercicio`;
                modalTitle.textContent = `Agregar ejercicio a ${dayName}`;
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
                 // Si no hay ninguno activo, toma el primero (Lunes)
                 updateDayContent(tabs[0].getAttribute('data-day'));
            }

            // --- Control del Modal ---

            // Abrir modal con los dos botones
            addExBtn.addEventListener('click', () => {
                modal.style.display = 'flex';
            });
            emptyAddExBtn.addEventListener('click', () => {
                modal.style.display = 'flex';
            });

            // Cerrar modal con el botón X
            closeModalBtn.addEventListener('click', () => {
                modal.style.display = 'none';
            });

            // Cerrar modal al hacer clic fuera del contenido
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    modal.style.display = 'none';
                }
            });
            
            // --- Control de Filtros (Solo activación/estilo) ---
            const filterButtons = document.querySelectorAll('.filter-btn');
            filterButtons.forEach(btn => {
                btn.addEventListener('click', (e) => {
                    e.target.classList.toggle('active');
                });
            });
        });

         document.addEventListener('DOMContentLoaded', () => {

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

               

                mealTitle.textContent = `Mis ${mealNamePlural}`;
                emptyStateIcon.className = `fas fa-${mealIcon}`;
                emptyStateText1.textContent = `No has agregado ${mealNamePlural}`;
                const searchButtonText = `+ Buscar ${mealNamePlural}`;
                emptySearchMealBtn.textContent = searchButtonText;
                searchMealBtn.textContent = searchButtonText;
                modalTitle.textContent = `Buscar ${mealNamePlural}`;

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
            searchMealBtn.addEventListener('click', () => {
                modal.style.display = 'flex';
            });
            emptySearchMealBtn.addEventListener('click', () => {
                modal.style.display = 'flex';
            });
            closeModalBtn.addEventListener('click', () => {
                modal.style.display = 'none';
            });
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    modal.style.display = 'none';
                }
            });
    
            const foodFilterButtons = document.querySelectorAll('.filter-btn-food');
            foodFilterButtons.forEach(btn => {
                btn.addEventListener('click', (e) => {
                    e.target.classList.toggle('active');
                });
            });
        });
