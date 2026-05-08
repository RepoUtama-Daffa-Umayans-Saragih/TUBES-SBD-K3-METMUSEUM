<?php
namespace App\Http\Controllers;

<<<<<<< HEAD
use App\Models\ArtWork;
use App\Models\Department;
=======
use App\Models\Artist;
use App\Models\ArtWork;
use App\Models\Department;
use App\Models\GeoLocation;
>>>>>>> d4924d7e134627f65fb14d5d19ae9cabdff3b454
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
<<<<<<< HEAD
            'departments' => Department::orderBy('department_name')->get(['department_id', 'department_name']),
            'types'       => ObjectType::orderBy('object_type_name')->get(['type_id', 'object_type_name']),
=======
            'departments'  => Department::orderBy('name')->get(['department_id', 'name']),
            'artists'      => Artist::orderBy('name')->get(['artist_id', 'name']),
            'types'        => ObjectType::orderBy('name')->get(['type_id', 'name']),
            'geolocations' => GeoLocation::orderBy('name')->get(['geo_id', 'name']),
>>>>>>> d4924d7e134627f65fb14d5d19ae9cabdff3b454
        ];
    }

    /**
     * Display a listing of all artworks / collections with search and filters
     */
    public function index()
    {
        $artworks = ArtWork::query()
<<<<<<< HEAD
            ->with(['department', 'objectType', 'location', 'images'])
=======
            ->with(['artists', 'department', 'objectType', 'geoLocation', 'location', 'images'])
>>>>>>> d4924d7e134627f65fb14d5d19ae9cabdff3b454
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
<<<<<<< HEAD
        $artwork = ArtWork::with(['department', 'objectType', 'location', 'images'])
=======
        $artwork = ArtWork::with(['artists', 'department', 'objectType', 'geoLocation', 'location', 'images'])
>>>>>>> d4924d7e134627f65fb14d5d19ae9cabdff3b454
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
<<<<<<< HEAD
            ->with(['department', 'objectType', 'location', 'images']);
=======
            ->with(['artists', 'department', 'objectType', 'geoLocation', 'location', 'images']);
>>>>>>> d4924d7e134627f65fb14d5d19ae9cabdff3b454

        // Search keyword in title and description
        $keyword = $request->input('q');
        if ($keyword) {
            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'like', "%{$keyword}%")
                    ->orWhere('description', 'like', "%{$keyword}%");
            });
        }

<<<<<<< HEAD
=======
        // Filter by artist_id
        $artistId = $request->input('artist_id');
        if ($artistId) {
            $query->whereHas('artists', function ($q) use ($artistId) {
                $q->where('artists.artist_id', $artistId);
            });
        }

>>>>>>> d4924d7e134627f65fb14d5d19ae9cabdff3b454
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

<<<<<<< HEAD
        // Filter by year range - object_begin_date
        $yearStart = $request->input('year_start');
        if ($yearStart) {
            $query->where('object_end_date', '>=', (int) $yearStart);
        }

        // Filter by year range - object_end_date
        $yearEnd = $request->input('year_end');
        if ($yearEnd) {
            $query->where('object_begin_date', '<=', (int) $yearEnd);
=======
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
>>>>>>> d4924d7e134627f65fb14d5d19ae9cabdff3b454
        }

        // Execute query with pagination
        $artworks = $query->paginate(12)->appends($request->query());

        $filterData = $this->getFilterData();

        return view('ordinary.art.art', array_merge([
            'artworks'            => $artworks,
            'title'               => 'Search Results',
            'search_query'        => $keyword ?? '',
<<<<<<< HEAD
            'selected_department' => $departmentId,
            'selected_type'       => $typeId,
=======
            'selected_artist'     => $artistId,
            'selected_department' => $departmentId,
            'selected_type'       => $typeId,
            'selected_geo'        => $geoId,
>>>>>>> d4924d7e134627f65fb14d5d19ae9cabdff3b454
        ], $filterData));
    }
}
