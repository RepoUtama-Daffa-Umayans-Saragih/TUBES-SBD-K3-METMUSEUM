
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

    <!-- Quick Stats -->
    <div class="quick-stats-grid">
        @include('admin.ticket-analytics.components.stat-card', [
            'title' => 'Adult Tickets',
            'value' => '$10',
            'icon' => '🎫',
            'trend' => 'Standard',
            'color' => 'primary'
        ])
        
        @include('admin.ticket-analytics.components.stat-card', [
            'title' => 'Senior Tickets',
            'value' => '$7',
            'icon' => '👴',
            'trend' => 'Age 65+',
            'color' => 'success'
        ])
        
        @include('admin.ticket-analytics.components.stat-card', [
            'title' => 'Disability Tickets',
            'value' => '$5',
            'icon' => '♿',
            'trend' => 'With 1 Companion',
            'color' => 'info'
        ])
        
        @include('admin.ticket-analytics.components.stat-card', [
            'title' => 'Companion Free',
            'value' => 'Free',
            'icon' => '👥',
            'trend' => 'With Disability',
            'color' => 'warning'
        ])
    </div>

    <!-- Available Dates Section -->
    <section class="tickets-dates-section">
        <h2 class="section-title">Select Visit Date</h2>
        <div class="dates-grid">
            @foreach($availableDates ?? [] as $date)
                <div class="date-card" data-date="{{ $date['date'] }}">
                    <div class="date-card__day">{{ $date['day'] }}</div>
                    <div class="date-card__date">{{ $date['display'] }}</div>
                    <div class="date-card__available">{{ $date['available'] }} available</div>
                    <button class="date-card__btn" onclick="selectDate('{{ $date['date'] }}')">Select</button>
                </div>
            @endforeach
        </div>
    </section>

    <!-- Tickets Purchase Interface -->
    <section class="purchase-section">
        <h2 class="section-title">Purchase Tickets</h2>
        
        <form id="ticketPurchaseForm" class="purchase-form">
            <!-- Ticket Type Selection -->
            <div class="form-section">
                <label class="form-label">Ticket Type</label>
                <div class="ticket-types-grid">
                    <label class="ticket-type-option">
                        <input type="radio" name="ticketType" value="adult" checked>
                        <span class="ticket-type-card">
                            <span class="ticket-type-icon">🎫</span>
                            <span class="ticket-type-info">
                                <span class="ticket-type-name">Adult</span>
                                <span class="ticket-type-price">$10</span>
                            </span>
                        </span>
                    </label>
                    
                    <label class="ticket-type-option">
                        <input type="radio" name="ticketType" value="senior">
                        <span class="ticket-type-card">
                            <span class="ticket-type-icon">👴</span>
                            <span class="ticket-type-info">
                                <span class="ticket-type-name">Senior (65+)</span>
                                <span class="ticket-type-price">$7</span>
                            </span>
                        </span>
                    </label>
                    
                    <label class="ticket-type-option">
                        <input type="radio" name="ticketType" value="disability">
                        <span class="ticket-type-card">
                            <span class="ticket-type-icon">♿</span>
                            <span class="ticket-type-info">
                                <span class="ticket-type-name">Disability</span>
                                <span class="ticket-type-price">$5 + 1 Free</span>
                            </span>
                        </span>
                    </label>
                    
                    <label class="ticket-type-option">
                        <input type="radio" name="ticketType" value="student">
                        <span class="ticket-type-card">
                            <span class="ticket-type-icon">🎓</span>
                            <span class="ticket-type-info">
                                <span class="ticket-type-name">Student</span>
                                <span class="ticket-type-price">$7</span>
                            </span>
                        </span>
                    </label>
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

.quick-stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1rem;
    margin-bottom: 2rem;
}

.dates-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
    gap: 1rem;
    margin-bottom: 2rem;
}

.date-card {
    padding: 1rem;
    border: 2px solid #e0e0e0;
    border-radius: 8px;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease;
}

.date-card:hover {
    border-color: #2196F3;
    background-color: #f5f9ff;
}

.date-card.selected {
    border-color: #2196F3;
    background-color: #e3f2fd;
}

.date-card__day {
    font-size: 0.9rem;
    color: #666;
    margin-bottom: 0.25rem;
}

.date-card__date {
    font-size: 0.95rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.date-card__available {
    font-size: 0.8rem;
    color: #2196F3;
    margin-bottom: 0.75rem;
}

.date-card__btn {
    width: 100%;
    padding: 0.5rem;
    background-color: #2196F3;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-weight: 500;
    transition: background-color 0.3s ease;
}

.date-card__btn:hover {
    background-color: #1976D2;
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
}
</style>

<script>
function selectDate(date) {
    // Implementation for date selection
    console.log('Date selected:', date);
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
    const ticketType = document.querySelector('input[name="ticketType"]:checked').value;
    const qty = document.getElementById('qty').value;
    
    const prices = {
        adult: 10,
        senior: 7,
        disability: 5,
        student: 7
    };
    
    const typeLabels = {
        adult: 'Adult',
        senior: 'Senior (65+)',
        disability: 'Disability + 1 Free',
        student: 'Student'
    };
    
    const price = prices[ticketType] || 10;
    const total = price * qty;
    
    document.getElementById('summaryType').textContent = typeLabels[ticketType];
    document.getElementById('summaryQty').textContent = qty;
    document.getElementById('summaryTotal').textContent = '$' + total.toFixed(2);
}

document.querySelectorAll('input[name="ticketType"]').forEach(radio => {
    radio.addEventListener('change', updateSummary);
});

function resetForm() {
    document.getElementById('ticketPurchaseForm').reset();
    document.getElementById('qty').value = 1;
    updateSummary();
}
</script>
@endsection
