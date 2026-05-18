
@extends('admin.layout.layout')

@section('admin-title')
    Ticket Sales
@endsection

@section('admin-content')
<div class="tickets-section">
    <!-- Page Header -->
    <div class="page-header">
        <h1>Ticket Sales</h1>
        <p class="page-subtitle">Point-of-sale interface for onsite ticket purchases</p>
    </div>

    <!-- Available Dates Section - Calendar Picker -->
    <section class="tickets-dates-section">
        <h2 class="section-title">Select Visit Date</h2>
        <div class="calendar-picker-container">
            <div class="calendar-picker">
                <div class="calendar-header">
                    <button type="button" class="calendar-nav-btn" id="prevMonth">← Previous</button>
                    <h3 id="monthYear" class="calendar-month-year"></h3>
                    <button type="button" class="calendar-nav-btn" id="nextMonth">Next →</button>
                </div>
                <div class="calendar-days">
                    <div class="day-header">Sun</div>
                    <div class="day-header">Mon</div>
                    <div class="day-header">Tue</div>
                    <div class="day-header">Wed</div>
                    <div class="day-header">Thu</div>
                    <div class="day-header">Fri</div>
                    <div class="day-header">Sat</div>
                    <div id="calendarDays" class="calendar-day-cells"></div>
                </div>
            </div>
            
            <div class="selected-date-info">
                <h3>Selected Date:</h3>
                <div class="date-display" id="selectedDateDisplay">
                    <p id="selectedDateText">No date selected</p>
                    <input type="hidden" id="selectedVisitScheduleId" name="visit_schedule_id" value="">
                    <input type="hidden" id="selectedDateValue" name="selected_date" value="">
                </div>
            </div>
        </div>
    </section>

    <!-- Tickets Purchase Interface -->
    <section class="purchase-section">
        <h2 class="section-title">Purchase Tickets</h2>
        
        <form id="ticketPurchaseForm" class="purchase-form">
            <!-- Ticket Type Selection -->
            <div class="form-section">
                <label class="form-label">Ticket Type <span class="required-indicator">(Select a date first)</span></label>
                <div class="ticket-types-grid" id="ticketTypesContainer">
                    <p class="no-date-message">Please select a visit date to see available ticket types.</p>
                </div>
            </div>

            <!-- Quantity Selection -->
            <div class="form-section">
                <label class="form-label">Quantity</label>
                <div class="quantity-selector">
                    <button type="button" class="qty-btn qty-minus" onclick="decreaseQty()">−</button>
                    <input type="number" id="qty" name="quantity" value="1" min="1" max="10" class="qty-input" readonly>
                    <button type="button" class="qty-btn qty-plus" onclick="increaseQty()">+</button>
                </div>
            </div>

            <!-- Important Notes -->
            <div class="info-box">
                <h3 class="info-box__title">Important Notes</h3>
                <ul class="info-box__list">
                    <li><strong>Disability Tickets:</strong> Includes 1 free companion ticket</li>
                    <li><strong>Companion Free:</strong> Automatically added with disability ticket purchase</li>
                    <li><strong>Verification:</strong> ID required for senior and student discounts</li>
                    <li><strong>Refund Policy:</strong> Check website for current policy</li>
                </ul>
            </div>

            <!-- Order Summary -->
            <div class="order-summary">
                <h3 class="summary-title">Order Summary</h3>
                <div class="summary-item">
                    <span class="summary-label">Ticket Type:</span>
                    <span class="summary-value" id="summaryType">Adult</span>
                </div>
                <div class="summary-item">
                    <span class="summary-label">Quantity:</span>
                    <span class="summary-value" id="summaryQty">1</span>
                </div>
                <div class="summary-divider"></div>
                <div class="summary-item summary-total">
                    <span class="summary-label">Total:</span>
                    <span class="summary-value" id="summaryTotal">$10.00</span>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="form-actions">
                <button type="button" class="btn btn-secondary" onclick="resetForm()">Clear</button>
                <button type="submit" class="btn btn-primary">Complete Sale</button>
            </div>
        </form>
    </section>
