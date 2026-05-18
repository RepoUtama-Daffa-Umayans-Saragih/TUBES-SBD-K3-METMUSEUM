
@extends('admin.layout.layout')

@section('admin-title')
    Order Management & Scanning
@endsection

@section('admin-content')
<div class="orders-section">
    <!-- Page Header -->
    <div class="page-header">
        <h1>Order Management</h1>
        <p class="page-subtitle">Scan and process orders | Track ticket usage</p>
    </div>

    <!-- Quick Stats -->
    <div class="quick-stats-grid">
        @include('admin.ticket-analytics.components.stat-card', [
            'title' => 'Total Orders',
            'value' => $totalOrders ?? 0,
            'icon' => '📦',
            'trend' => 'today',
            'color' => 'primary'
        ])
        
        @include('admin.ticket-analytics.components.stat-card', [
            'title' => 'Pending Orders',
            'value' => $pendingOrders ?? 0,
            'icon' => '⏳',
            'trend' => 'awaiting scan',
            'color' => 'warning'
        ])
        
        @include('admin.ticket-analytics.components.stat-card', [
            'title' => 'Completed Orders',
            'value' => $completedOrders ?? 0,
            'icon' => '✅',
            'trend' => 'scanned',
            'color' => 'success'
        ])
    </div>

    <!-- QR Scan Interface -->
    <section class="scan-section">
        <h2 class="section-title">Scan QR Ticket</h2>
        <div class="scan-interface">
            <div class="scan-input-area">
                <div class="scan-icon">📱</div>
                <p class="scan-label">Scan ticket QR code or enter order ID manually</p>
                <input 
                    type="text" 
                    id="scanInput" 
                    class="scan-input" 
                    placeholder="Scan here or paste order ID..."
                    autofocus
                    @keyup.enter="processScannedTicket"
                >
                <p class="scan-hint">Tip: Start typing after clicking the input field</p>
            </div>
        </div>
    </section>

    <!-- Scan Result / Order Details -->
    <section class="order-details-section" id="orderDetailsSection" style="display: none;">
        <h2 class="section-title">Order Details</h2>
        <div class="order-card">
            <div class="order-header">
                <div class="order-id">Order #<span id="detailOrderId">-</span></div>
                <div class="order-status" id="detailOrderStatus">Pending</div>
            </div>

            <div class="order-info-grid">
                <div class="order-info-item">
                    <span class="info-label">Customer Name</span>
                    <span class="info-value" id="detailCustomerName">-</span>
                </div>
                <div class="order-info-item">
                    <span class="info-label">Email</span>
                    <span class="info-value" id="detailEmail">-</span>
                </div>
                <div class="order-info-item">
                    <span class="info-label">Order Date</span>
                    <span class="info-value" id="detailOrderDate">-</span>
                </div>
                <div class="order-info-item">
                    <span class="info-label">Total Amount</span>
                    <span class="info-value" id="detailTotal">-</span>
                </div>
            </div>

            <h3 class="tickets-header">Tickets in Order</h3>
            <div class="tickets-list" id="ticketsList">
                <!-- Tickets will be populated here -->
            </div>

            <div class="scan-progress">
                <h3 class="progress-label">Scanning Progress</h3>
                <div class="progress-bar">
                    <div class="progress-fill" id="progressFill" style="width: 0%"></div>
                </div>
                <p class="progress-text"><span id="scannedCount">0</span> / <span id="totalTickets">0</span> tickets scanned</p>
            </div>

            <div class="order-actions">
                <button class="btn btn-secondary" onclick="resetScan()">Clear & Scan Another</button>
                <button class="btn btn-success" id="completeBtn" onclick="completeOrder()" style="display: none;">✓ Complete Order</button>
            </div>
        </div>
    </section>

    <!-- Orders Table -->
    <section class="orders-table-section">
        <h2 class="section-title">Recent Orders</h2>
        <div class="table-wrapper">
            <table class="orders-table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Tickets</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentOrders ?? [] as $order)
                        <tr>
                            <td><strong>{{ $order['order_id'] ?? 'N/A' }}</strong></td>
                            <td>{{ $order['customer_name'] ?? 'Guest' }}</td>
                            <td>{{ $order['ticket_count'] ?? 0 }} tickets</td>
                            <td>${{ number_format($order['total'] ?? 0, 2) }}</td>
                            <td>
                                <span class="status-badge status-{{ $order['status'] ?? 'pending' }}">
                                    {{ ucfirst($order['status'] ?? 'pending') }}
                                </span>
                            </td>
                            <td>{{ $order['date'] ?? 'N/A' }}</td>
                            <td>
                                <button class="action-btn" onclick="scanOrder('{{ $order['order_id'] ?? '' }}')">Scan</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
</div>

