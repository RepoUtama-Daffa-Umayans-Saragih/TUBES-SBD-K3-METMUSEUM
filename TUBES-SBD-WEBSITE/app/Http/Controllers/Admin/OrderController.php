<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Ticket;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Display orders management page
     */
    public function index()
    {
        // Get stats
        $totalOrders = Order::count();
        $pendingOrders = Order::where('order_status', 'pending_payment')->count();
        $completedOrders = Order::where('order_status', 'completed')->count();

        // Get recent orders
        $recentOrders = Order::with(['user.profile', 'guest', 'tickets', 'payment'])
            ->orderBy('order_date', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($order) {
                $customerName = 'Guest';
                if ($order->user_id && $order->user?->profile) {
                    $customerName = trim(($order->user->profile->first_name ?? '') . ' ' . ($order->user->profile->last_name ?? ''));
                } elseif ($order->guest_id && $order->guest) {
                    $customerName = trim(($order->guest->first_name ?? '') . ' ' . ($order->guest->last_name ?? ''));
                }

                return [
                    'order_id'     => $order->order_id,
                    'order_code'   => $order->order_code,
                    'customer_name' => $customerName,
                    'ticket_count' => $order->tickets->count(),
                    'total'        => $order->total_amount,
                    'status'       => $order->order_status,
                    'date'         => $order->order_date?->format('Y-m-d H:i'),
                ];
            });

        return view('admin.orders.index', [
            'title'           => 'Orders',
            'subtitle'        => 'Manage all orders',
            'activeNav'       => 'orders',
            'breadcrumbs'     => [
                ['label' => 'Dashboard', 'href' => route('admin.dashboard')],
                ['label' => 'Orders', 'isCurrent' => true],
            ],
            'totalOrders'     => $totalOrders,
            'pendingOrders'   => $pendingOrders,
            'completedOrders' => $completedOrders,
            'recentOrders'    => $recentOrders,
        ]);
    }

    /**
     * Search for ticket by QR code or Ticket ID
     * Used for real-time scanning
     */
    public function searchTicket(Request $request): JsonResponse
    {
        $search = $request->input('search', '');

        if (empty($search)) {
            return response()->json([
                'success' => false,
                'message' => 'Please enter a ticket ID or scan QR code',
            ], 400);
        }

        // Search by QR code or ticket_id
        $ticket = Ticket::where('qr_code', $search)
            ->orWhere('ticket_id', (int) $search)
            ->with(['order', 'ticketAvailability.ticketType'])
            ->first();

        if (!$ticket) {
            return response()->json([
                'success' => false,
                'message' => 'Ticket not found',
            ], 404);
        }

        // Get order details
        $order = $ticket->order;
        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found for this ticket',
            ], 404);
        }

        // Get customer info
        $customerName = 'Guest';
        $customerEmail = 'N/A';
        
        if ($order->user_id) {
            $order->load('user.profile');
            if ($order->user?->profile) {
                $customerName = trim(($order->user->profile->first_name ?? '') . ' ' . ($order->user->profile->last_name ?? ''));
            }
            $customerEmail = $order->user?->email ?? 'N/A';
        } elseif ($order->guest_id) {
            $order->load('guest');
            if ($order->guest) {
                $customerName = trim(($order->guest->first_name ?? '') . ' ' . ($order->guest->last_name ?? ''));
            }
            $customerEmail = $order->guest?->email ?? 'N/A';
        }

        // Get all tickets in this order
        $allTickets = $order->tickets->load('ticketAvailability.ticketType')->map(function ($t) {
            return [
                'ticket_id'   => $t->ticket_id,
                'qr_code'     => $t->qr_code,
                'status'      => $t->status,
                'used_at'     => $t->used_at?->format('Y-m-d H:i:s'),
                'type'        => $t->ticketAvailability?->ticketType?->ticket_type_name ?? 'Standard',
            ];
        });

        return response()->json([
            'success' => true,
            'data'    => [
                'ticket' => [
                    'ticket_id'   => $ticket->ticket_id,
                    'qr_code'     => $ticket->qr_code,
                    'status'      => $ticket->status,
                    'used_at'     => $ticket->used_at?->format('Y-m-d H:i:s'),
                    'type'        => $ticket->ticketAvailability?->ticketType?->ticket_type_name ?? 'Standard',
                    'is_used'     => $ticket->status === 'used',
                ],
                'order' => [
                    'order_id'     => $order->order_id,
                    'order_code'   => $order->order_code,
                    'customer_name' => $customerName ?: 'Guest',
                    'customer_email' => $customerEmail,
                    'order_date'   => $order->order_date?->format('Y-m-d H:i:s'),
                    'total_amount' => number_format($order->total_amount, 2),
                    'status'       => $order->order_status,
                ],
                'all_tickets' => $allTickets,
            ],
        ]);
    }

    /**
     * Validate (mark as used) a ticket
     */
    public function validateTicket(Request $request): JsonResponse
    {
        $ticketId = $request->input('ticket_id');

        if (!$ticketId) {
            return response()->json([
                'success' => false,
                'message' => 'Ticket ID is required',
            ], 400);
        }

        $ticket = Ticket::find($ticketId);

        if (!$ticket) {
            return response()->json([
                'success' => false,
                'message' => 'Ticket not found',
            ], 404);
        }

        // Check if already used
        if ($ticket->status === 'used') {
            return response()->json([
                'success' => false,
                'message' => 'This ticket has already been used',
                'already_used' => true,
            ], 409);
        }

        // Check if cancelled
        if ($ticket->status === 'cancelled') {
            return response()->json([
                'success' => false,
                'message' => 'This ticket has been cancelled',
            ], 409);
        }

        // Mark ticket as used
        $ticket->update([
            'status'  => 'used',
            'used_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Ticket successfully validated and marked as used',
            'data'    => [
                'ticket_id' => $ticket->ticket_id,
                'status'    => $ticket->status,
                'used_at'   => $ticket->used_at?->format('Y-m-d H:i:s'),
            ],
        ]);
    }

    /**
     * Show the form for creating a new order
     */
    public function create()
    {
        return view('admin.orders.form', [
            'title' => 'Create Order',
            'subtitle' => 'Add a new order',
            'activeNav' => 'orders',
            'breadcrumbs' => [
                ['label' => 'Dashboard', 'href' => route('admin.dashboard')],
                ['label' => 'Orders', 'href' => route('admin.orders.index')],
                ['label' => 'Create', 'isCurrent' => true],
            ],
            'order' => null,
            'isEdit' => false,
            'users' => \App\Models\User::orderBy('email')->get(),
            'guests' => \App\Models\Guest::orderBy('email')->get(),
            'order_types' => ['ticket' => 'Ticket', 'membership' => 'Membership'],
        ]);
    }

    /**
     * Store a newly created order
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'order_code' => 'required|string|unique:orders,order_code',
            'user_id' => 'nullable|exists:users,user_id',
            'guest_id' => 'nullable|exists:guests,guest_id',
            'order_date' => 'required|date',
            'expired_at' => 'nullable|date|after_or_equal:order_date',
            'total_amount' => 'required|numeric|min:0',
            'order_type' => 'required|in:ticket,membership',
        ]);

        // Ensure either user_id or guest_id is provided
        if (!$validated['user_id'] && !$validated['guest_id']) {
            return back()->withInput()->withErrors(['user_id' => 'Either a user or guest must be selected']);
        }

        try {
            $order = Order::create($validated);
            
            return redirect()->route('admin.orders.show', $order->order_id)
                ->with('success', 'Order created successfully');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error creating order: ' . $e->getMessage());
        }
    }

    /**
     * Show the specified order
     */
    public function show(Order $order)
    {
        $order->load(['user.profile', 'guest', 'tickets.ticketAvailability.ticketType', 'payments', 'membership']);
        
        return view('admin.orders.show', [
            'title' => 'Order Details',
            'subtitle' => 'View order information',
            'activeNav' => 'orders',
            'breadcrumbs' => [
                ['label' => 'Dashboard', 'href' => route('admin.dashboard')],
                ['label' => 'Orders', 'href' => route('admin.orders.index')],
                ['label' => $order->order_code, 'isCurrent' => true],
            ],
            'order' => $order,
        ]);
    }

    /**
     * Show the form for editing the specified order
     */
    public function edit(Order $order)
    {
        $order->load(['user', 'guest']);
        
        return view('admin.orders.form', [
            'title' => 'Edit Order',
            'subtitle' => 'Modify order details',
            'activeNav' => 'orders',
            'breadcrumbs' => [
                ['label' => 'Dashboard', 'href' => route('admin.dashboard')],
                ['label' => 'Orders', 'href' => route('admin.orders.index')],
                ['label' => 'Edit', 'isCurrent' => true],
            ],
            'order' => $order,
            'isEdit' => true,
            'users' => \App\Models\User::orderBy('email')->get(),
            'guests' => \App\Models\Guest::orderBy('email')->get(),
            'order_types' => ['ticket' => 'Ticket', 'membership' => 'Membership'],
        ]);
    }

    /**
     * Update the specified order
     */
    public function update(Request $request, Order $order)
    {
        $validated = $request->validate([
            'order_code' => 'required|string|unique:orders,order_code,' . $order->order_id . ',order_id',
            'user_id' => 'nullable|exists:users,user_id',
            'guest_id' => 'nullable|exists:guests,guest_id',
            'order_date' => 'required|date',
            'expired_at' => 'nullable|date|after_or_equal:order_date',
            'total_amount' => 'required|numeric|min:0',
            'order_type' => 'required|in:ticket,membership',
        ]);

        // Ensure either user_id or guest_id is provided
        if (!$validated['user_id'] && !$validated['guest_id']) {
            return back()->withInput()->withErrors(['user_id' => 'Either a user or guest must be selected']);
        }

        try {
            $order->update($validated);
            
            return redirect()->route('admin.orders.show', $order->order_id)
                ->with('success', 'Order updated successfully');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error updating order: ' . $e->getMessage());
        }
    }

    /**
     * Delete the specified order
     */
    public function destroy(Order $order)
    {
        try {
            // Delete related payments
            $order->payments()->delete();
            
            // Delete related membership
            if ($order->membership) {
                $order->membership()->delete();
            }
            
            // Delete the order (tickets will be cascade deleted)
            $order->delete();

            return redirect()->route('admin.orders.index')
                ->with('success', 'Order deleted successfully');
        } catch (\Exception $e) {
            return redirect()->route('admin.orders.index')
                ->with('error', 'Error deleting order: ' . $e->getMessage());
        }
    }
}
