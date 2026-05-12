<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class ExhibitionController extends Controller
{
    public function index()
    {
        return view('admin.exhibitions.index', [
            'title'       => 'Exhibitions',
            'subtitle'    => 'Manage all exhibitions',
            'activeNav'   => 'exhibitions',
            'breadcrumbs' => [
                ['label' => 'Dashboard', 'href' => route('admin.dashboard')],
                ['label' => 'Exhibitions', 'isCurrent' => true],
            ],
        ]);
    }
}
