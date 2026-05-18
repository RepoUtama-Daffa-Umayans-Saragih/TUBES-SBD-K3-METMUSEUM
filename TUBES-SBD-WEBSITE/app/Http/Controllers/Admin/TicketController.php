<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TicketType;
use App\Models\VisitSchedule;
use App\Models\TicketAvailability;
use App\Models\Ticket;

class TicketController extends Controller
{
    public function index()
    {
        // Load ticket types from database
        $ticketTypes = TicketType::where('deleted_at', null)
            ->orderBy('ticket_type_name')
            ->get();

        // Load visit schedules with availability (no soft deletes on this table)
        $visitSchedules = VisitSchedule::orderBy('visit_date')
            ->limit(30)
            ->get();

        // Calculate available dates with ticket availability
        $availableDates = $this->getAvailableDatesFromDB($visitSchedules);

        return view('admin.tickets.index', [
            'title'       => 'Ticket Sales',
            'subtitle'    => 'Point-of-sale interface for onsite ticket purchases',
            'activeNav'   => 'tickets',
            'ticketTypes' => $ticketTypes,
            'availableDates' => $availableDates,
            'visitSchedules' => $visitSchedules,
            'breadcrumbs' => [
                ['label' => 'Dashboard', 'href' => route('admin.dashboard')],
                ['label' => 'Tickets', 'isCurrent' => true],
            ],
        ]);
    }

    public function management()
    {
        // Load ticket types from database
        $ticketTypes = TicketType::where('deleted_at', null)
            ->orderBy('ticket_type_name')
            ->get();

        // Load ticket availability data
        $ticketAvailability = TicketAvailability::with(['ticketType', 'visitSchedule'])
            ->orderBy('visit_schedule_id')
            ->get();

        // Calculate stock statistics
        $totalTickets = Ticket::count();
        $soldTickets = Ticket::where('status', '!=', 'valid')->count();
        $availableTickets = TicketAvailability::count() - $totalTickets;

        // Get daily stocks organized by date
        $dailyStocks = $this->getDailyStocks($ticketTypes);

        return view('admin.tickets.management', [
            'title'           => 'Ticket Management',
            'subtitle'        => 'Manage ticket stock and prices',
            'activeNav'       => 'tickets',
            'ticketTypes'     => $ticketTypes,
            'totalStock'      => $totalTickets,
            'ticketsSold'     => $soldTickets,
            'availableStock'  => $availableTickets,
            'dailyStocks'     => $dailyStocks,
            'ticketAvailability' => $ticketAvailability,
            'breadcrumbs'     => [
                ['label' => 'Dashboard', 'href' => route('admin.dashboard')],
                ['label' => 'Ticket Management', 'isCurrent' => true],
            ],
        ]);
    }

    /**
     * Get available dates from database visit schedules
     */
    private function getAvailableDatesFromDB($visitSchedules)
    {
        $dates = [];
        
        foreach ($visitSchedules as $schedule) {
            // Count available tickets for this schedule
            $availableCount = TicketAvailability::where('visit_schedule_id', $schedule->visit_schedule_id)
                ->count();

            $visitDate = $schedule->visit_date;
            $dates[] = [
                'date'      => $visitDate->format('Y-m-d'),
                'day'       => $visitDate->format('l'),
                'display'   => $visitDate->format('M d'),
                'available' => $availableCount,
                'visit_schedule_id' => $schedule->visit_schedule_id,
            ];
        }

        return $dates;
    }

    /**
     * Get daily stocks organized by date and ticket type
     */
    private function getDailyStocks($ticketTypes)
    {
        $visitSchedules = VisitSchedule::orderBy('visit_date')
            ->limit(14)
            ->get();

        $stocks = [];

        foreach ($visitSchedules as $schedule) {
            $dayStocks = [
                'date' => $schedule->visit_date->format('M d, Y'),
                'visit_date' => $schedule->visit_date->format('Y-m-d'),
                'types' => []  // Array of {type_id, type_name, availability_count}
            ];
            
            $totalForDay = 0;

            foreach ($ticketTypes as $type) {
                $availability = TicketAvailability::where('visit_schedule_id', $schedule->visit_schedule_id)
                    ->where('ticket_type_id', $type->ticket_type_id)
                    ->count();

                $dayStocks['types'][] = [
                    'ticket_type_id' => $type->ticket_type_id,
                    'ticket_type_name' => $type->ticket_type_name,
                    'base_price' => $type->base_price,
                    'availability' => $availability
                ];
                
                $totalForDay += $availability;
            }

            $dayStocks['total'] = $totalForDay;
            $stocks[] = $dayStocks;
        }

        return $stocks;
    }

    /**
     * API: Get all available dates with ticket availability count
     */
    public function getAvailableDates()
    {
        $dates = VisitSchedule::select('visit_schedule_id', 'visit_date')
            ->orderBy('visit_date')
            ->limit(30)
            ->get()
            ->map(function ($schedule) {
                $availabilityCount = TicketAvailability::where('visit_schedule_id', $schedule->visit_schedule_id)
                    ->count();
                
                return [
                    'visit_schedule_id' => $schedule->visit_schedule_id,
                    'visit_date' => $schedule->visit_date->format('Y-m-d'),
                    'display_date' => $schedule->visit_date->format('M d, Y'),
                    'day_of_week' => $schedule->visit_date->format('l'),
                    'available_count' => $availabilityCount,
                    'is_available' => $availabilityCount > 0
                ];
            });

        return response()->json($dates);
    }

    /**
     * API: Get ticket types available for a specific date
     */
    public function getTicketTypesForDate($visitScheduleId)
    {
        $ticketTypes = TicketAvailability::where('visit_schedule_id', $visitScheduleId)
            ->with('ticketType')
            ->get()
            ->map(function ($availability) {
                return [
                    'ticket_type_id' => $availability->ticketType->ticket_type_id,
                    'ticket_type_name' => $availability->ticketType->ticket_type_name,
                    'base_price' => (float) $availability->ticketType->base_price,
                    'formatted_price' => '$' . number_format($availability->ticketType->base_price, 2)
                ];
            });

        return response()->json($ticketTypes);
    }
}