</div>

<style>
.tickets-section {
    max-width: 1200px;
    margin: 0 auto;
}

.page-header {
    margin-bottom: 2rem;
}

.page-header h1 {
    font-size: 1.75rem;
    font-weight: 700;
    margin: 0 0 0.5rem 0;
    color: #333;
}

.page-subtitle {
    font-size: 0.95rem;
    color: #666;
    margin: 0;
}

.section-title {
    font-size: 1.1rem;
    font-weight: 600;
    margin-bottom: 1.5rem;
    color: #333;
}

.purchase-form {
    background: white;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    padding: 2rem;
}

.form-section {
    margin-bottom: 2rem;
}

.form-label {
    display: block;
    font-weight: 600;
    margin-bottom: 1rem;
    color: #333;
}

.ticket-types-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 1rem;
}

.ticket-type-option {
    cursor: pointer;
}

.ticket-type-option input {
    display: none;
}

.ticket-type-card {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 1rem;
    border: 2px solid #e0e0e0;
    border-radius: 6px;
    transition: all 0.3s ease;
    background: white;
}

.ticket-type-option input:checked + .ticket-type-card {
    border-color: #2196F3;
    background-color: #e3f2fd;
}

.ticket-type-card:hover {
    border-color: #2196F3;
}

.ticket-type-icon {
    font-size: 1.5rem;
}

.ticket-type-info {
    display: flex;
    flex-direction: column;
}

.ticket-type-name {
    font-weight: 600;
    font-size: 0.9rem;
}

.ticket-type-price {
    font-size: 0.8rem;
    color: #2196F3;
    font-weight: 600;
}

.quantity-selector {
    display: flex;
    align-items: center;
    gap: 1rem;
    width: fit-content;
}

.qty-btn {
    width: 40px;
    height: 40px;
    border: 1px solid #ddd;
    background: white;
    border-radius: 4px;
    cursor: pointer;
    font-weight: 600;
    font-size: 1.1rem;
    transition: all 0.3s ease;
}

.qty-btn:hover {
    border-color: #2196F3;
    color: #2196F3;
}

.qty-input {
    width: 60px;
    height: 40px;
    border: 1px solid #ddd;
    border-radius: 4px;
    text-align: center;
    font-size: 1rem;
    font-weight: 600;
}

.info-box {
    background-color: #f5f5f5;
    border-left: 4px solid #2196F3;
    padding: 1rem;
    margin-bottom: 2rem;
    border-radius: 4px;
}

.info-box__title {
    font-weight: 600;
    margin: 0 0 0.75rem 0;
}

.info-box__list {
    margin: 0;
    padding-left: 1.5rem;
    font-size: 0.9rem;
}

.info-box__list li {
    margin-bottom: 0.5rem;
    color: #555;
}

.order-summary {
    background: white;
    border: 1px solid #e0e0e0;
    border-radius: 6px;
    padding: 1.5rem;
    margin-bottom: 2rem;
}

.summary-title {
    font-weight: 600;
    margin: 0 0 1rem 0;
}

.summary-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem 0;
}

.summary-label {
    color: #666;
}

.summary-value {
    font-weight: 600;
}

.summary-divider {
    height: 1px;
    background-color: #e0e0e0;
    margin: 1rem 0;
}

.summary-total {
    font-size: 1.1rem;
}

.summary-total .summary-value {
    color: #2196F3;
    font-size: 1.25rem;
}

.form-actions {
    display: flex;
    gap: 1rem;
}

.btn {
    padding: 0.75rem 1.5rem;
    border: none;
    border-radius: 6px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 1rem;
}

.btn-primary {
    background-color: #2196F3;
    color: white;
    flex: 1;
}

