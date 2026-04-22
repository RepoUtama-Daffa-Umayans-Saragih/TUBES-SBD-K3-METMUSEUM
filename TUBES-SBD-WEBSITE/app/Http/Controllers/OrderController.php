<?php
namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Models\Order;
use App\Models\Ticket;
use App\Models\TicketAvailability;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function create()
    {
        $ticketAvailabilities = TicketAvailability::with(['ticketType', 'visitSchedule.location'])->get();

        $groupedTickets = $ticketAvailabilities->groupBy('visitSchedule.location_id')->map(function ($locationTickets) {
            return [
                'location'   => optional(optional($locationTickets->first()->visitSchedule)->location),
                'categories' => $locationTickets->groupBy(function ($item) {
                    return optional($item->ticketType)->name ?? 'General';
                })->map(function ($categoryTickets) {
                    return $categoryTickets->values();
                }),
            ];
        });

        return view('ordinary.order.create.create', compact('groupedTickets'));
    }

    public function store(StoreOrderRequest $request)
    {
        try {
            $validated = $request->validated();

            $quantity = (int) $validated['quantity'];

            $ticketAvailability = null;
            if (! empty($validated['ticket_availability_id'])) {
                $ticketAvailability = TicketAvailability::with('ticketType')->findOrFail($validated['ticket_availability_id']);
            } elseif (! empty($validated['ticket_id'])) {
                $existingTicket     = Ticket::findOrFail($validated['ticket_id']);
                $ticketAvailability = TicketAvailability::with('ticketType')->findOrFail($existingTicket->ticket_availability_id);
            }

            if (! $ticketAvailability) {
                return back()->with('error', 'Ticket availability is required.')->withInput();
            }

            $order = DB::transaction(function () use ($ticketAvailability, $quantity) {
                $order = Order::create([
                    'order_code'     => (string) Str::uuid(),
                    'user_id'        => Auth::id() ?? null,
                    'guest_id'       => null,
                    'order_date'     => now(),
                    'payment_status' => 'pending',
                    'expired_at'     => now()->addMinutes(30),
                ]);

                for ($i = 0; $i < $quantity; $i++) {
                    Ticket::create([
                        'order_id'               => $order->id,
                        'ticket_availability_id' => $ticketAvailability->id,
                        'qr_code'                => (string) Str::uuid(),
                        'status'                 => 'valid',
                        'used_at'                => null,
                    ]);
                }

                return $order;
            });

            return redirect()->route('order.show', $order->id)
                ->with('success', 'Order created successfully. Please proceed to payment.');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'An error occurred while creating your order. Please try again.')
                ->withInput();
        }
    }

    public function show($id)
    {
        try {
            $order = Order::with('tickets.ticketAvailability.visitSchedule.location')->findOrFail($id);

            return view('ordinary.order.show.show', compact('order'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            abort(404, 'Order not found');
        }
    }
}
