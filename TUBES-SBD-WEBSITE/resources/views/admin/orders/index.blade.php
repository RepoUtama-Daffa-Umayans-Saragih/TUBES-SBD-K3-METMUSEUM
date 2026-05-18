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
        <h2 class="section-title">Scan QR Ticket or Enter Ticket ID</h2>
        <div class="scan-interface">
            <div class="scan-input-area">
                <div class="scan-icon">📱</div>
                <p class="scan-label">Scan ticket QR code or enter ticket ID manually</p>
                <input 
                    type="text" 
                    id="scanInput" 
                    class="scan-input" 
                    placeholder="Scan QR code or enter ticket ID..."
                    autofocus
                >
                <p class="scan-hint">💡 Press Enter after scanning or typing</p>
                <div id="searchStatus" class="search-status" style="display: none;"></div>
            </div>
        </div>
    </section>

    <!-- Scan Result / Order Details -->
    <section class="order-details-section" id="orderDetailsSection" style="display: none;">
        <h2 class="section-title">Order Details</h2>
        <div class="order-card">
            <div class="order-header">
                <div>
                    <div class="order-id">Order #<span id="detailOrderId">-</span></div>
                    <div class="order-code" id="detailOrderCode">-</div>
                </div>
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

            <!-- Current Ticket Info -->
            <div class="current-ticket-section">
                <h3 class="tickets-header">📌 Current Ticket</h3>
                <div class="current-ticket-card">
                    <div class="ticket-detail-grid">
                        <div class="ticket-detail-item">
                            <span class="detail-label">Ticket ID</span>
                            <span class="detail-value" id="currentTicketId">-</span>
                        </div>
                        <div class="ticket-detail-item">
                            <span class="detail-label">QR Code</span>
                            <span class="detail-value mono" id="currentQrCode">-</span>
                        </div>
                        <div class="ticket-detail-item">
                            <span class="detail-label">Ticket Type</span>
                            <span class="detail-value" id="currentTicketType">-</span>
                        </div>
                        <div class="ticket-detail-item">
                            <span class="detail-label">Status</span>
                            <span class="detail-value">
                                <span id="currentTicketStatus" class="ticket-status-badge">-</span>
                            </span>
                        </div>
                    </div>

                    <!-- Validation Actions -->
                    <div class="validation-actions" id="validationActions">
                        <button class="btn btn-validate" id="validateBtn" onclick="validateCurrentTicket()">
                            ✓ Mark as USED
                        </button>
                        <button class="btn btn-secondary" onclick="resetScan()">
                            Clear & Scan Another
                        </button>
                    </div>
                </div>
            </div>

            <!-- All Tickets in Order -->
            <h3 class="tickets-header">🎟️ All Tickets in Order</h3>
            <div class="tickets-list" id="ticketsList">
                <!-- Tickets will be populated here -->
            </div>

            <!-- Scanning Progress -->
            <div class="scan-progress">
                <h3 class="progress-label">Validation Progress</h3>
                <div class="progress-bar">
                    <div class="progress-fill" id="progressFill" style="width: 0%"></div>
                </div>
                <p class="progress-text"><span id="usedCount">0</span> / <span id="totalTickets">0</span> tickets validated</p>
            </div>
        </div>
    </section>

    <!-- Recent Orders Table -->
    <section class="orders-table-section">
        <h2 class="section-title">Recent Orders</h2>
        <div class="table-wrapper">
            <table class="orders-table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Code</th>
                        <th>Customer</th>
                        <th>Tickets</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentOrders ?? [] as $order)
                        <tr>
                            <td><strong>#{{ $order['order_id'] ?? 'N/A' }}</strong></td>
                            <td><code>{{ $order['order_code'] ?? 'N/A' }}</code></td>
                            <td>{{ $order['customer_name'] ?? 'Guest' }}</td>
                            <td><span class="ticket-badge">{{ $order['ticket_count'] ?? 0 }}</span></td>
                            <td>${{ $order['total'] ?? '0.00' }}</td>
                            <td>
                                <span class="status-badge status-{{ strtolower($order['status'] ?? 'pending') }}">
                                    {{ ucfirst($order['status'] ?? 'pending') }}
                                </span>
                            </td>
                            <td>{{ $order['date'] ?? 'N/A' }}</td>
                            <td>
                                <button class="action-btn" onclick="focusAndSearch('{{ $order['order_id'] ?? '' }}')">
                                    Scan Order
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted">No orders found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>

