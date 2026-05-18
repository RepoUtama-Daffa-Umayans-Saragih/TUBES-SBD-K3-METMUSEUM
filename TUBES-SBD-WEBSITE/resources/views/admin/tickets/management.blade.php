@extends('admin.layout.layout')

@section('admin-title')
    Ticket Stock Management
@endsection

@section('admin-content')
<div class="admin-page-section">
    <div class="page-header">
        <h1>Ticket Stock Management</h1>
        <p class="page-subtitle">Add stock, manage prices, and ticket types</p>
    </div>

    <!-- Quick Stats -->
    <div class="quick-stats-grid">
        @include('admin.ticket-analytics.components.stat-card', [
            'title' => 'Total Stock',
            'value' => $totalStock ?? 0,
            'icon' => '📦',
            'trend' => 'tickets',
            'color' => 'primary'
        ])
        
        @include('admin.ticket-analytics.components.stat-card', [
            'title' => 'Tickets Sold',
            'value' => $ticketsSold ?? 0,
            'icon' => '✓',
            'trend' => 'this month',
            'color' => 'success'
        ])
        
        @include('admin.ticket-analytics.components.stat-card', [
            'title' => 'Available',
            'value' => $availableStock ?? 0,
            'icon' => '✓',
            'trend' => 'in stock',
            'color' => 'info'
        ])
    </div>

    <!-- Ticket Types Management -->
    <section class="management-section">
        <h2 class="section-title">Ticket Types & Prices</h2>
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Ticket Type</th>
                        <th>Price</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ticketTypes ?? [] as $type)
                    <tr>
                        <td><strong>{{ $type->ticket_type_name }}</strong></td>
                        <td>${{ number_format($type->base_price, 2) }}</td>
                        <td>Museum admission</td>
                        <td><span class="status-badge status-active">Active</span></td>
                        <td class="actions">
                            <button class="action-btn">Edit</button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 2rem;">No ticket types available</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <!-- Daily Stock Management -->
    <section class="management-section">
        <h2 class="section-title">Daily Stock by Date</h2>
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        @foreach($ticketTypes ?? [] as $type)
                        <th>{{ $type->ticket_type_name }}</th>
                        @endforeach
                        <th>Total</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($dailyStocks ?? [] as $dayStock)
                    <tr>
                        <td><strong>{{ $dayStock['date'] }}</strong></td>
                        @foreach($dayStock['types'] ?? [] as $typeStock)
                        <td>{{ $typeStock['availability'] ?? 0 }}</td>
                        @endforeach
                        <td><strong>{{ $dayStock['total'] ?? 0 }}</strong></td>
                        <td class="actions">
                            <button class="action-btn">Update</button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="99" style="text-align: center; padding: 2rem;">No daily stocks available</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <!-- Add Stock Form -->
    <section class="management-section">
        <h2 class="section-title">Add Stock</h2>
        <form class="add-stock-form">
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Date</label>
                    <input type="date" class="form-input" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Ticket Type</label>
                    <select class="form-input" name="ticket_type_id" required>
                        <option value="">-- Select Ticket Type --</option>
                        @forelse($ticketTypes ?? [] as $type)
                        <option value="{{ $type->ticket_type_id }}">{{ $type->ticket_type_name }} (${{ number_format($type->base_price, 2) }})</option>
                        @empty
                        <option disabled>No ticket types available</option>
                        @endforelse
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Quantity</label>
                    <input type="number" class="form-input" min="1" required>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Add Stock</button>
        </form>
    </section>
</div>

<style>
.admin-page-section {
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
}

.quick-stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1rem;
    margin-bottom: 2rem;
}

.management-section {
    background: white;
    border-radius: 8px;
    padding: 1.5rem;
    margin-bottom: 2rem;
}

.table-wrapper {
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    overflow: hidden;
}

.data-table {
    width: 100%;
    border-collapse: collapse;
}

.data-table th {
    background-color: #f5f5f5;
    padding: 1rem;
    text-align: left;
    font-weight: 600;
    color: #333;
    border-bottom: 2px solid #e0e0e0;
}

.data-table td {
    padding: 1rem;
    border-bottom: 1px solid #e0e0e0;
}

.data-table tbody tr:hover {
    background-color: #f9f9f9;
}

.status-badge {
    padding: 0.35rem 0.75rem;
    border-radius: 4px;
    font-size: 0.85rem;
    font-weight: 600;
}

.status-active {
    background-color: #d4edda;
    color: #155724;
}

.status-inactive {
    background-color: #f8d7da;
    color: #721c24;
}

.actions {
    display: flex;
    gap: 0.5rem;
}

.action-btn {
    padding: 0.4rem 0.8rem;
    border: 1px solid #ddd;
    background: white;
    border-radius: 4px;
    cursor: pointer;
    font-weight: 600;
    font-size: 0.8rem;
    transition: all 0.3s ease;
}

.action-btn:hover {
    border-color: #2196F3;
    color: #2196F3;
}

.add-stock-form {
    background-color: #f9f9f9;
    padding: 1.5rem;
    border-radius: 6px;
}

.form-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-bottom: 1rem;
}

.form-group {
    display: flex;
    flex-direction: column;
}

.form-label {
    font-weight: 600;
    margin-bottom: 0.5rem;
    color: #333;
}

.form-input {
    padding: 0.75rem;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 0.95rem;
}

.form-input:focus {
    outline: none;
    border-color: #2196F3;
    box-shadow: 0 0 0 3px rgba(33, 150, 243, 0.1);
}

.btn {
    padding: 0.75rem 1.5rem;
    border: none;
    border-radius: 6px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-primary {
    background-color: #2196F3;
    color: white;
}

.btn-primary:hover {
    background-color: #1976D2;
}

@media (max-width: 768px) {
    .form-row {
        grid-template-columns: 1fr;
    }
}
</style>
@endsection