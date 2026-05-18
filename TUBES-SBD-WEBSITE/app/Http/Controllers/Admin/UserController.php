<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class UserController extends Controller
{
    public function index()
    {
        $usersRaw = \App\Models\User::with('profile')->get();

        $users = $usersRaw->map(function ($user) {
            $profile = $user->profile;
            $name = trim(($profile->first_name ?? '') . ' ' . ($profile->last_name ?? ''));
            if ($name === '') {
                $name = 'N/A';
            }

            return [
                'id'         => $user->user_id,
                'name'       => $name,
                'email'      => $user->email,
                'role'       => $user->is_admin ? 'admin' : 'user',
                'status'     => 'active',
                'created_at' => $user->created_at ? $user->created_at->format('Y-m-d H:i:s') : 'N/A',
            ];
        })->all();

        $totalUsers  = $usersRaw->count();
        $adminCount  = $usersRaw->where('is_admin', true)->count();
        $activeToday = \App\Models\User::whereDate('created_at', \Carbon\Carbon::today())->count();

        return view('admin.users.index', [
            'title'       => 'Users',
            'subtitle'    => 'Manage all users',
            'activeNav'   => 'users',
            'breadcrumbs' => [
                ['label' => 'Dashboard', 'href' => route('admin.dashboard')],
                ['label' => 'Users', 'isCurrent' => true],
            ],
            'users'       => $users,
            'totalUsers'  => $totalUsers,
            'adminCount'  => $adminCount,
            'activeToday' => $activeToday,
        ]);
    }
}