.btn-primary:hover {
    background-color: #1976D2;
}

.btn-secondary {
    background-color: #f0f0f0;
    color: #333;
    border: 1px solid #ddd;
}

.btn-secondary:hover {
    background-color: #e0e0e0;
}

/* Calendar Picker Styles */
.calendar-picker-container {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 2rem;
    margin-bottom: 2rem;
}

.calendar-picker {
    background: white;
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 1.5rem;
}

.calendar-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
}

.calendar-month-year {
    font-size: 1.2rem;
    font-weight: 600;
    color: #333;
    margin: 0;
    min-width: 200px;
    text-align: center;
}

.calendar-nav-btn {
    padding: 0.5rem 1rem;
    background-color: #f5f5f5;
    border: 1px solid #ddd;
    border-radius: 4px;
    cursor: pointer;
    font-size: 0.95rem;
    font-weight: 500;
    transition: all 0.3s ease;
}

.calendar-nav-btn:hover {
    background-color: #2196F3;
    color: white;
    border-color: #2196F3;
}

.calendar-days {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 0.5rem;
}

.day-header {
    font-weight: 600;
    text-align: center;
    padding: 0.75rem;
    color: #666;
    font-size: 0.9rem;
    border-bottom: 2px solid #f0f0f0;
}

.calendar-day-cells {
    display: contents;
}

.calendar-day {
    aspect-ratio: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 1px solid #f0f0f0;
    border-radius: 4px;
    background-color: white;
    cursor: pointer;
    font-weight: 500;
    transition: all 0.3s ease;
    font-size: 0.9rem;
}

.calendar-day:hover:not(.disabled):not(.empty) {
    background-color: #e3f2fd;
    border-color: #2196F3;
}

.calendar-day.available {
    background-color: #f0f8f0;
    border-color: #4caf50;
    color: #2d5f2d;
    font-weight: 600;
}

.calendar-day.available:hover {
    background-color: #c8e6c9;
    border-color: #4caf50;
}

.calendar-day.selected {
    background-color: #2196F3;
    color: white;
    border-color: #1976d2;
}

.calendar-day.disabled {
    background-color: #fafafa;
    color: #ccc;
    cursor: not-allowed;
    border-color: #f0f0f0;
}

.calendar-day.empty {
    cursor: default;
    background-color: transparent;
    border: none;
}

.selected-date-info {
    background: white;
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 1.5rem;
    height: fit-content;
}

.selected-date-info h3 {
    font-size: 1rem;
    font-weight: 600;
    margin: 0 0 1rem 0;
    color: #333;
}

.date-display {
    padding: 1rem;
    background-color: #f5f5f5;
    border-radius: 4px;
    text-align: center;
}

.date-display p {
    margin: 0;
    font-size: 1.1rem;
    color: #2196F3;
    font-weight: 600;
}

.no-date-message {
    color: #999;
    font-style: italic;
    text-align: center;
    padding: 2rem;
    background-color: #f5f5f5;
    border-radius: 4px;
    margin: 0;
}

.required-indicator {
    font-size: 0.85rem;
    color: #999;
    font-weight: normal;
    margin-left: 0.5rem;
}

@media (max-width: 1024px) {
    .calendar-picker-container {
        grid-template-columns: 1fr;
    }
    
    .selected-date-info {
        height: auto;
    }
}

@media (max-width: 768px) {
    .ticket-types-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .purchase-form {
        padding: 1rem;
    }
    
    .form-actions {
        flex-direction: column;
    }
    
    .calendar-day {
        font-size: 0.8rem;
    }
}
</style>

<script>
// ========================================
// Calendar and Date Picker Functionality
// ========================================

let availableDatesData = [];
let currentMonth = new Date();
let selectedVisitScheduleId = null;
let selectedDateValue = null;

