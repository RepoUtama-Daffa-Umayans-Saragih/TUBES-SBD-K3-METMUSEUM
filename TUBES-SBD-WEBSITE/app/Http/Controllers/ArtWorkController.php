<?php
namespace App\Http\Controllers;

use App\Http\Requests\FilterArtworkRequest;
use App\Models\ArtWork;
use App\Models\Department;
<<<<<<< HEAD
=======
use App\Models\GeoLocation;
>>>>>>> d4924d7e134627f65fb14d5d19ae9cabdff3b454
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
<<<<<<< HEAD
                    'images',
                    'department',
                    'objectType',
=======
                    'artists',
                    'images',
                    'department',
                    'objectType',
                    'geoLocation',
>>>>>>> d4924d7e134627f65fb14d5d19ae9cabdff3b454
                    'location',
                ])
                ->whereHas('images')
                ->when($request->filled('department_id'), function ($q) use ($request) {
                    return $q->where('department_id', $request->input('department_id'));
                })
                ->when($request->filled('type_id'), function ($q) use ($request) {
                    return $q->where('type_id', $request->input('type_id'));
                })
<<<<<<< HEAD
                ->when($request->filled('object_begin_date') && $request->filled('object_end_date'), function ($q) use ($request) {
                    $yearStart = intval($request->input('object_begin_date'));
                    $yearEnd   = intval($request->input('object_end_date'));
                    return $q->orWhereBetween('object_begin_date', [$yearStart, $yearEnd])
                        ->orWhereBetween('object_end_date', [$yearStart, $yearEnd]);
=======
                ->when($request->filled('geo_id'), function ($q) use ($request) {
                    return $q->where('geo_id', $request->input('geo_id'));
                })
                ->when($request->filled('artist_id'), function ($q) use ($request) {
                    return $q->whereHas('artists', function ($query) use ($request) {
                        $query->where('artists.artist_id', $request->input('artist_id'));
                    });
                })
                ->when($request->filled('year_start') && $request->filled('year_end'), function ($q) use ($request) {
                    $yearStart = intval($request->input('year_start'));
                    $yearEnd   = intval($request->input('year_end'));
                    return $q->orWhereBetween('year_start', [$yearStart, $yearEnd])
                        ->orWhereBetween('year_end', [$yearStart, $yearEnd]);
>>>>>>> d4924d7e134627f65fb14d5d19ae9cabdff3b454
                })
                ->when($request->filled('search'), function ($q) use ($request) {
                    $searchTerm = '%' . $request->input('search') . '%';
                    return $q->where(function ($query) use ($searchTerm) {
                        $query->where('title', 'LIKE', $searchTerm)
                            ->orWhere('description', 'LIKE', $searchTerm);
                    });
                })
                ->orderBy('art_work_id', 'DESC');

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

<<<<<<< HEAD
        $activeFilters = [
            'department_id'     => $request->input('department_id'),
            'type_id'           => $request->input('type_id'),
            'object_begin_date' => $request->input('object_begin_date'),
            'object_end_date'   => $request->input('object_end_date'),
            'search'            => $request->input('search'),
=======
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
>>>>>>> d4924d7e134627f65fb14d5d19ae9cabdff3b454
        ];

        $hasActiveFilters = collect($activeFilters)->filter()->isNotEmpty();

        return view('ordinary.art.catalog.catalog', [
            'artworks'         => $data['artworks'],
            'departments'      => $departments,
            'types'            => $types,
<<<<<<< HEAD
=======
            'geoLocations'     => $geoLocations,
>>>>>>> d4924d7e134627f65fb14d5d19ae9cabdff3b454
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
<<<<<<< HEAD
                        'images',
                        'department',
                        'objectType',
=======
                        'artists',
                        'images',
                        'department',
                        'objectType',
                        'geoLocation',
>>>>>>> d4924d7e134627f65fb14d5d19ae9cabdff3b454
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
