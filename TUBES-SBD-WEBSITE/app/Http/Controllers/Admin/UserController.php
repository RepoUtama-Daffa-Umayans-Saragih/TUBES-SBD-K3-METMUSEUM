<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class UserController extends Controller
{
    public function index()
    {
        return view('admin.users.index', [
            'title'       => 'Users',
            'subtitle'    => 'Manage all users',
            'activeNav'   => 'users',
            'breadcrumbs' => [
                ['label' => 'Dashboard', 'href' => route('admin.dashboard')],
                ['label' => 'Users', 'isCurrent' => true],
            ],
        ]);
    }
}