// Initialize calendar on page load
document.addEventListener('DOMContentLoaded', function() {
    loadAvailableDates();
    setupCalendarNavigation();
    renderCalendar();
    setupQuantityControls();
});

// Fetch available dates from API
function loadAvailableDates() {
    fetch('/admin/api/tickets/available-dates')
        .then(response => response.json())
        .then(data => {
            availableDatesData = data;
            renderCalendar();
        })
        .catch(error => {
            console.error('Error loading available dates:', error);
            document.getElementById('calendarDays').innerHTML = '<p class="text-danger">Error loading dates</p>';
        });
}

// Setup calendar navigation buttons
function setupCalendarNavigation() {
    document.getElementById('prevMonth').addEventListener('click', function(e) {
        e.preventDefault();
        currentMonth.setMonth(currentMonth.getMonth() - 1);
        renderCalendar();
    });
    
    document.getElementById('nextMonth').addEventListener('click', function(e) {
        e.preventDefault();
        currentMonth.setMonth(currentMonth.getMonth() + 1);
        renderCalendar();
    });
}

// Render calendar based on current month
function renderCalendar() {
    const year = currentMonth.getFullYear();
    const month = currentMonth.getMonth();
    
    // Update header with month and year
    const monthNames = ['January', 'February', 'March', 'April', 'May', 'June',
                       'July', 'August', 'September', 'October', 'November', 'December'];
    document.getElementById('monthYear').textContent = monthNames[month] + ' ' + year;
    
    // Get first day of month and number of days
    const firstDay = new Date(year, month, 1).getDay();
    const daysInMonth = new Date(year, month + 1, 0).getDate();
    
    // Clear calendar
    const calendarDaysContainer = document.getElementById('calendarDays');
    calendarDaysContainer.innerHTML = '';
    
    // Add empty cells for days before month starts
    for (let i = 0; i < firstDay; i++) {
        const emptyDay = document.createElement('div');
        emptyDay.className = 'calendar-day empty';
        calendarDaysContainer.appendChild(emptyDay);
    }
    
    // Add day cells
    for (let day = 1; day <= daysInMonth; day++) {
        const dateString = year + '-' + String(month + 1).padStart(2, '0') + '-' + String(day).padStart(2, '0');
        const availableDate = availableDatesData.find(d => d.visit_date === dateString);
        
        const dayCell = document.createElement('div');
        dayCell.className = 'calendar-day';
        dayCell.textContent = day;
        
        if (availableDate && availableDate.is_available) {
            dayCell.classList.add('available');
            dayCell.title = availableDate.available_count + ' tickets available';
            dayCell.addEventListener('click', function() {
                selectDate(availableDate);
            });
        } else if (new Date(dateString) < new Date(new Date().toISOString().split('T')[0])) {
            // Disable past dates
            dayCell.classList.add('disabled');
            dayCell.title = 'Past date';
        } else {
            dayCell.classList.add('disabled');
            dayCell.title = 'No tickets available';
        }
        
        if (dateString === selectedDateValue) {
            dayCell.classList.add('selected');
        }
        
        calendarDaysContainer.appendChild(dayCell);
    }
}

// Handle date selection
function selectDate(dateInfo) {
    selectedVisitScheduleId = dateInfo.visit_schedule_id;
    selectedDateValue = dateInfo.visit_date;
    
    // Update hidden inputs
    document.getElementById('selectedVisitScheduleId').value = selectedVisitScheduleId;
    document.getElementById('selectedDateValue').value = selectedDateValue;
    
    // Update date display
    document.getElementById('selectedDateText').textContent = 
        dateInfo.display_date + ' (' + dateInfo.day_of_week + ')';
    
    // Load ticket types for this date
    loadTicketTypesForDate(selectedVisitScheduleId);
    
    // Re-render calendar to highlight selected date
    renderCalendar();
}

