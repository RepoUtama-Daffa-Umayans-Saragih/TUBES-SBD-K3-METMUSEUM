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
            ->with(['department', 'objectType', 'location', 'images', 'constituents', 'cultures']);

        // Canonical Field Mapping
        $fieldMap = [
            'all' => 'All Fields',
            'artist' => 'Artist / Culture',
            'title' => 'Title',
            'description' => 'Description',
            'gallery' => 'Gallery',
            'object_number' => 'Object Number',
            'credit_line' => 'Credit Line'
        ];

        // Read Search State
        $keyword = $request->input('q');
        $currentField = $request->input('field', 'all');
        $currentFieldLabel = $fieldMap[$currentField] ?? 'All Fields';
        
        if ($keyword) {
            $query->where(function ($q) use ($keyword, $currentField) {
                if ($currentField === 'title' || $currentField === 'all') {
                    $q->orWhere('title', 'like', "%{$keyword}%");
                }
                if ($currentField === 'description' || $currentField === 'all') {
                    $q->orWhere('description', 'like', "%{$keyword}%");
                }
                if ($currentField === 'credit_line' || $currentField === 'all') {
                    $q->orWhere('credit_line', 'like', "%{$keyword}%");
                }
                if ($currentField === 'gallery' || $currentField === 'all') {
                    $q->orWhere('gallery_number', 'like', "%{$keyword}%");
                }
                if ($currentField === 'object_number' || $currentField === 'all') {
                    $q->orWhere('accession_number', 'like', "%{$keyword}%");
                }
                if ($currentField === 'artist' || $currentField === 'all') {
                    $q->orWhereHas('constituents', function ($sub) use ($keyword) {
                        $sub->where('display_name', 'like', "%{$keyword}%");
                    })->orWhereHas('cultures', function ($sub) use ($keyword) {
                        $sub->where('culture_name', 'like', "%{$keyword}%");
                    });
                }
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

        return view('ordinary.art.search.search', array_merge([
            'artworks'            => $artworks,
            'totalResults'        => $artworks->total(),
            'title'               => 'Search Results',
            'search_query'        => $keyword ?? '',
            'currentField'        => $currentField,
            'currentFieldLabel'   => $currentFieldLabel,
            'currentSort'         => $request->input('sort', 'relevance'),
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
