<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class ArtworkController extends Controller
{
    public function index()
    {
        return view('admin.artworks.index', [
            'title'       => 'Artworks',
            'subtitle'    => 'Manage all artworks',
            'activeNav'   => 'artworks',
            'breadcrumbs' => [
                ['label' => 'Dashboard', 'href' => route('admin.dashboard')],
                ['label' => 'Artworks', 'isCurrent' => true],
            ],
        ]);
    }
}
