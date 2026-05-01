<?php
namespace App\Http\Controllers;

use App\Models\Artist;
use App\Models\ArtWork;
use App\Models\Department;
use App\Models\GeoLocation;
use App\Models\ObjectType;
use Illuminate\Http\Request;

class ArtController extends Controller
{
    /**
     * Get filter dropdown data
     */
    private function getFilterData()
    {
        return [
            'departments'  => Department::orderBy('name')->get(['department_id', 'name']),
            'artists'      => Artist::orderBy('name')->get(['artist_id', 'name']),
            'types'        => ObjectType::orderBy('name')->get(['type_id', 'name']),
            'geolocations' => GeoLocation::orderBy('name')->get(['geo_id', 'name']),
        ];
    }

    /**
     * Display a listing of all artworks / collections with search and filters
     */
    public function index()
    {
        $artworks = ArtWork::query()
            ->with(['artists', 'department', 'objectType', 'geoLocation', 'location', 'images'])
            ->paginate(12);

        $filterData = $this->getFilterData();

        return view('ordinary.art.art', array_merge([
            'artworks'     => $artworks,
            'title'        => 'Art Collections',
            'search_query' => '',
        ], $filterData));
    }

    /**
     * Display a specific artwork detail page
     */
    public function show($id)
    {
        $artwork = ArtWork::with(['artists', 'department', 'objectType', 'geoLocation', 'location', 'images'])
            ->where('art_work_id', $id)
            ->firstOrFail();

        return view('ordinary.art.show.show', [
            'artwork' => $artwork,
            'title'   => 'Artwork Detail',
        ]);
    }

    /**
     * Search and filter artworks with complex criteria
     */
    public function search(Request $request)
    {
        $query = ArtWork::query()
            ->with(['artists', 'department', 'objectType', 'geoLocation', 'location', 'images']);

        // Search keyword in title and description
        $keyword = $request->input('q');
        if ($keyword) {
            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'like', "%{$keyword}%")
                    ->orWhere('description', 'like', "%{$keyword}%");
            });
        }

        // Filter by artist_id
        $artistId = $request->input('artist_id');
        if ($artistId) {
            $query->whereHas('artists', function ($q) use ($artistId) {
                $q->where('artists.artist_id', $artistId);
            });
        }

        // Filter by department_id
        $departmentId = $request->input('department_id');
        if ($departmentId) {
            $query->where('department_id', $departmentId);
        }

        // Filter by object type_id
        $typeId = $request->input('type_id');
        if ($typeId) {
            $query->where('type_id', $typeId);
        }

        // Filter by geo_id
        $geoId = $request->input('geo_id');
        if ($geoId) {
            $query->where('geo_id', $geoId);
        }

        // Filter by year range - year_start
        $yearStart = $request->input('year_start');
        if ($yearStart) {
            $query->where('year_end', '>=', (int) $yearStart);
        }

        // Filter by year range - year_end
        $yearEnd = $request->input('year_end');
        if ($yearEnd) {
            $query->where('year_start', '<=', (int) $yearEnd);
        }

        // Execute query with pagination
        $artworks = $query->paginate(12)->appends($request->query());

        $filterData = $this->getFilterData();

        return view('ordinary.art.art', array_merge([
            'artworks'            => $artworks,
            'title'               => 'Search Results',
            'search_query'        => $keyword ?? '',
            'selected_artist'     => $artistId,
            'selected_department' => $departmentId,
            'selected_type'       => $typeId,
            'selected_geo'        => $geoId,
        ], $filterData));
    }
}
