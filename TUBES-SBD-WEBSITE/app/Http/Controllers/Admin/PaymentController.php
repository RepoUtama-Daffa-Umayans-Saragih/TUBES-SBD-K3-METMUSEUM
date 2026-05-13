<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Order;
use App\Models\Ticket;
use App\Models\User;
use App\Models\Guest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PaymentController extends Controller
{
    /**
     * Display payment management dashboard
     */
    public function index(Request $request)
    {
        // Get filter status (All, Pending, Paid, Used)
        $filterStatus = $request->get('status', 'All');
        $perPage = $request->get('per_page', 15);
        $sortBy = $request->get('sort', 'created_at');
        $sortOrder = $request->get('order', 'desc');

        // Build query based on filter status
        $query = Payment::with(['order.user', 'order.guest', 'order.tickets.ticketAvailability.ticketType']);

        // Apply filter
        if ($filterStatus !== 'All' && $filterStatus !== '') {
            $query->where('payment_status', $filterStatus);
        }

        // Apply sorting
        $query->orderBy($sortBy, $sortOrder);

        // Paginate results
        $payments = $query->paginate($perPage);

        // Transform payments data
        $paymentsList = $payments->map(function ($payment) {
            $order = $payment->order;
            $customer = $order->user ?? $order->guest;
            $ticketCount = $order->tickets->where('status', '!=', 'cancelled')->count();
            
            // Get primary ticket type
            $ticketType = $order->tickets
                ->where('status', '!=', 'cancelled')
                ->first()
                ?->ticketAvailability
                ?->ticketType
                ?->name ?? 'N/A';

            return [
                'payment_id' => $payment->payment_id,
                'order_id' => $order->order_id,
                'customer_name' => $customer?->name ?? 'Guest',
                'customer_email' => $customer?->email ?? 'N/A',
                'ticket_type' => $ticketType,
                'ticket_count' => $ticketCount,
                'amount' => $payment->amount,
                'payment_status' => $payment->payment_status,
                'payment_method' => $payment->payment_method ?? 'N/A',
                'ticket_usage_status' => $this->getTicketUsageStatus($order),
                'created_at' => $payment->created_at,
                'updated_at' => $payment->updated_at,
            ];
        });

        // Analytics data
        $analytics = $this->getPaymentAnalytics($filterStatus);

        // Status options for filter
        $statusOptions = ['All', 'Pending', 'Paid', 'Failed', 'Refunded'];

        return view('admin.payment.index', [
            'payments' => $payments,
            'paymentsList' => $paymentsList,
            'filterStatus' => $filterStatus,
            'statusOptions' => $statusOptions,
            'sortBy' => $sortBy,
            'sortOrder' => $sortOrder,
            'perPage' => $perPage,
            
            // Analytics
            'totalPayments' => $analytics['total'],
            'totalRevenue' => $analytics['revenue'],
            'averageAmount' => $analytics['average'],
            'pendingAmount' => $analytics['pending'],
            'completedCount' => $analytics['completed'],
            'pendingCount' => $analytics['pending_count'],
            'failedCount' => $analytics['failed'],
            
            // Meta
            'pageTitle' => 'Payment Management',
            'pageSubtitle' => 'Manage and track payment transactions',
            'breadcrumbs' => [
                ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
                ['label' => 'Payment Management', 'isCurrent' => true],
            ],
        ]);
    }

    /**
     * Get ticket usage status
     */
    private function getTicketUsageStatus($order)
    {
        $totalTickets = $order->tickets->where('status', '!=', 'cancelled')->count();
        if ($totalTickets === 0) {
            return 'cancelled';
        }

        $usedTickets = $order->tickets->where('status', 'used')->count();
        $cancelledTickets = $order->tickets->where('status', 'cancelled')->count();

        if ($usedTickets === $totalTickets) {
            return 'used';
        } elseif ($usedTickets > 0) {
            return 'partial';
        } elseif ($cancelledTickets > 0) {
            return 'partially_cancelled';
        }

        return 'pending';
    }

    /**
     * Get payment analytics
     */
    private function getPaymentAnalytics($filterStatus = null)
    {
        $query = Payment::query();

        if ($filterStatus && $filterStatus !== 'All') {
            $query->where('payment_status', $filterStatus);
        }

        $total = $query->count();
        $revenue = $query->where('payment_status', 'Paid')->sum('amount');
        $average = $total > 0 ? $revenue / $total : 0;
        $pending = Payment::where('payment_status', 'Pending')->sum('amount');
        $completed = Payment::where('payment_status', 'Paid')->count();
        $pendingCount = Payment::where('payment_status', 'Pending')->count();
        $failed = Payment::where('payment_status', 'Failed')->count();

        return [
            'total' => $total,
            'revenue' => $revenue,
            'average' => $average,
            'pending' => $pending,
            'completed' => $completed,
            'pending_count' => $pendingCount,
            'failed' => $failed,
        ];
    }

    /**
     * Get payments as JSON (for AJAX requests)
     */
    public function getData(Request $request)
    {
        $filterStatus = $request->get('status', 'All');
        $sortBy = $request->get('sort', 'created_at');
        $sortOrder = $request->get('order', 'desc');

        $query = Payment::with(['order.user', 'order.guest']);

        if ($filterStatus !== 'All' && $filterStatus !== '') {
            $query->where('payment_status', $filterStatus);
        }

        $payments = $query->orderBy($sortBy, $sortOrder)
            ->limit(50)
            ->get();

        return response()->json([
            'success' => true,
            'payments' => $payments,
            'total' => $payments->count(),
        ]);
    }
}