<style>
.orders-section {
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

.scan-section {
    margin-bottom: 2rem;
}

.scan-interface {
    background: white;
    border: 2px solid #e0e0e0;
    border-radius: 12px;
    padding: 3rem 2rem;
}

.scan-input-area {
    text-align: center;
}

.scan-icon {
    font-size: 3rem;
    margin-bottom: 1rem;
}

.scan-label {
    font-size: 1rem;
    margin-bottom: 1.5rem;
    color: #666;
}

.scan-input {
    width: 100%;
    max-width: 500px;
    padding: 1rem;
    font-size: 1.1rem;
    border: 2px solid #2196F3;
    border-radius: 8px;
    text-align: center;
    outline: none;
}

.scan-input:focus {
    border-color: #1976D2;
    box-shadow: 0 0 0 3px rgba(33, 150, 243, 0.1);
}

.scan-hint {
    font-size: 0.85rem;
    color: #999;
    margin-top: 0.5rem;
}

.order-details-section {
    margin-bottom: 2rem;
}

.order-card {
    background: white;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    padding: 2rem;
}

.order-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid #e0e0e0;
}

.order-id {
    font-size: 1.25rem;
    font-weight: 700;
}

.order-status {
    padding: 0.5rem 1rem;
    background-color: #fff3cd;
    color: #856404;
    border-radius: 4px;
    font-weight: 600;
}

.order-info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.order-info-item {
    display: flex;
    flex-direction: column;
}

.info-label {
    font-size: 0.8rem;
    color: #999;
    text-transform: uppercase;
    font-weight: 600;
    margin-bottom: 0.25rem;
}

.info-value {
    font-size: 1rem;
    font-weight: 600;
}

.tickets-header {
    font-weight: 600;
    margin: 2rem 0 1rem 0;
}

.tickets-list {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
    margin-bottom: 2rem;
}

.ticket-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1rem;
    background-color: #f5f5f5;
    border-radius: 6px;
    border-left: 4px solid #2196F3;
}

.ticket-info {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.ticket-type {
    font-weight: 600;
}

.ticket-status {
    padding: 0.25rem 0.75rem;
    border-radius: 4px;
    font-size: 0.8rem;
    font-weight: 600;
}

.ticket-status.scanned {
    background-color: #d4edda;
    color: #155724;
}

.ticket-status.pending {
    background-color: #fff3cd;
    color: #856404;
}

.scan-progress {
    background-color: #f5f5f5;
    padding: 1.5rem;
    border-radius: 6px;
    margin-bottom: 1.5rem;
}

.progress-label {
    font-weight: 600;
    margin: 0 0 0.75rem 0;
}

.progress-bar {
    width: 100%;
    height: 24px;
    background-color: #e0e0e0;
    border-radius: 12px;
    overflow: hidden;
    margin-bottom: 0.75rem;
}

.progress-fill {
    height: 100%;
    background: linear-gradient(90deg, #4CAF50, #45a049);
    transition: width 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 600;
    font-size: 0.8rem;
}

.progress-text {
    margin: 0;
    text-align: center;
    font-weight: 600;
}

.order-actions {
    display: flex;
    gap: 1rem;
}

.btn {
    flex: 1;
    padding: 0.75rem 1.5rem;
    border: none;
    border-radius: 6px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-secondary {
    background-color: #f0f0f0;
    color: #333;
    border: 1px solid #ddd;
}

.btn-secondary:hover {
    background-color: #e0e0e0;
}

.btn-success {
    background-color: #4CAF50;
    color: white;
}

.btn-success:hover {
    background-color: #45a049;
}

.orders-table-section {
    margin-top: 3rem;
}

.table-wrapper {
    background: white;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    overflow: hidden;
}

.orders-table {
    width: 100%;
    border-collapse: collapse;
}

.orders-table th {
    background-color: #f5f5f5;
    padding: 1rem;
    text-align: left;
    font-weight: 600;
    color: #333;
    border-bottom: 2px solid #e0e0e0;
}

.orders-table td {
    padding: 1rem;
    border-bottom: 1px solid #e0e0e0;
}

.orders-table tbody tr:hover {
    background-color: #f9f9f9;
}

.status-badge {
    padding: 0.35rem 0.75rem;
    border-radius: 4px;
    font-size: 0.85rem;
    font-weight: 600;
}

.status-pending {
    background-color: #fff3cd;
    color: #856404;
}

.status-completed {
    background-color: #d4edda;
    color: #155724;
}

.status-cancelled {
    background-color: #f8d7da;
    color: #721c24;
}

.action-btn {
    padding: 0.5rem 1rem;
    background-color: #2196F3;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-weight: 600;
}

.action-btn:hover {
    background-color: #1976D2;
}

@media (max-width: 768px) {
    .order-header {
        flex-direction: column;
        gap: 1rem;
        align-items: flex-start;
    }
    
    .order-info-grid {
        grid-template-columns: 1fr;
    }
    
    .orders-table {
        font-size: 0.85rem;
    }
    
    .orders-table th, .orders-table td {
        padding: 0.75rem;
    }
}
</style>

<script>
function processScannedTicket() {
    const input = document.getElementById('scanInput').value.trim();
    if (input) {
        // Implementation for processing ticket
        console.log('Processing ticket:', input);
    }
}

function scanOrder(orderId) {
    document.getElementById('scanInput').value = orderId;
    document.getElementById('scanInput').focus();
    processScannedTicket();
}

function resetScan() {
    document.getElementById('scanInput').value = '';
    document.getElementById('orderDetailsSection').style.display = 'none';
    document.getElementById('scanInput').focus();
}

function completeOrder() {
    // Implementation for completing order
    console.log('Order completed');
}
</script>
@endsection