// Load ticket types available for selected date
function loadTicketTypesForDate(visitScheduleId) {
    fetch('/admin/api/tickets/types-for-date/' + visitScheduleId)
        .then(response => response.json())
        .then(ticketTypes => {
            renderTicketTypes(ticketTypes);
        })
        .catch(error => {
            console.error('Error loading ticket types:', error);
            document.getElementById('ticketTypesContainer').innerHTML = 
                '<p class="text-danger">Error loading ticket types</p>';
        });
}

// Render available ticket types
function renderTicketTypes(ticketTypes) {
    const container = document.getElementById('ticketTypesContainer');
    container.innerHTML = '';
    
    if (!ticketTypes || ticketTypes.length === 0) {
        container.innerHTML = '<p class="text-warning">No ticket types available for this date</p>';
        return;
    }
    
    ticketTypes.forEach((type, index) => {
        const label = document.createElement('label');
        label.className = 'ticket-type-option';
        
        const input = document.createElement('input');
        input.type = 'radio';
        input.name = 'ticketType';
        input.value = type.ticket_type_id;
        input.setAttribute('data-price', type.base_price);
        input.setAttribute('data-name', type.ticket_type_name);
        if (index === 0) input.checked = true;
        input.addEventListener('change', updateSummary);
        
        const span = document.createElement('span');
        span.className = 'ticket-type-card';
        span.innerHTML = `
            <span class="ticket-type-icon">🎫</span>
            <span class="ticket-type-info">
                <span class="ticket-type-name">${type.ticket_type_name}</span>
                <span class="ticket-type-price">${type.formatted_price}</span>
            </span>
        `;
        
        label.appendChild(input);
        label.appendChild(span);
        container.appendChild(label);
    });
    
    // Update summary after loading ticket types
    updateSummary();
}

// ========================================
// Quantity and Summary Functionality
// ========================================

function setupQuantityControls() {
    const increaseBtn = document.querySelector('.qty-btn:nth-of-type(1)');
    const decreaseBtn = document.querySelector('.qty-btn:nth-of-type(2)');
    
    if (increaseBtn) increaseBtn.addEventListener('click', increaseQty);
    if (decreaseBtn) decreaseBtn.addEventListener('click', decreaseQty);
}

function increaseQty() {
    const qty = document.getElementById('qty');
    if (qty.value < 10) {
        qty.value = parseInt(qty.value) + 1;
        updateSummary();
    }
}

function decreaseQty() {
    const qty = document.getElementById('qty');
    if (qty.value > 1) {
        qty.value = parseInt(qty.value) - 1;
        updateSummary();
    }
}

function updateSummary() {
    const radioButton = document.querySelector('input[name="ticketType"]:checked');
    
    if (!radioButton) {
        document.getElementById('summaryType').textContent = 'None selected';
        document.getElementById('summaryQty').textContent = '0';
        document.getElementById('summaryTotal').textContent = '$0.00';
        return;
    }
    
    const qty = document.getElementById('qty').value;
    const price = parseFloat(radioButton.getAttribute('data-price')) || 0;
    const typeName = radioButton.getAttribute('data-name') || 'Unknown';
    
    const total = price * qty;
    
    document.getElementById('summaryType').textContent = typeName;
    document.getElementById('summaryQty').textContent = qty;
    document.getElementById('summaryTotal').textContent = '$' + total.toFixed(2);
}

// ========================================
// Form Actions
// ========================================

function resetForm() {
    document.getElementById('ticketPurchaseForm').reset();
    document.getElementById('qty').value = 1;
    selectedVisitScheduleId = null;
    selectedDateValue = null;
    document.getElementById('selectedVisitScheduleId').value = '';
    document.getElementById('selectedDateValue').value = '';
    document.getElementById('selectedDateText').textContent = 'No date selected';
    document.getElementById('ticketTypesContainer').innerHTML = 
        '<p class="no-date-message">Please select a visit date to see available ticket types.</p>';
    renderCalendar();
    updateSummary();
}
</script>
@endsection
