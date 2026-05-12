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
            'breadcrumbs' => [
                ['label' => 'Dashboard', 'href' => route('admin.dashboard')],
                ['label' => 'Tickets', 'isCurrent' => true],
            ],
        ]);
    }
}
