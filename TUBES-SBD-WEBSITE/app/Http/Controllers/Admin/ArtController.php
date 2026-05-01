<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ArtWork;
use App\Models\ArtWorkImage;
use App\Models\Department;
use App\Models\GeoLocation;
use App\Models\Location;
use App\Models\ObjectType;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ArtController extends Controller
{
    /**
     * Display admin dashboard
     */
    public function dashboard()
    {
        $totalArtworks  = ArtWork::count();
        $totalUsers     = User::count();
        $totalOrders    = Order::count();
        $recentArtworks = ArtWork::latest('art_work_id')->take(5)->get();

        return view('admin.dashboard.dashboard', [
            'totalArtworks'  => $totalArtworks,
            'totalUsers'     => $totalUsers,
            'totalOrders'    => $totalOrders,
            'recentArtworks' => $recentArtworks,
        ]);
    }

    /**
     * Display list of all artworks
     */
    public function index()
    {
        $artworks = ArtWork::with('department', 'objectType', 'geoLocation', 'location', 'artists')
            ->paginate(20);

        return view('admin.art.art', [
            'artworks' => $artworks,
        ]);
    }

    /**
     * Show create artwork form
     */
    public function create()
    {
        $departments  = Department::orderBy('name')->get();
        $types        = ObjectType::orderBy('name')->get();
        $geoLocations = GeoLocation::orderBy('name')->get();
        $locations    = Location::orderBy('name')->get();

        return view('admin.art.create.create', [
            'departments'  => $departments,
            'types'        => $types,
            'geoLocations' => $geoLocations,
            'locations'    => $locations,
        ]);
    }

    /**
     * Store newly created artwork
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'            => 'required|string|max:255',
            'description'      => 'nullable|string',
            'department_id'    => 'required|exists:departments,department_id',
            'object_type_id'   => 'required|exists:object_types,type_id',
            'geo_location_id'  => 'required|exists:geo_locations,geo_id',
            'location_id'      => 'required|exists:locations,location_id',
            'year_start'       => 'nullable|integer|min:1000|max:' . date('Y'),
            'year_end'         => 'nullable|integer|min:1000|max:' . date('Y'),
            'object_number'    => 'nullable|string|max:255|unique:art_works,object_number',
            'accession_number' => 'nullable|string|max:255',
            'images'           => 'nullable|array',
            'images.*'         => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        $objectNumber = $validated['object_number'] ?? $validated['accession_number'] ?? strtoupper(Str::random(12));

        $artwork = ArtWork::create([
            'object_number' => $objectNumber,
            'title'         => $validated['title'],
            'description'   => $validated['description'] ?? null,
            'department_id' => $validated['department_id'],
            'type_id'       => $validated['object_type_id'],
            'geo_id'        => $validated['geo_location_id'] ?? null,
            'location_id'   => $validated['location_id'] ?? null,
            'year_start'    => $validated['year_start'] ?? null,
            'year_end'      => $validated['year_end'] ?? null,
            'slug'          => Str::slug($validated['title']),
        ]);

        // Handle image uploads
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('artworks', 'public');

                ArtWorkImage::create([
                    'art_work_id' => $artwork->art_work_id,
                    'url'         => $path,
                    'is_primary'  => $index === 0 ? 1 : 0,
                ]);
            }
        }

        return redirect()->route('admin.art.show', $artwork->art_work_id)
            ->with('success', 'Artwork created successfully!');
    }

    /**
     * Show edit artwork form
     */
    public function edit($id)
    {
        $artwork      = ArtWork::where('art_work_id', $id)->firstOrFail();
        $departments  = Department::orderBy('name')->get();
        $types        = ObjectType::orderBy('name')->get();
        $geoLocations = GeoLocation::orderBy('name')->get();
        $locations    = Location::orderBy('name')->get();

        return view('admin.art.edit.edit', [
            'artwork'      => $artwork,
            'departments'  => $departments,
            'types'        => $types,
            'geoLocations' => $geoLocations,
            'locations'    => $locations,
        ]);
    }

    /**
     * Update artwork
     */
    public function update(Request $request, $id)
    {
        $artwork = ArtWork::where('art_work_id', $id)->firstOrFail();

        $validated = $request->validate([
            'title'            => 'required|string|max:255',
            'description'      => 'nullable|string',
            'department_id'    => 'required|exists:departments,department_id',
            'object_type_id'   => 'required|exists:object_types,type_id',
            'geo_location_id'  => 'required|exists:geo_locations,geo_id',
            'location_id'      => 'required|exists:locations,location_id',
            'year_start'       => 'nullable|integer|min:1000|max:' . date('Y'),
            'year_end'         => 'nullable|integer|min:1000|max:' . date('Y'),
            'object_number'    => 'nullable|string|max:255|unique:art_works,object_number,' . $id . ',art_work_id',
            'accession_number' => 'nullable|string|max:255',
            'images'           => 'nullable|array',
            'images.*'         => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        $objectNumber = $validated['object_number'] ?? $validated['accession_number'] ?? $artwork->object_number;

        $artwork->update([
            'object_number' => $objectNumber,
            'title'         => $validated['title'],
            'description'   => $validated['description'] ?? null,
            'department_id' => $validated['department_id'],
            'type_id'       => $validated['object_type_id'],
            'geo_id'        => $validated['geo_location_id'] ?? null,
            'location_id'   => $validated['location_id'] ?? null,
            'year_start'    => $validated['year_start'] ?? null,
            'year_end'      => $validated['year_end'] ?? null,
            'slug'          => Str::slug($validated['title']),
        ]);

        // Handle image uploads
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('artworks', 'public');

                ArtWorkImage::create([
                    'art_work_id' => $artwork->art_work_id,
                    'url'         => $path,
                    'is_primary'  => 0,
                ]);
            }
        }

        return redirect()->route('admin.art.index')
            ->with('success', 'Artwork updated successfully!');
    }

    /**
     * Show artwork details
     */
    public function show($id)
    {
        $artwork = ArtWork::with('department', 'objectType', 'geoLocation', 'location', 'artists', 'images')
            ->where('art_work_id', $id)
            ->firstOrFail();

        return view('admin.art.show.show', [
            'artwork' => $artwork,
        ]);
    }

    /**
     * Delete artwork
     */
    public function destroy($id)
    {
        $artwork = ArtWork::where('art_work_id', $id)->firstOrFail();
        $title   = $artwork->title;

        // Delete all images
        foreach ($artwork->images as $image) {
            Storage::disk('public')->delete($image->url);
            $image->delete();
        }

        $artwork->delete();

        return redirect()->route('admin.art.index')
            ->with('success', "Artwork '{$title}' deleted successfully!");
    }

    /**
     * Delete artwork image
     */
    public function deleteImage($imageId)
    {
        $image     = ArtWorkImage::where('image_id', $imageId)->firstOrFail();
        $artworkId = $image->art_work_id;

        // Delete file from storage
        Storage::disk('public')->delete($image->url);
        $image->delete();

        return redirect()->route('admin.art.edit', $artworkId)
            ->with('success', 'Image deleted successfully!');
    }
}
