<?php
namespace App\Http\Controllers;

use App\Http\Requests\FilterArtworkRequest;
use App\Models\ArtWork;
use App\Models\Department;
use App\Models\GeoLocation;
use App\Models\ObjectType;
use Illuminate\Support\Facades\Cache;

class ArtWorkController extends Controller
{
    private $cacheTTL = 3600;

    public function index(FilterArtworkRequest $request)
    {
        $perPage  = $request->input('per_page', 24);
        $cacheKey = $request->getCacheKey();

        $data = Cache::remember($cacheKey, $this->cacheTTL, function () use ($request, $perPage) {
            $query = ArtWork::query()
                ->with([
                    'artists',
                    'images',
                    'department',
                    'objectType',
                    'geoLocation',
                    'location',
                ])
                ->whereHas('images')
                ->when($request->filled('department_id'), function ($q) use ($request) {
                    return $q->where('department_id', $request->input('department_id'));
                })
                ->when($request->filled('type_id'), function ($q) use ($request) {
                    return $q->where('type_id', $request->input('type_id'));
                })
                ->when($request->filled('geo_id'), function ($q) use ($request) {
                    return $q->where('geo_id', $request->input('geo_id'));
                })
                ->when($request->filled('artist_id'), function ($q) use ($request) {
                    return $q->whereHas('artists', function ($query) use ($request) {
                        $query->where('artists.id', $request->input('artist_id'));
                    });
                })
                ->when($request->filled('year_start') && $request->filled('year_end'), function ($q) use ($request) {
                    $yearStart = intval($request->input('year_start'));
                    $yearEnd   = intval($request->input('year_end'));
                    return $q->orWhereBetween('year_start', [$yearStart, $yearEnd])
                        ->orWhereBetween('year_end', [$yearStart, $yearEnd]);
                })
                ->when($request->filled('search'), function ($q) use ($request) {
                    $searchTerm = '%' . $request->input('search') . '%';
                    return $q->where(function ($query) use ($searchTerm) {
                        $query->where('title', 'LIKE', $searchTerm)
                            ->orWhere('description', 'LIKE', $searchTerm);
                    });
                })
                ->orderBy('id', 'DESC');

            $total    = $query->count();
            $artworks = $query->paginate($perPage);

            return [
                'artworks' => $artworks,
                'total'    => $total,
            ];
        });

        $departments = Cache::remember('departments_all', $this->cacheTTL, function () {
            return Department::all();
        });

        $types = Cache::remember('types_all', $this->cacheTTL, function () {
            return ObjectType::all();
        });

        $geoLocations = Cache::remember('geo_all', $this->cacheTTL, function () {
            return GeoLocation::all();
        });

        $activeFilters = [
            'department_id' => $request->input('department_id'),
            'type_id'       => $request->input('type_id'),
            'geo_id'        => $request->input('geo_id'),
            'artist_id'     => $request->input('artist_id'),
            'year_start'    => $request->input('year_start'),
            'year_end'      => $request->input('year_end'),
            'search'        => $request->input('search'),
        ];

        $hasActiveFilters = collect($activeFilters)->filter()->isNotEmpty();

        return view('ordinary.art.catalog.catalog', [
            'artworks'         => $data['artworks'],
            'departments'      => $departments,
            'types'            => $types,
            'geoLocations'     => $geoLocations,
            'activeFilters'    => $activeFilters,
            'hasActiveFilters' => $hasActiveFilters,
            'totalResults'     => $data['total'],
        ]);
    }

    public function show($slug)
    {
        try {
            $artwork = Cache::remember('artwork_' . $slug, $this->cacheTTL, function () use ($slug) {
                return ArtWork::where('slug', $slug)
                    ->with([
                        'artists',
                        'images',
                        'department',
                        'objectType',
                        'geoLocation',
                        'location',
                    ])
                    ->firstOrFail();
            });

            return view('ordinary.art.detail.detail', compact('artwork'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            abort(404, 'Artwork not found');
        }
    }
}
