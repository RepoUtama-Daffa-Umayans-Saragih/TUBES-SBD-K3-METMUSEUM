<?php
namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\VisitSchedule;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TicketController extends Controller
{
    public function index(): View | JsonResponse
    {
        $schedules = VisitSchedule::with('location')
            ->orderBy('visit_date')
            ->get();

        if (request()->expectsJson()) {
            return response()->json([
                'data' => $schedules,
            ]);
        }

        return view('ordinary.admission.admission', [
            'schedules' => $schedules,
            'title'     => 'Select Visit Date',
        ]);
    }

    public function show(VisitSchedule $schedule): View | JsonResponse
    {
        $schedule->load([
            'location',
            'ticketAvailabilities.ticketType',
        ]);

        if (request()->expectsJson()) {
            return response()->json([
                'data' => $schedule,
            ]);
        }

        return view('ordinary.admission.select', [
            'schedule'             => $schedule,
            'ticketAvailabilities' => $schedule->ticketAvailabilities,
            'title'                => 'Select Tickets',
        ]);
    }

    public function scan(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'qr_code' => ['required', 'string'],
        ]);

        $ticket = Ticket::with([
            'ticketAvailability.ticketType',
            'ticketAvailability.visitSchedule.location',
        ])->where('qr_code', $validated['qr_code'])->first();

        if (! $ticket) {
            return response()->json([
                'message' => 'Ticket not found.',
            ], 404);
        }

        if ($ticket->status === 'used') {
            return response()->json([
                'message' => 'Ticket has already been used.',
                'data'    => $ticket,
            ], 422);
        }

        $ticket->update([
            'status'  => 'used',
            'used_at' => now(),
        ]);

        return response()->json([
            'message' => 'Ticket check-in successful.',
            'data'    => $ticket->fresh([
                'ticketAvailability.ticketType',
                'ticketAvailability.visitSchedule.location',
            ]),
        ]);
    }
}