<style>
* {
    box-sizing: border-box;
}

.orders-section {
    max-width: 1200px;
    margin: 0 auto;
    padding: 2rem 1rem;
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

/* Scan Section */
.scan-section {
    margin-bottom: 2rem;
}

.scan-interface {
    background: white;
    border: 2px solid #e0e0e0;
    border-radius: 12px;
    padding: 3rem 2rem;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
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
    font-weight: 500;
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
    transition: all 0.3s ease;
}

.scan-input:focus {
    border-color: #1976D2;
    box-shadow: 0 0 0 3px rgba(33, 150, 243, 0.1);
}

.scan-input:disabled {
    background-color: #f5f5f5;
    cursor: not-allowed;
}

.scan-hint {
    font-size: 0.85rem;
    color: #999;
    margin-top: 0.5rem;
}

.search-status {
    margin-top: 1rem;
    padding: 0.75rem 1rem;
    border-radius: 6px;
    font-weight: 500;
    font-size: 0.9rem;
}

.search-status.loading {
    background-color: #e3f2fd;
    color: #1976D2;
}

.search-status.error {
    background-color: #ffebee;
    color: #c62828;
}

.search-status.success {
    background-color: #e8f5e9;
    color: #2e7d32;
}

/* Order Details Section */
.order-details-section {
    margin-bottom: 2rem;
}

.order-card {
    background: white;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    padding: 2rem;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

.order-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid #f0f0f0;
}

.order-id {
    font-size: 1.25rem;
    font-weight: 700;
    color: #333;
}

.order-code {
    font-size: 0.85rem;
    color: #999;
    font-family: 'Monaco', 'Courier New', monospace;
    margin-top: 0.25rem;
}

.order-status {
    padding: 0.5rem 1rem;
    background-color: #fff3cd;
    color: #856404;
    border-radius: 4px;
    font-weight: 600;
    font-size: 0.9rem;
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
    letter-spacing: 0.5px;
    margin-bottom: 0.25rem;
}

.info-value {
    font-size: 1rem;
    font-weight: 600;
    color: #333;
}

/* Current Ticket Section */
.current-ticket-section {
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    padding: 1.5rem;
    border-radius: 8px;
    margin-bottom: 2rem;
}

.current-ticket-card {
    background: white;
    padding: 1.5rem;
    border-radius: 6px;
    border-left: 4px solid #2196F3;
}

.ticket-detail-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.5rem;
    margin-bottom: 1.5rem;
}

.ticket-detail-item {
    display: flex;
    flex-direction: column;
}

.detail-label {
    font-size: 0.75rem;
    color: #999;
    text-transform: uppercase;
    font-weight: 600;
    letter-spacing: 0.5px;
    margin-bottom: 0.25rem;
}

.detail-value {
    font-size: 1rem;
    font-weight: 600;
    color: #333;
}

.detail-value.mono {
    font-family: 'Monaco', 'Courier New', monospace;
    color: #1976D2;
}

.ticket-status-badge {
    display: inline-block;
    padding: 0.4rem 0.8rem;
    border-radius: 4px;
    font-size: 0.85rem;
    font-weight: 600;
}

.ticket-status-badge.valid {
    background-color: #c8e6c9;
    color: #2e7d32;
}

.ticket-status-badge.used {
    background-color: #ffcdd2;
    color: #c62828;
}

.ticket-status-badge.cancelled {
    background-color: #f5f5f5;
    color: #616161;
}

/* Validation Actions */
.validation-actions {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
}

.btn {
    flex: 1;
    min-width: 150px;
    padding: 0.75rem 1.5rem;
    border: none;
    border-radius: 6px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 0.95rem;
}

.btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.btn-validate {
    background-color: #4CAF50;
    color: white;
}

