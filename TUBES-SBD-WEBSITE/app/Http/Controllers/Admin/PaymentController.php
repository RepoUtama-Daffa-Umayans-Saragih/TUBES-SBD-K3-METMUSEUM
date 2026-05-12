<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class PaymentController extends Controller
{
    public function index()
    {
        return view('admin.payments.index', [
            'title'       => 'Payments',
            'subtitle'    => 'Manage all payments',
            'activeNav'   => 'payments',
            'breadcrumbs' => [
                ['label' => 'Dashboard', 'href' => route('admin.dashboard')],
                ['label' => 'Payments', 'isCurrent' => true],
            ],
        ]);
    }
}
