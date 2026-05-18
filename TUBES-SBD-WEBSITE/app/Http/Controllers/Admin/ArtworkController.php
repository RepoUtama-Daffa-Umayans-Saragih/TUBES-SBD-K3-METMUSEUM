<?php
namespace App\Http\Controllers\Admin;

use App\Models\ArtWork;
use App\Models\Department;
use App\Models\ObjectType;
use App\Models\Location;
use App\Models\Repository;
use App\Models\Classification;
use App\Models\CreditLine;
use App\Models\Material;
use App\Models\Medium;
use App\Models\Constituent;
use App\Models\Tag;
use App\Models\Culture;
use App\Models\Period;
use App\Models\Dynasty;
use App\Models\Reign;
use App\Models\Portfolio;
use App\Models\ConstituentRole;
use App\Models\ConstituentPrefix;
use App\Models\ConstituentSuffix;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ArtworkController extends Controller
{
    /**
     * Display a listing of artworks
     */
    public function index()
    {
        $artworks = ArtWork::with([
            'department', 'objectType', 'location', 'constituents', 
            'materials', 'mediums', 'images'
        ])->orderBy('art_work_id', 'desc')->paginate(20);
        
        $totalArtworks = ArtWork::count();
        $totalDepartments = Department::count();
        $onDisplay = ArtWork::where('is_on_view', true)->count();
        
        return view('admin.artworks.index', [
            'artworks' => $artworks,
            'totalArtworks' => $totalArtworks,
            'totalDepartments' => $totalDepartments,
            'onDisplay' => $onDisplay,
            'title' => 'Artworks',
            'subtitle' => 'Manage all artworks',
            'activeNav' => 'artworks',
            'breadcrumbs' => [
                ['label' => 'Dashboard', 'href' => route('admin.dashboard')],
                ['label' => 'Artworks', 'isCurrent' => true],
            ],
        ]);
    }

    /**
     * Show the form for creating a new artwork
     */
    public function create()
    {
        return view('admin.artworks.form', [
            'title' => 'Create Artwork',
            'subtitle' => 'Add a new artwork to the collection',
            'activeNav' => 'artworks',
            'breadcrumbs' => [
                ['label' => 'Dashboard', 'href' => route('admin.dashboard')],
                ['label' => 'Artworks', 'href' => route('admin.artworks.index')],
                ['label' => 'Create', 'isCurrent' => true],
            ],
            'artwork' => null,
            'isEdit' => false,
            'departments' => Department::orderBy('department_name')->get(),
            'objectTypes' => ObjectType::orderBy('object_type_name')->get(),
            'locations' => Location::orderBy('location_name')->get(),
            'repositories' => Repository::orderBy('repository_name')->get(),
            'classifications' => Classification::orderBy('classification_name')->get(),
            'creditLines' => CreditLine::orderBy('credit_line_text')->get(),
            'materials' => Material::orderBy('material_name')->get(),
            'mediums' => Medium::orderBy('medium_name')->get(),
            'constituents' => Constituent::orderBy('display_name')->get(),
            'tags' => Tag::orderBy('tag_term')->get(),
            'cultures' => Culture::orderBy('culture_name')->get(),
            'periods' => Period::orderBy('period_name')->get(),
            'dynasties' => Dynasty::orderBy('dynasty_name')->get(),
            'reigns' => Reign::orderBy('reign_name')->get(),
            'portfolios' => Portfolio::orderBy('portfolio_name')->get(),
            'roles' => ConstituentRole::orderBy('role_name')->get(),
            'prefixes' => ConstituentPrefix::orderBy('prefix_name')->get(),
            'suffixes' => ConstituentSuffix::orderBy('suffix_name')->get(),
        ]);
    }

    /**
     * Store a newly created artwork
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'met_object_id' => 'required|integer|unique:art_works,met_object_id',
            'title' => 'required|string|max:500',
            'accession_number' => 'required|string|unique:art_works,accession_number',
            'accession_year' => 'nullable|integer|min:1000|max:2100',
            'description' => 'nullable|string',
            'gallery_number' => 'nullable|string|max:50',
            'object_date_display' => 'nullable|string|max:255',
            'object_begin_date' => 'nullable|integer|min:1000|max:2100',
            'object_end_date' => 'nullable|integer|min:1000|max:2100',
            'dimensions_display' => 'nullable|string',
            'rights_and_reproduction' => 'nullable|string',
            'provenance' => 'nullable|string',
            'department_id' => 'required|exists:departments,department_id',
            'type_id' => 'nullable|exists:object_types,object_type_id',
            'location_id' => 'nullable|exists:locations,location_id',
            'repository_id' => 'nullable|exists:repositories,repository_id',
            'classification_id' => 'nullable|exists:classifications,classification_id',
            'credit_line_id' => 'nullable|exists:credit_lines,credit_line_id',
            'is_on_view' => 'boolean',
            'is_highlight' => 'boolean',
            'is_public_domain' => 'boolean',
            'is_timeline_work' => 'boolean',
            'materials' => 'nullable|array',
            'materials.*' => 'exists:materials,material_id',
            'mediums' => 'nullable|array',
            'mediums.*' => 'exists:mediums,medium_id',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,tag_id',
            'cultures' => 'nullable|array',
            'cultures.*' => 'exists:cultures,culture_id',
            'periods' => 'nullable|array',
            'periods.*' => 'exists:periods,period_id',
            'dynasties' => 'nullable|array',
            'dynasties.*' => 'exists:dynasties,dynasty_id',
            'reigns' => 'nullable|array',
            'reigns.*' => 'exists:reigns,reign_id',
            'portfolios' => 'nullable|array',
            'portfolios.*' => 'exists:portfolios,portfolio_id',
        ]);

        try {
            $artwork = ArtWork::create($validated);

            // Sync M2M relationships
            $this->syncM2MRelationships($artwork, $request);

            // Save constituents with pivot data
            $this->saveConstituents($artwork, $request);

            // Handle image uploads
            $this->saveImages($artwork, $request);

            return redirect()->route('admin.artworks.show', $artwork->art_work_id)
                ->with('success', 'Artwork created successfully');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error creating artwork: ' . $e->getMessage());
        }
    }

    /**
     * Show the specified artwork
     */
    public function show(ArtWork $artwork)
    {
        $artwork->load([
            'department',
            'objectType',
            'location',
            'repository',
            'classification',
            'creditLine',
            'materials',
            'mediums',
            'constituents',
            'tags',
            'cultures',
            'periods',
            'dynasties',
            'reigns',
            'portfolios',
            'images',
            'measurements',
            'exhibitionHistories',
            'references',
            'geographies'
        ]);

        return view('admin.artworks.show', [
            'title' => 'Artwork Details',
            'subtitle' => $artwork->title ?? 'Untitled',
            'activeNav' => 'artworks',
            'breadcrumbs' => [
                ['label' => 'Dashboard', 'href' => route('admin.dashboard')],
                ['label' => 'Artworks', 'href' => route('admin.artworks.index')],
                ['label' => $artwork->title ?? 'Untitled', 'isCurrent' => true],
            ],
            'artwork' => $artwork,
        ]);
    }

    /**
     * Show the form for editing the artwork
     */
    public function edit(ArtWork $artwork)
    {
        $artwork->load([
            'materials',
            'mediums',
            'constituents',
            'tags',
            'cultures',
            'periods',
            'dynasties',
            'reigns',
            'portfolios',
            'images'
        ]);

        return view('admin.artworks.form', [
            'title' => 'Edit Artwork',
            'subtitle' => 'Update artwork information',
            'activeNav' => 'artworks',
            'breadcrumbs' => [
                ['label' => 'Dashboard', 'href' => route('admin.dashboard')],
                ['label' => 'Artworks', 'href' => route('admin.artworks.index')],
                ['label' => 'Edit', 'isCurrent' => true],
            ],
            'artwork' => $artwork,
            'isEdit' => true,
            'departments' => Department::orderBy('department_name')->get(),
            'objectTypes' => ObjectType::orderBy('object_type_name')->get(),
            'locations' => Location::orderBy('location_name')->get(),
            'repositories' => Repository::orderBy('repository_name')->get(),
            'classifications' => Classification::orderBy('classification_name')->get(),
            'creditLines' => CreditLine::orderBy('credit_line_text')->get(),
            'materials' => Material::orderBy('material_name')->get(),
            'mediums' => Medium::orderBy('medium_name')->get(),
            'constituents' => Constituent::orderBy('display_name')->get(),
            'tags' => Tag::orderBy('tag_term')->get(),
            'cultures' => Culture::orderBy('culture_name')->get(),
            'periods' => Period::orderBy('period_name')->get(),
            'dynasties' => Dynasty::orderBy('dynasty_name')->get(),
            'reigns' => Reign::orderBy('reign_name')->get(),
            'portfolios' => Portfolio::orderBy('portfolio_name')->get(),
            'roles' => ConstituentRole::orderBy('role_name')->get(),
            'prefixes' => ConstituentPrefix::orderBy('prefix_name')->get(),
            'suffixes' => ConstituentSuffix::orderBy('suffix_name')->get(),
        ]);
    }

    /**
     * Update the specified artwork
     */
    public function update(Request $request, ArtWork $artwork)
    {
        $validated = $request->validate([
            'met_object_id' => 'required|integer|unique:art_works,met_object_id,' . $artwork->art_work_id . ',art_work_id',
            'title' => 'required|string|max:500',
            'accession_number' => 'required|string|unique:art_works,accession_number,' . $artwork->art_work_id . ',art_work_id',
            'accession_year' => 'nullable|integer|min:1000|max:2100',
            'description' => 'nullable|string',
            'gallery_number' => 'nullable|string|max:50',
            'object_date_display' => 'nullable|string|max:255',
            'object_begin_date' => 'nullable|integer|min:1000|max:2100',
            'object_end_date' => 'nullable|integer|min:1000|max:2100',
            'dimensions_display' => 'nullable|string',
            'rights_and_reproduction' => 'nullable|string',
            'provenance' => 'nullable|string',
            'department_id' => 'required|exists:departments,department_id',
            'type_id' => 'nullable|exists:object_types,object_type_id',
            'location_id' => 'nullable|exists:locations,location_id',
            'repository_id' => 'nullable|exists:repositories,repository_id',
            'classification_id' => 'nullable|exists:classifications,classification_id',
            'credit_line_id' => 'nullable|exists:credit_lines,credit_line_id',
            'is_on_view' => 'boolean',
            'is_highlight' => 'boolean',
            'is_public_domain' => 'boolean',
            'is_timeline_work' => 'boolean',
            'materials' => 'nullable|array',
            'materials.*' => 'exists:materials,material_id',
            'mediums' => 'nullable|array',
            'mediums.*' => 'exists:mediums,medium_id',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,tag_id',
            'cultures' => 'nullable|array',
            'cultures.*' => 'exists:cultures,culture_id',
            'periods' => 'nullable|array',
            'periods.*' => 'exists:periods,period_id',
            'dynasties' => 'nullable|array',
            'dynasties.*' => 'exists:dynasties,dynasty_id',
            'reigns' => 'nullable|array',
            'reigns.*' => 'exists:reigns,reign_id',
            'portfolios' => 'nullable|array',
            'portfolios.*' => 'exists:portfolios,portfolio_id',
        ]);

        try {
            $artwork->update($validated);

            // Sync M2M relationships
            $this->syncM2MRelationships($artwork, $request);

            // Save constituents with pivot data
            $this->saveConstituents($artwork, $request);

            // Handle image uploads and updates
            $this->saveImages($artwork, $request);

            return redirect()->route('admin.artworks.show', $artwork->art_work_id)
                ->with('success', 'Artwork updated successfully');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error updating artwork: ' . $e->getMessage());
        }
    }

    /**
     * Delete the specified artwork
     */
    public function destroy(ArtWork $artwork)
    {
        try {
            // Detach all M2M relationships before deleting
            $artwork->materials()->detach();
            $artwork->mediums()->detach();
            $artwork->constituents()->detach();
            $artwork->tags()->detach();
            $artwork->cultures()->detach();
            $artwork->periods()->detach();
            $artwork->dynasties()->detach();
            $artwork->reigns()->detach();
            $artwork->portfolios()->detach();

            // Delete all images
            $artwork->images()->delete();

            // Delete the artwork
            $artwork->delete();

            return redirect()->route('admin.artworks.index')
                ->with('success', 'Artwork deleted successfully');
        } catch (\Exception $e) {
            return redirect()->route('admin.artworks.index')
                ->with('error', 'Error deleting artwork: ' . $e->getMessage());
        }
    }

    /**
     * Sync many-to-many relationships
     */
    protected function syncM2MRelationships(ArtWork $artwork, Request $request)
    {
        // Sync simple M2M relationships
        $artwork->materials()->sync($request->input('materials') ?? []);
        $artwork->mediums()->sync($request->input('mediums') ?? []);
        $artwork->tags()->sync($request->input('tags') ?? []);
        $artwork->cultures()->sync($request->input('cultures') ?? []);
        $artwork->periods()->sync($request->input('periods') ?? []);
        $artwork->dynasties()->sync($request->input('dynasties') ?? []);
        $artwork->reigns()->sync($request->input('reigns') ?? []);
        $artwork->portfolios()->sync($request->input('portfolios') ?? []);
    }

    /**
     * Save/update artwork images
     */
    protected function saveImages(ArtWork $artwork, Request $request)
    {
        // Handle primary image selection
        if ($request->has('primary_image_id') && $request->input('primary_image_id')) {
            $primaryImageId = $request->input('primary_image_id');
            
            // Set all images to non-primary
            $artwork->images()->update(['is_primary' => false]);
            
            // Set selected image as primary
            $artwork->images()
                ->where('image_id', $primaryImageId)
                ->update(['is_primary' => true]);
        }

        // Add new image if URL provided
        if ($request->has('new_image_url') && $request->input('new_image_url')) {
            $imageUrl = $request->input('new_image_url');
            $isPrimary = $request->has('new_image_primary') ? (bool) $request->input('new_image_primary') : false;

            // If this is primary, set existing images to non-primary
            if ($isPrimary) {
                $artwork->images()->update(['is_primary' => false]);
            }

            // Create new image
            $artwork->images()->create([
                'image_url' => $imageUrl,
                'is_primary' => $isPrimary
            ]);
        }
    }

    /**
     * Save/update constituents with pivot data (roles, prefixes, suffixes)
     */
    protected function saveConstituents(ArtWork $artwork, Request $request)
    {
        // Handle adding a new constituent if provided
        if ($request->has('new_constituent_id') && $request->input('new_constituent_id')) {
            $constituentId = $request->input('new_constituent_id');
            $roleId = $request->input('new_constituent_role') ?: null;
            $prefixId = $request->input('new_constituent_prefix') ?: null;
            $suffixId = $request->input('new_constituent_suffix') ?: null;

            // Check if constituent is already attached using the pivot table
            $exists = $artwork->constituents()
                ->wherePivot('constituent_id', $constituentId)
                ->exists();

            if (!$exists) {
                // Attach with pivot data
                $artwork->constituents()->attach($constituentId, [
                    'role_id' => $roleId,
                    'prefix_id' => $prefixId,
                    'suffix_id' => $suffixId,
                    'display_order' => $artwork->constituents()->count() + 1
                ]);
            }
        }
    }
}


