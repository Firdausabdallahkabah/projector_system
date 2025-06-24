document.addEventListener('DOMContentLoaded', () => {
    // Only run the script on the main index.php page
    if (!document.getElementById('dashboard')) {
        return;
    }

    const navLinks = document.querySelectorAll('.nav-links a[data-view]');
    const views = document.querySelectorAll('.view-container > div');
    const projectorGrid = document.querySelector('.projector-grid');
    const reservationForm = document.getElementById('reservation-form');
    const scheduleGrid = document.querySelector('.schedule-grid');
    const projectorSelects = [document.getElementById('projector'), document.getElementById('schedule-projector')];
    
    // --- Navigation Handling ---
    navLinks.forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            const viewToShow = e.target.dataset.view;
            
            navLinks.forEach(l => l.classList.remove('active'));
            e.target.classList.add('active');
            
            views.forEach(view => {
                view.id === viewToShow ? view.classList.remove('hidden') : view.classList.add('hidden');
            });
        });
    });

    // --- Data Fetching and Rendering ---

    // Fetch projectors for dashboard and select dropdowns
    async function loadProjectors() {
        try {
            const response = await fetch('api/get_projectors.php');
            if (!response.ok) throw new Error('Network response was not ok');
            const projectors = await response.json();

            projectorGrid.innerHTML = '';
            projectorSelects.forEach(select => {
                // Clear existing options except the first one
                while (select.options.length > 1) {
                    select.remove(1);
                }
            });

            if(projectors.error) {
                projectorGrid.innerHTML = `<p class="error-box">${projectors.error}</p>`;
                return;
            }

            projectors.forEach(p => {
                // Populate Dashboard
                const card = document.createElement('div');
                card.className = 'projector-card';
                card.innerHTML = `
                    <h3>${p.name} - ${p.room}</h3>
                    <p><strong>Overall Status:</strong> ${p.status}</p>
                    <p><strong>Current Status:</strong> <span class="status ${p.current_status}">${p.current_status}</span></p>
                    <p>${p.description || ''}</p>
                `;
                projectorGrid.appendChild(card);

                // Populate Select Dropdowns
                projectorSelects[0].innerHTML += `<option value="${p.id}">${p.name} - ${p.room}</option>`;
                projectorSelects[1].innerHTML += `<option value="${p.id}">${p.name}</option>`;
            });

        } catch (error) {
            projectorGrid.innerHTML = `<p class="error-box">Failed to load projectors. ${error.message}</p>`;
        }
    }

    // Fetch and display the schedule
    async function updateSchedule() {
        const scheduleDate = document.getElementById('schedule-date').value;
        const selectedProjector = document.getElementById('schedule-projector').value;

        if (!scheduleDate) {
            scheduleGrid.innerHTML = '<p>Please select a date to view the schedule.</p>';
            return;
        }

        scheduleGrid.innerHTML = '<p>Loading schedule...</p>';
        
        try {
            const response = await fetch(`api/get_reservations.php?date=${scheduleDate}&projector_id=${selectedProjector}`);
            if (!response.ok) throw new Error('Network response was not ok');
            const reservations = await response.json();
            
            scheduleGrid.innerHTML = '';

            if(reservations.error){
                scheduleGrid.innerHTML = `<p class="error-box">${reservations.error}</p>`;
                return;
            }

            if (reservations.length === 0) {
                scheduleGrid.innerHTML = '<p>No reservations for this day.</p>';
                return;
            }

            reservations.forEach(r => {
                const startTime = new Date(r.start_time).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                const endTime = new Date(r.end_time).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                
                const slot = document.createElement('div');
                slot.className = 'reservation-slot';
                slot.innerHTML = `
                    <h4>${r.projector_name} (${r.room})</h4>
                    <p><strong>Time:</strong> ${startTime} - ${endTime}</p>
                    <p><strong>Reserved by:</strong> ${r.username}</p>
                    <p><strong>Purpose:</strong> ${r.purpose}</p>
                `;
                scheduleGrid.appendChild(slot);
            });

        } catch (error) {
            scheduleGrid.innerHTML = `<p class="error-box">Failed to load schedule. ${error.message}</p>`;
        }
    }

    // --- Event Listeners ---

    // Reservation form submission
    reservationForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const formData = {
            projector_id: document.getElementById('projector').value,
            date: document.getElementById('date').value,
            start_time: document.getElementById('start-time').value,
            end_time: document.getElementById('end-time').value,
            purpose: document.getElementById('purpose').value
        };

        try {
            const response = await fetch('api/make_reservation.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(formData)
            });

            const result = await response.json();

            if (result.success) {
                alert(result.message);
                reservationForm.reset();
                loadProjectors(); // Refresh dashboard
                updateSchedule(); // Refresh schedule if a date is selected
            } else {
                alert(`Error: ${result.message}`);
            }

        } catch (error) {
            alert(`An unexpected error occurred. ${error.message}`);
        }
    });

    // Schedule filter changes
    document.getElementById('schedule-date').addEventListener('change', updateSchedule);
    document.getElementById('schedule-projector').addEventListener('change', updateSchedule);

    // --- Initial Load ---
    function initialize() {
        // Set min date to today for date inputs
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('date').min = today;
        document.getElementById('schedule-date').value = today;
        document.getElementById('schedule-date').min = today;

        loadProjectors();
        updateSchedule(); // Load schedule for today
    }

    initialize();
});