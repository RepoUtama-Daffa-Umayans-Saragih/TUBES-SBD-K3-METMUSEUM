@extends('admin.layout.layout')

@section('admin-title')
    Payment Management
@endsection

@section('admin-content')
<div class="payment-dashboard">
    
    <!-- Page Header -->
    <div class="payment-header">
        <div class="header-content">
            <h1 class="page-title">💳 Payment Management</h1>
            <p class="page-subtitle">Manage and track payment transactions</p>
        </div>
    </div>
    
    <!-- Filter Bar with Status Tabs -->
    <div class="payment-filter-section">
        <div class="filter-tabs">
            @foreach(['All' => 'All Payments', 'Pending' => 'Pending', 'Paid' => 'Paid', 'Failed' => 'Failed', 'Refunded' => 'Refunded'] as $status => $label)
                <form method="GET" action="{{ route('admin.payment.index') }}" style="display: inline;">
                    <button type="submit" 
                            name="status" 
                            value="{{ $status }}"
                            class="filter-tab {{ $filterStatus === $status ? 'active' : '' }}">
                        <span class="tab-label">{{ $label }}</span>
                        <span class="tab-count">
                            @if($status === 'All')
                                {{ $totalPayments }}
                            @elseif($status === 'Pending')
                                {{ $pendingCount }}
                            @elseif($status === 'Paid')
                                {{ $completedCount }}
                            @elseif($status === 'Failed')
                                {{ $failedCount }}
                            @else
                                0
                            @endif
                        </span>
                    </button>
                </form>
            @endforeach
        </div>
    </div>
    
    <!-- Analytics Cards -->
    <section class="payment-analytics-section">
        <div class="analytics-cards-grid">
            @include('admin.ticket-analytics.components.stat-card', [
                'title' => 'Total Payments',
                'value' => $totalPayments,
                'icon' => '📊',
                'trend' => 'Transactions',
                'color' => 'primary'
            ])
            
            @include('admin.ticket-analytics.components.stat-card', [
                'title' => 'Total Revenue',
                'value' => '₹' . number_format($totalRevenue, 0),
                'icon' => '💰',
                'trend' => 'Completed',
                'color' => 'success'
            ])
            
            @include('admin.ticket-analytics.components.stat-card', [
                'title' => 'Pending Amount',
                'value' => '₹' . number_format($pendingAmount, 0),
                'icon' => '⏳',
                'trend' => $pendingCount . ' pending',
                'color' => 'warning'
            ])
            
            @include('admin.ticket-analytics.components.stat-card', [
                'title' => 'Average Amount',
                'value' => '₹' . number_format($averageAmount, 0),
                'icon' => '📈',
                'trend' => 'Per transaction',
                'color' => 'info'
            ])
        </div>
    </section>
    
    <!-- Payments Table -->
    <section class="payment-table-section">
        <div class="section-header">
            <h2 class="section-title">Latest Payments</h2>
            <div class="header-actions">
                <span class="table-info">Showing {{ $paymentsList->count() }} of {{ $totalPayments }} payments</span>
            </div>
        </div>
        
        <div class="table-wrapper">
            @if($paymentsList->count() > 0)
                <table class="analytics-table payment-table">
                    <thead>
                        <tr>
                            <th class="col-payment-id">Payment ID</th>
                            <th class="col-customer">Customer</th>
                            <th class="col-ticket-type">Ticket Type</th>
                            <th class="col-amount">Amount</th>
                            <th class="col-status">Payment Status</th>
                            <th class="col-ticket-usage">Ticket Usage</th>
                            <th class="col-date">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($payments as $payment)
                            @php
                                $order = $payment->order;
                                $customer = $order->user ?? $order->guest;
                                $ticketCount = $order->tickets->where('status', '!=', 'cancelled')->count();
                                $ticketType = $order->tickets
                                    ->where('status', '!=', 'cancelled')
                                    ->first()
                                    ?->ticketAvailability
                                    ?->ticketType
                                    ?->name ?? 'N/A';
                                
                                $usageStatus = 'Pending';
                                $usageBadgeClass = 'pending';
                                $totalTickets = $ticketCount;
                                $usedTickets = $order->tickets->where('status', 'used')->count();
                                
                                if ($totalTickets === 0) {
                                    $usageStatus = 'Cancelled';
                                    $usageBadgeClass = 'danger';
                                } elseif ($usedTickets === $totalTickets) {
                                    $usageStatus = 'Used';
                                    $usageBadgeClass = 'success';
                                } elseif ($usedTickets > 0) {
                                    $usageStatus = "Used {$usedTickets}/{$totalTickets}";
                                    $usageBadgeClass = 'info';
                                }
                            @endphp
                            <tr class="table-row">
                                <td class="col-payment-id">
                                    <span class="payment-id-badge">{{ $payment->payment_id ?? 'N/A' }}</span>
                                </td>
                                <td class="col-customer">
                                    <div class="customer-cell">
                                        <div class="customer-name">{{ $customer?->name ?? 'Guest' }}</div>
                                        <div class="customer-email">{{ $customer?->email ?? 'N/A' }}</div>
                                    </div>
                                </td>
                                <td class="col-ticket-type">
                                    <span class="ticket-type-badge">{{ $ticketType }}</span>
                                </td>
                                <td class="col-amount">
                                    <span class="amount">₹{{ number_format($payment->amount, 0) }}</span>
                                </td>
                                <td class="col-status">
                                    <span class="badge badge-{{ strtolower($payment->payment_status) }}">
                                        {{ $payment->payment_status }}
                                    </span>
                                </td>
                                <td class="col-ticket-usage">
                                    <span class="badge badge-{{ $usageBadgeClass }}">
                                        {{ $usageStatus }}
                                    </span>
                                </td>
                                <td class="col-date">
                                    <span class="date">{{ $payment->created_at->format('M d, Y') }}</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                
                <!-- Pagination -->
                @if($payments->hasPages())
                    <div class="pagination-wrapper">
                        {{ $payments->links('pagination::bootstrap-5') }}
                    </div>
                @endif
            @else
                <div class="empty-state">
                    <div class="empty-icon">📭</div>
                    <h3>No payments found</h3>
                    <p>No payments match your current filter. Try selecting a different status.</p>
                </div>
            @endif
        </div>
    </section>
    
    <!-- Orders/Transactions Table -->
    <section class="orders-table-section">
        <div class="section-header">
            <h2 class="section-title">Latest Transactions</h2>
            <div class="header-actions">
                <span class="table-info">Recent orders</span>
            </div>
        </div>
        
        <div class="table-wrapper">
            @if($payments->count() > 0)
                <table class="analytics-table orders-table">
                    <thead>
                        <tr>
                            <th class="col-order-id">Order ID</th>
                            <th class="col-customer">Customer</th>
                            <th class="col-total">Total Amount</th>
                            <th class="col-payment-status">Payment Status</th>
                            <th class="col-order-status">Order Status</th>
                            <th class="col-tickets">Tickets</th>
                            <th class="col-date">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($payments->take(10) as $payment)
                            @php
                                $order = $payment->order;
                                $customer = $order->user ?? $order->guest;
                                $ticketCount = $order->tickets->where('status', '!=', 'cancelled')->count();
                            @endphp
                            <tr class="table-row">
                                <td class="col-order-id">
                                    <span class="order-id-badge">{{ $order->order_id ?? 'N/A' }}</span>
                                </td>
                                <td class="col-customer">
                                    <div class="customer-cell">
                                        <div class="customer-name">{{ $customer?->name ?? 'Guest' }}</div>
                                        <div class="customer-email">{{ $customer?->email ?? 'N/A' }}</div>
                                    </div>
                                </td>
                                <td class="col-total">
                                    <span class="amount">₹{{ number_format($order->total_amount, 0) }}</span>
                                </td>
                                <td class="col-payment-status">
                                    <span class="badge badge-{{ strtolower($payment->payment_status) }}">
                                        {{ $payment->payment_status }}
                                    </span>
                                </td>
                                <td class="col-order-status">
                                    <span class="badge badge-{{ strtolower($order->status) }}">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </td>
                                <td class="col-tickets">
                                    <span class="ticket-count">{{ $ticketCount }} ticket{{ $ticketCount !== 1 ? 's' : '' }}</span>
                                </td>
                                <td class="col-date">
                                    <span class="date">{{ $order->created_at->format('M d, Y') }}</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="empty-state">
                    <div class="empty-icon">📭</div>
                    <h3>No transactions found</h3>
                    <p>No recent transactions to display.</p>
                </div>
            @endif
        </div>
    </section>
    
</div>

@push('styles')
    @vite(['resources/css/admin/payment/index.css'])
@endpush

@push('scripts')
    @vite(['resources/js/admin/payment/index.js'])
@endpush

@endsection
