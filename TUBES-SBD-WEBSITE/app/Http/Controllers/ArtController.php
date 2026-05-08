<?php
namespace App\Http\Controllers;

use App\Models\ArtWork;
use App\Models\Department;
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
            'departments' => Department::orderBy('department_name')->get(['department_id', 'department_name']),
            'types'       => ObjectType::orderBy('object_type_name')->get(['type_id', 'object_type_name']),
        ];
    }

    /**
     * Display a listing of all artworks / collections with search and filters
     */
    public function index()
    {
        $artworks = ArtWork::query()
            ->with(['department', 'objectType', 'location', 'images'])
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
        $artwork = ArtWork::with(['department', 'objectType', 'location', 'images'])
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
            ->with(['department', 'objectType', 'location', 'images']);

        // Search keyword in title and description
        $keyword = $request->input('q');
        if ($keyword) {
            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'like', "%{$keyword}%")
                    ->orWhere('description', 'like', "%{$keyword}%");
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

        // Filter by year range - object_begin_date
        $yearStart = $request->input('year_start');
        if ($yearStart) {
            $query->where('object_end_date', '>=', (int) $yearStart);
        }

        // Filter by year range - object_end_date
        $yearEnd = $request->input('year_end');
        if ($yearEnd) {
            $query->where('object_begin_date', '<=', (int) $yearEnd);
        }

        // Execute query with pagination
        $artworks = $query->paginate(12)->appends($request->query());

        $filterData = $this->getFilterData();

        return view('ordinary.art.art', array_merge([
            'artworks'            => $artworks,
            'title'               => 'Search Results',
            'search_query'        => $keyword ?? '',
            'selected_department' => $departmentId,
            'selected_type'       => $typeId,
        ], $filterData));
    }

    /**
     * Display curatorial areas / departments page
     */
    public function curatorialAreas()
    {
        return view('ordinary.art.curatorial-areas');
    }
}
