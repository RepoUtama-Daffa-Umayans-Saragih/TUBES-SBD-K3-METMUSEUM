<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class OrderController extends Controller
{
    public function index()
    {
        return view('admin.orders.index', [
            'title'       => 'Orders',
            'subtitle'    => 'Manage all orders',
            'activeNav'   => 'orders',
            'breadcrumbs' => [
                ['label' => 'Dashboard', 'href' => route('admin.dashboard')],
                ['label' => 'Orders', 'isCurrent' => true],
            ],
        ]);
    }
}