.btn-validate:hover:not(:disabled) {
    background-color: #45a049;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(76, 175, 80, 0.3);
}

.btn-secondary {
    background-color: #f0f0f0;
    color: #333;
    border: 1px solid #ddd;
}

.btn-secondary:hover:not(:disabled) {
    background-color: #e0e0e0;
}

/* Tickets Section */
.tickets-header {
    font-weight: 600;
    margin: 2rem 0 1rem 0;
    color: #333;
    font-size: 1rem;
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
    transition: all 0.3s ease;
}

.ticket-item.used {
    background-color: #e8f5e9;
    border-left-color: #4CAF50;
}

.ticket-item.cancelled {
    background-color: #fafafa;
    border-left-color: #bdbdbd;
    opacity: 0.7;
}

.ticket-info {
    display: flex;
    align-items: center;
    gap: 1rem;
    flex: 1;
}

.ticket-type {
    font-weight: 600;
    color: #333;
}

.ticket-code-small {
    font-size: 0.8rem;
    color: #999;
    font-family: 'Monaco', 'Courier New', monospace;
}

.ticket-status {
    padding: 0.25rem 0.75rem;
    border-radius: 4px;
    font-size: 0.8rem;
    font-weight: 600;
}

.ticket-status.valid {
    background-color: #c8e6c9;
    color: #2e7d32;
}

.ticket-status.used {
    background-color: #ffcdd2;
    color: #c62828;
}

.ticket-status.cancelled {
    background-color: #eeeeee;
    color: #616161;
}

/* Scanning Progress */
.scan-progress {
    background-color: #f5f5f5;
    padding: 1.5rem;
    border-radius: 6px;
    margin-top: 2rem;
}

.progress-label {
    font-weight: 600;
    margin: 0 0 0.75rem 0;
    color: #333;
}

.progress-bar {
    width: 100%;
    height: 28px;
    background-color: #e0e0e0;
    border-radius: 14px;
    overflow: hidden;
    margin-bottom: 0.75rem;
}

.progress-fill {
    height: 100%;
    background: linear-gradient(90deg, #4CAF50, #45a049);
    transition: width 0.5s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 600;
    font-size: 0.8rem;
    min-width: 30px;
}

.progress-text {
    margin: 0;
    text-align: center;
    font-weight: 600;
    color: #333;
    font-size: 0.9rem;
}

/* Orders Table */
.orders-table-section {
    margin-top: 3rem;
}

.table-wrapper {
    background: white;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
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
    font-size: 0.9rem;
}

.orders-table td {
    padding: 1rem;
    border-bottom: 1px solid #e0e0e0;
    font-size: 0.9rem;
}

.orders-table tbody tr:hover {
    background-color: #f9f9f9;
}

.orders-table code {
    background-color: #f5f5f5;
    padding: 0.2rem 0.4rem;
    border-radius: 3px;
    font-family: 'Monaco', 'Courier New', monospace;
    font-size: 0.85rem;
    color: #1976D2;
}

.ticket-badge {
    display: inline-block;
    background-color: #e3f2fd;
    color: #1976D2;
    padding: 0.25rem 0.75rem;
    border-radius: 12px;
    font-weight: 600;
    font-size: 0.85rem;
}

.status-badge {
    display: inline-block;
    padding: 0.35rem 0.75rem;
    border-radius: 4px;
    font-size: 0.8rem;
    font-weight: 600;
}

.status-pending_payment,
.status-pending {
    background-color: #fff3cd;
    color: #856404;
}

.status-paid,
.status-completed {
    background-color: #d4edda;
    color: #155724;
}

.status-expired,
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
    font-size: 0.85rem;
    transition: all 0.2s ease;
}

.action-btn:hover {
    background-color: #1976D2;
    transform: translateY(-1px);
    box-shadow: 0 2px 6px rgba(33, 150, 243, 0.3);
}

.text-center {
    text-align: center;
}

.text-muted {
    color: #999;
}

