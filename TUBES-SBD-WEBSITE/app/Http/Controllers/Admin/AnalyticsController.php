<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class AnalyticsController extends Controller
{
    public function index()
    {
        return view('admin.analytics.index', [
            'title'       => 'Analytics',
            'subtitle'    => 'Monitor analytics and trends',
            'activeNav'   => 'analytics',
            'breadcrumbs' => [
                ['label' => 'Dashboard', 'href' => route('admin.dashboard')],
                ['label' => 'Analytics', 'isCurrent' => true],
            ],
        ]);
    }
}
