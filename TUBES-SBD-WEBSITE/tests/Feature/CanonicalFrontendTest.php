<?php

namespace Tests\Feature;

use App\Models\ArtWork;
use App\Models\Constituent;
use App\Models\ConstituentRole;
use App\Models\Department;
use App\Models\Location;
use App\Models\ObjectType;
use App\Models\Repository;
use App\Models\ArtWorkGeography;
use App\Models\GeographyType;
use App\Models\Country;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CanonicalFrontendTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        \Illuminate\Support\Facades\Cache::flush();
        $this->seedReferenceData();
    }

    private function seedReferenceData()
    {
        $dept = Department::create(['department_name' => 'Paintings']);
        $type = ObjectType::create(['object_type_name' => 'Painting']);
        $loc = Location::create(['location_name' => 'Gallery 101']);
        $repo = Repository::create(['repository_name' => 'The Met Fifth Avenue']);

        $artwork = ArtWork::create([
            'met_object_id' => 1001,
            'title' => 'Starry Night Canonical',
            'slug' => 'starry-night-canonical',
            'department_id' => $dept->department_id,
            'type_id' => $type->type_id,
            'location_id' => $loc->location_id,
            'repository_id' => $repo->repository_id,
            'accession_number' => '123.456',
            'object_begin_date' => 1889,
            'object_end_date' => 1889,
            'object_date_display' => '1889',
        ]);

        $constituent = Constituent::create(['display_name' => 'Vincent van Gogh']);
        $role = ConstituentRole::create(['role_name' => 'Artist']);
        
        $artwork->constituents()->attach($constituent->constituent_id, [
            'role_id' => $role->role_id,
            'display_order' => 1,
        ]);

        $geoType = GeographyType::create(['geography_type_name' => 'Made in']);
        $country = Country::create(['country_name' => 'France']);
        
        ArtWorkGeography::create([
            'art_work_id' => $artwork->art_work_id,
            'geography_type_id' => $geoType->geography_type_id,
            'country_id' => $country->country_id,
        ]);

        \App\Models\ArtWorkImage::create([
            'art_work_id' => $artwork->art_work_id,
            'image_url' => 'https://example.com/image.jpg',
            'is_primary' => 1,
        ]);
    }

    /**
     * Test catalog grid view rendering without N+1.
     */
    public function test_catalog_view_renders_successfully_without_lazy_loading()
    {
        // If the view tries to lazy load 'artists' or something missing, it will throw an exception.
        $response = $this->get('/art/collection/search');

        $response->assertStatus(200);
        $response->assertSee('Starry Night Canonical');
        $response->assertSee('Vincent van Gogh');
    }

    /**
     * Test artwork detail view rendering without N+1.
     */
    public function test_detail_view_renders_successfully_without_lazy_loading()
    {
        $artwork = ArtWork::where('slug', 'starry-night-canonical')->first();
        \App\Models\ArtWorkSim::create([
            'art_work_id' => $artwork->art_work_id,
            'sim_type' => 'Signature',
            'sim_text' => 'Signed: V. van Gogh'
        ]);

        $response = $this->get('/art/starry-night-canonical');

        $response->assertStatus(200);
        $response->assertSee('Starry Night Canonical');
        $response->assertSee('Vincent van Gogh');
        $response->assertSee('Artist:'); // Curatorial sentence role
        $response->assertSee('Signed: V. van Gogh');
    }
}