@media (max-width: 768px) {
    .orders-section {
        padding: 1rem 0.5rem;
    }

    .page-header h1 {
        font-size: 1.5rem;
    }

    .scan-interface {
        padding: 2rem 1rem;
    }

    .order-header {
        flex-direction: column;
        gap: 1rem;
        align-items: flex-start;
    }

    .order-info-grid {
        grid-template-columns: 1fr;
    }

    .ticket-detail-grid {
        grid-template-columns: 1fr;
    }

    .validation-actions {
        flex-direction: column;
    }

    .btn {
        width: 100%;
        min-width: unset;
    }

    .orders-table {
        font-size: 0.8rem;
    }

    .orders-table th, 
    .orders-table td {
        padding: 0.75rem 0.5rem;
    }

    .scan-input {
        max-width: 100%;
    }
}
</style>

<script>
let currentSearchData = null;
let currentTicketId = null;

// Listen for Enter key on scan input
document.getElementById('scanInput').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        searchTicket();
    }
});

/**
 * Search for ticket
 */
function searchTicket() {
    const input = document.getElementById('scanInput');
    const search = input.value.trim();

    if (!search) {
        showSearchStatus('Please enter a ticket ID or scan QR code', 'error');
        return;
    }

    // Disable input while searching
    input.disabled = true;
    showSearchStatus('Searching...', 'loading');

    // Call API
    fetch('{{ route("admin.orders.search-ticket") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ search: search })
    })
    .then(response => response.json())
    .then(data => {
        input.disabled = false;

        if (data.success) {
            showSearchStatus('✓ Ticket found!', 'success');
            currentSearchData = data.data;
            currentTicketId = data.data.ticket.ticket_id;
            displayOrderDetails(data.data);
            input.value = '';
        } else {
            showSearchStatus('✗ ' + (data.message || 'Ticket not found'), 'error');
            currentSearchData = null;
            currentTicketId = null;
        }
    })
    .catch(error => {
        input.disabled = false;
        console.error('Error:', error);
        showSearchStatus('✗ Error searching ticket. Please try again.', 'error');
    });
}

/**
 * Show search status message
 */
function showSearchStatus(message, type) {
    const statusEl = document.getElementById('searchStatus');
    statusEl.textContent = message;
    statusEl.className = 'search-status ' + type;
    statusEl.style.display = 'block';

    if (type === 'success') {
        setTimeout(() => {
            statusEl.style.display = 'none';
        }, 3000);
    }
}

/**
 * Display order and ticket details
 */
function displayOrderDetails(data) {
    const ticket = data.ticket;
    const order = data.order;
    const allTickets = data.all_tickets;

    // Show details section
    document.getElementById('orderDetailsSection').style.display = 'block';

    // Order info
    document.getElementById('detailOrderId').textContent = order.order_id;
    document.getElementById('detailOrderCode').textContent = 'Code: ' + order.order_code;
    document.getElementById('detailCustomerName').textContent = order.customer_name;
    document.getElementById('detailEmail').textContent = order.customer_email;
    document.getElementById('detailOrderDate').textContent = order.order_date;
    document.getElementById('detailTotal').textContent = '$' + order.total_amount;
    document.getElementById('detailOrderStatus').textContent = order.status.replace('_', ' ').toUpperCase();

    // Current ticket info
    document.getElementById('currentTicketId').textContent = '#' + ticket.ticket_id;
    document.getElementById('currentQrCode').textContent = ticket.qr_code;
    document.getElementById('currentTicketType').textContent = ticket.type;
    updateTicketStatusBadge('currentTicketStatus', ticket.status);

    // Validation button visibility
    const validateBtn = document.getElementById('validateBtn');
    if (ticket.is_used) {
        validateBtn.disabled = true;
        validateBtn.textContent = '⚠️ Already Used';
    } else {
        validateBtn.disabled = false;
        validateBtn.textContent = '✓ Mark as USED';
    }

    // List all tickets in order
    displayAllTickets(allTickets);

    // Update progress
    updateProgress(allTickets);

    // Scroll to details
    document.getElementById('orderDetailsSection').scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}

/**
 * Display all tickets in the order
 */
