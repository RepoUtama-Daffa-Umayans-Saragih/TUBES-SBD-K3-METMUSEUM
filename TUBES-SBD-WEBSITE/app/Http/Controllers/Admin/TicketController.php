<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class TicketController extends Controller
{
    public function index()
    {
        return view('admin.tickets.index', [
            'title'       => 'Tickets',
            'subtitle'    => 'Manage all tickets',
            'activeNav'   => 'tickets',
            'availableDates' => $this->getAvailableDates(),
            'breadcrumbs' => [
                ['label' => 'Dashboard', 'href' => route('admin.dashboard')],
                ['label' => 'Tickets', 'isCurrent' => true],
            ],
        ]);
    }

    public function management()
    {
        return view('admin.tickets.management', [
            'title'           => 'Ticket Management',
            'subtitle'        => 'Manage ticket stock and prices',
            'activeNav'       => 'tickets',
            'totalStock'      => 0,
            'ticketsSold'     => 0,
            'availableStock'  => 0,
            'dailyStocks'     => [],
            'breadcrumbs'     => [
                ['label' => 'Dashboard', 'href' => route('admin.dashboard')],
                ['label' => 'Tickets', 'isCurrent' => true],
            ],
        ]);
    }

    private function getAvailableDates()
    {
        $dates = [];
        for ($i = 0; $i < 30; $i++) {
            $date = now()->addDays($i);
            $dates[] = [
                'date'      => $date->format('Y-m-d'),
                'day'       => $date->format('l'),
                'display'   => $date->format('M d'),
                'available' => rand(10, 100),
            ];
        }
        return $dates;
    }
}