function displayAllTickets(tickets) {
    const listEl = document.getElementById('ticketsList');
    listEl.innerHTML = '';

    if (!tickets || tickets.length === 0) {
        listEl.innerHTML = '<p style="color: #999; text-align: center;">No tickets found</p>';
        return;
    }

    tickets.forEach(ticket => {
        const ticketEl = document.createElement('div');
        ticketEl.className = 'ticket-item ' + ticket.status;
        
        const statusBadge = getStatusBadgeHtml(ticket.status);
        const usedInfo = ticket.used_at ? ` (Used: ${ticket.used_at})` : '';

        ticketEl.innerHTML = `
            <div class="ticket-info">
                <div>
                    <div class="ticket-type">🎟️ ${ticket.type}</div>
                    <div class="ticket-code-small">ID: ${ticket.ticket_id} | QR: ${ticket.qr_code}</div>
                </div>
            </div>
            <div>
                <span class="ticket-status ${ticket.status}">${statusBadge}</span>
            </div>
        `;

        listEl.appendChild(ticketEl);
    });
}

/**
 * Get status badge HTML
 */
function getStatusBadgeHtml(status) {
    const badges = {
        'valid': '✓ Valid',
        'used': '✓ Used',
        'cancelled': '✗ Cancelled'
    };
    return badges[status] || status;
}

/**
 * Update ticket status badge
 */
function updateTicketStatusBadge(elementId, status) {
    const el = document.getElementById(elementId);
    el.textContent = getStatusBadgeHtml(status);
    el.className = 'ticket-status-badge ' + status;
}

/**
 * Update progress bar
 */
function updateProgress(allTickets) {
    if (!allTickets) return;

    const total = allTickets.length;
    const used = allTickets.filter(t => t.status === 'used').length;
    const percentage = total > 0 ? Math.round((used / total) * 100) : 0;

    document.getElementById('usedCount').textContent = used;
    document.getElementById('totalTickets').textContent = total;
    
    const progressFill = document.getElementById('progressFill');
    progressFill.style.width = percentage + '%';
    progressFill.textContent = percentage > 10 ? percentage + '%' : '';
}

/**
 * Validate (mark as used) current ticket
 */
function validateCurrentTicket() {
    if (!currentTicketId) {
        alert('No ticket selected');
        return;
    }

    if (!confirm('Mark this ticket as USED?')) {
        return;
    }

    const btn = document.getElementById('validateBtn');
    btn.disabled = true;
    btn.textContent = '⏳ Processing...';

    fetch('{{ route("admin.orders.validate-ticket") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ ticket_id: currentTicketId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showSearchStatus('✓ ' + data.message, 'success');
            
            // Update current ticket status
            if (currentSearchData) {
                currentSearchData.ticket.status = 'used';
                currentSearchData.ticket.used_at = data.data.used_at;
                currentSearchData.ticket.is_used = true;

                // Update display
                updateTicketStatusBadge('currentTicketStatus', 'used');
                btn.disabled = true;
                btn.textContent = '⚠️ Already Used';

                // Update all tickets list
                displayAllTickets(currentSearchData.all_tickets.map(t => {
                    if (t.ticket_id === currentTicketId) {
                        t.status = 'used';
                        t.used_at = data.data.used_at;
                    }
                    return t;
                }));

                // Update progress
                updateProgress(currentSearchData.all_tickets);
            }
        } else {
            alert('Error: ' + (data.message || 'Failed to validate ticket'));
            btn.disabled = false;
            btn.textContent = '✓ Mark as USED';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error validating ticket');
        btn.disabled = false;
        btn.textContent = '✓ Mark as USED';
    });
}

/**
 * Reset scan and clear details
 */
function resetScan() {
    document.getElementById('scanInput').value = '';
    document.getElementById('orderDetailsSection').style.display = 'none';
    document.getElementById('searchStatus').style.display = 'none';
    currentSearchData = null;
    currentTicketId = null;
    document.getElementById('scanInput').focus();
}

/**
 * Focus search input and set value
 */
function focusAndSearch(value) {
    const input = document.getElementById('scanInput');
    input.value = value;
    input.focus();
    searchTicket();
}
</script>

@endsection
