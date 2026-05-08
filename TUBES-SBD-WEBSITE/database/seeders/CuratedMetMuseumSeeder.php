<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CuratedMetMuseumSeeder extends Seeder
{
    private array $cache = [];

    public function run()
    {
        $csvPath = 'C:/Users/ASUS/Downloads/metmuseum_curated_full_columns_2000.csv';
        if (!file_exists($csvPath)) {
            $this->command->error("CSV not found at $csvPath");
            return;
        }

        $this->command->info("Starting to seed from $csvPath");
        Schema::disableForeignKeyConstraints();

        // Truncate relevant tables
        $tablesToTruncate = [
            'art_work_geographies', 'art_work_constituents', 'art_work_tags',
            'art_work_cultures', 'art_work_periods', 'art_work_dynasties',
            'art_work_reigns', 'art_work_portfolios', 'art_work_materials',
            'art_work_images', 'art_works'
        ];
        
        foreach ($tablesToTruncate as $table) {
            DB::table($table)->truncate();
        }

        $handle = fopen($csvPath, "r");
        $headers = fgetcsv($handle);
        $headerMap = array_flip($headers);

        $defaultLocationId = $this->getGeoId('locations', 'The Met Fifth Avenue', 'location_name', 'location_id');
        $defaultRepoId = $this->getGeoId('repositories', 'Metropolitan Museum of Art, New York, NY', 'repository_name', 'repository_id');

        $count = 0;
        while (($row = fgetcsv($handle)) !== false) {
            if (empty($row[$headerMap['Object ID']])) continue;

            $deptNameCsv = trim($row[$headerMap['Department']] ?? '');
            $deptId = $this->getGeoId('departments', $deptNameCsv, 'department_name', 'department_id');

            $typeNameCsv = trim($row[$headerMap['Object Name']] ?? '');
            $classNameCsv = trim($row[$headerMap['Classification']] ?? '');
            $mediumCsv = trim($row[$headerMap['Medium']] ?? '');
            
            $combinedForTaxonomy = strtolower(implode(' | ', $row));

            $typeId = null;
            $classId = null;

            // Map Object Type (Search combined text for known types)
            $knownTypes = DB::table('object_types')->pluck('object_type_name', 'type_id');
            foreach ($knownTypes as $id => $name) {
                if ($typeNameCsv === $name || stripos($combinedForTaxonomy, $name) !== false || stripos($name, $typeNameCsv) !== false) {
                    $typeId = $id;
                    break;
                }
            }

            // Map Classification
            $knownClasses = DB::table('classifications')->pluck('classification_name', 'classification_id');
            foreach ($knownClasses as $id => $name) {
                if ($classNameCsv === $name || stripos($combinedForTaxonomy, $name) !== false || stripos($name, $classNameCsv) !== false) {
                    $classId = $id;
                    break;
                }
            }

            // Skip invalid unmatched taxonomy safely
            if (!$deptId || (!$typeId && !$classId)) {
                continue;
            }
            
            if (!$typeId || !$classId) {
                // If one is missing, fallback to the other if possible, or fallback to the first to avoid failing NOT NULL
                // Wait, they are foreign keys to DIFFERENT tables. We can't fallback typeId to classId.
                // We will create a dummy 'Unassigned' locally if needed? No, skip safely.
                continue;
            }

            $repoName = $row[$headerMap['Repository']] ?: 'Metropolitan Museum of Art, New York, NY';
            $repoId = $this->getGeoId('repositories', $repoName, 'repository_name', 'repository_id');

            $isPublicDomain = strtolower($row[$headerMap['Is Public Domain']] ?? '') === 'true';

            $artWorkId = DB::table('art_works')->insertGetId([
                'met_object_id' => $row[$headerMap['Object ID']],
                'accession_number' => $row[$headerMap['Object Number']] ?: 'UNKNOWN-' . uniqid(),
                'accession_year' => is_numeric($row[$headerMap['AccessionYear']]) ? $row[$headerMap['AccessionYear']] : null,
                'title' => substr($row[$headerMap['Title']] ?: 'Unknown Title', 0, 255),
                'slug' => 'art-' . $row[$headerMap['Object ID']],
                'description' => null, // Not directly in CSV
                'gallery_number' => $row[$headerMap['Gallery Number']] ?: null,
                'is_on_view' => !empty($row[$headerMap['Gallery Number']]),
                'is_highlight' => strtolower($row[$headerMap['Is Highlight']] ?? '') === 'true',
                'is_public_domain' => $isPublicDomain,
                'is_timeline_work' => strtolower($row[$headerMap['Is Timeline Work']] ?? '') === 'true',
                'object_date_display' => $row[$headerMap['Object Date']] ?: null,
                'object_begin_date' => is_numeric($row[$headerMap['Object Begin Date']]) ? $row[$headerMap['Object Begin Date']] : null,
                'object_end_date' => is_numeric($row[$headerMap['Object End Date']]) ? $row[$headerMap['Object End Date']] : null,
                'medium_display' => $row[$headerMap['Medium']] ?: null,
                'dimensions_display' => $row[$headerMap['Dimensions']] ?: null,
                'credit_line' => $row[$headerMap['Credit Line']] ?: null,
                'rights_and_reproduction' => $row[$headerMap['Rights and Reproduction']] ?: null,
                'link_resource' => $row[$headerMap['Link Resource']] ?: null,
                'object_wikidata_url' => $row[$headerMap['Object Wikidata URL']] ?: null,
                'metadata_date' => $row[$headerMap['Metadata Date']] ? \Carbon\Carbon::parse($row[$headerMap['Metadata Date']])->toDateTimeString() : null,
                'department_id' => $deptId,
                'type_id' => $typeId,
                'classification_id' => $classId,
                'location_id' => $defaultLocationId,
                'repository_id' => $repoId,
            ]);

            // Images
            if ($isPublicDomain || rand(1, 100) > 50) {
                DB::table('art_work_images')->insert([
                    'art_work_id' => $artWorkId,
                    'image_url' => 'https://collectionapi.metmuseum.org/api/collection/v1/iiif/' . $row[$headerMap['Object ID']] . '/main-image',
                    'is_primary' => true,
                    'display_order' => 1
                ]);
            }

            // Pivot relations
            $this->seedPivots($artWorkId, $row[$headerMap['Culture']], 'cultures', 'culture_name', 'culture_id', 'art_work_cultures');
            $this->seedPivots($artWorkId, $row[$headerMap['Period']], 'periods', 'period_name', 'period_id', 'art_work_periods');
            $this->seedPivots($artWorkId, $row[$headerMap['Dynasty']], 'dynasties', 'dynasty_name', 'dynasty_id', 'art_work_dynasties');
            $this->seedPivots($artWorkId, $row[$headerMap['Reign']], 'reigns', 'reign_name', 'reign_id', 'art_work_reigns');
            $this->seedPivots($artWorkId, $row[$headerMap['Portfolio']], 'portfolios', 'portfolio_name', 'portfolio_id', 'art_work_portfolios');
            
            // Materials (from Object Type and Classification as fallback for filter coverage)
            $materials = array_filter([$typeNameCsv, $classNameCsv]);
            foreach ($materials as $mat) {
                $matId = $this->getGeoId('materials', $mat, 'material_name', 'material_id');
                if ($matId) {
                    DB::table('art_work_materials')->insertOrIgnore(['art_work_id' => $artWorkId, 'material_id' => $matId]);
                }
            }

            // Tags
            $tags = array_filter(explode('|', $row[$headerMap['Tags']] ?? ''));
            foreach ($tags as $tag) {
                $tagId = $this->getGeoId('tags', $tag, 'tag_term', 'tag_id');
                if ($tagId) {
                    DB::table('art_work_tags')->insertOrIgnore(['art_work_id' => $artWorkId, 'tag_id' => $tagId]);
                }
            }

            // Geographies
            $geoData = [];
            $geoData['geography_type_id'] = $this->getGeoId('geography_types', $row[$headerMap['Geography Type']], 'geography_type_name', 'geography_type_id');
            $geoData['excavation_id'] = $this->getGeoId('excavations', $row[$headerMap['Excavation']], 'excavation_name', 'excavation_id');
            $geoData['river_id'] = $this->getGeoId('rivers', $row[$headerMap['River']], 'river_name', 'river_id');
            
            $countryId = $this->getGeoId('countries', $row[$headerMap['Country']] ?: 'Unknown Country', 'country_name', 'country_id');
            $geoData['country_id'] = $row[$headerMap['Country']] ? $countryId : null;
            
            $regionId = $this->getHierarchicalGeoId('regions', $row[$headerMap['Region']], 'region_name', 'region_id', 'country_id', $countryId);
            $geoData['region_id'] = $regionId;
            
            $subregionId = $this->getHierarchicalGeoId('subregions', $row[$headerMap['Subregion']], 'subregion_name', 'subregion_id', 'region_id', $regionId ?: $this->getHierarchicalGeoId('regions', 'Unknown Region', 'region_name', 'region_id', 'country_id', $countryId));
            $geoData['subregion_id'] = $subregionId;
            
            $localeId = $this->getHierarchicalGeoId('locales', $row[$headerMap['Locale']], 'locale_name', 'locale_id', 'subregion_id', $subregionId ?: $this->getHierarchicalGeoId('subregions', 'Unknown Subregion', 'subregion_name', 'subregion_id', 'region_id', $this->getHierarchicalGeoId('regions', 'Unknown Region', 'region_name', 'region_id', 'country_id', $countryId)));
            $geoData['locale_id'] = $localeId;
            
            $locusId = $this->getHierarchicalGeoId('loci', $row[$headerMap['Locus']], 'locus_name', 'locus_id', 'locale_id', $localeId ?: $this->getHierarchicalGeoId('locales', 'Unknown Locale', 'locale_name', 'locale_id', 'subregion_id', $this->getHierarchicalGeoId('subregions', 'Unknown Subregion', 'subregion_name', 'subregion_id', 'region_id', $this->getHierarchicalGeoId('regions', 'Unknown Region', 'region_name', 'region_id', 'country_id', $countryId))));
            $geoData['locus_id'] = $locusId;
            
            $stateId = $this->getHierarchicalGeoId('states', $row[$headerMap['State']], 'state_name', 'state_id', 'country_id', $countryId);
            $geoData['state_id'] = $stateId;
            
            $countyId = $this->getHierarchicalGeoId('counties', $row[$headerMap['County']], 'county_name', 'county_id', 'state_id', $stateId ?: $this->getHierarchicalGeoId('states', 'Unknown State', 'state_name', 'state_id', 'country_id', $countryId));
            $geoData['county_id'] = $countyId;
            
            $cityId = $this->getHierarchicalGeoId('cities', $row[$headerMap['City']], 'city_name', 'city_id', 'state_id', $stateId ?: $this->getHierarchicalGeoId('states', 'Unknown State', 'state_name', 'state_id', 'country_id', $countryId));
            $geoData['city_id'] = $cityId;

            if (array_filter($geoData)) {
                $geoData['art_work_id'] = $artWorkId;
                DB::table('art_work_geographies')->insert($geoData);
            }

            // Constituents
            $constituentIds = explode('|', $row[$headerMap['Constituent ID']] ?? '');
            $roles = explode('|', $row[$headerMap['Artist Role']] ?? '');
            $names = explode('|', $row[$headerMap['Artist Display Name']] ?? '');
            $prefixes = explode('|', $row[$headerMap['Artist Prefix']] ?? '');
            $suffixes = explode('|', $row[$headerMap['Artist Suffix']] ?? '');
            $bios = explode('|', $row[$headerMap['Artist Display Bio']] ?? '');
            $alphaSorts = explode('|', $row[$headerMap['Artist Alpha Sort']] ?? '');
            $nationalities = explode('|', $row[$headerMap['Artist Nationality']] ?? '');
            $beginDates = explode('|', $row[$headerMap['Artist Begin Date']] ?? '');
            $endDates = explode('|', $row[$headerMap['Artist End Date']] ?? '');
            $genders = explode('|', $row[$headerMap['Artist Gender']] ?? '');
            $ulanUrls = explode('|', $row[$headerMap['Artist ULAN URL']] ?? '');
            $wikiUrls = explode('|', $row[$headerMap['Artist Wikidata URL']] ?? '');

            for ($i = 0; $i < count($constituentIds); $i++) {
                $cId = trim($constituentIds[$i] ?? '');
                $name = trim($names[$i] ?? '');
                if (!$name && !$cId) continue;

                $roleName = trim($roles[$i] ?? 'Artist');
                $roleId = $this->getGeoId('constituent_roles', $roleName, 'role_name', 'role_id');

                $prefixName = trim($prefixes[$i] ?? '');
                $prefixId = $prefixName ? $this->getGeoId('constituent_prefixes', $prefixName, 'prefix_name', 'prefix_id') : null;

                $suffixName = trim($suffixes[$i] ?? '');
                $suffixId = $suffixName ? $this->getGeoId('constituent_suffixes', $suffixName, 'suffix_name', 'suffix_id') : null;

                if ($cId) {
                    $localConstId = $this->getConstituent($cId, $name, $bios[$i] ?? '', $alphaSorts[$i] ?? '', $beginDates[$i] ?? '', $endDates[$i] ?? '', $genders[$i] ?? '', $ulanUrls[$i] ?? '', $wikiUrls[$i] ?? '');
                } else {
                    $localConstId = $this->getConstituentByDetails($name, $bios[$i] ?? '', $alphaSorts[$i] ?? '', $beginDates[$i] ?? '', $endDates[$i] ?? '', $genders[$i] ?? '', $ulanUrls[$i] ?? '', $wikiUrls[$i] ?? '');
                }

                if ($localConstId) {
                    DB::table('art_work_constituents')->insertOrIgnore([
                        'art_work_id' => $artWorkId,
                        'constituent_id' => $localConstId,
                        'role_id' => $roleId,
                        'prefix_id' => $prefixId,
                        'suffix_id' => $suffixId,
                        'display_order' => $i + 1
                    ]);
                }
                
                // Assign Nationality
                $natName = trim($nationalities[$i] ?? '');
                if ($natName) {
                    $natId = $this->getGeoId('nationalities', $natName, 'nationality_name', 'nationality_id');
                    DB::table('constituent_nationalities')->insertOrIgnore([
                        'constituent_id' => $localConstId,
                        'nationality_id' => $natId
                    ]);
                }
            }

            $count++;
            if ($count % 500 === 0) {
                $this->command->info("Processed $count records");
            }
        }
        fclose($handle);
        
        Schema::enableForeignKeyConstraints();
        $this->command->info("Finished! Processed $count artworks.");
    }

    private function getGeoId($table, $name, $nameCol, $idCol)
    {
        if (!$name) return null;
        $name = trim($name);
        if (!isset($this->cache[$table][$name])) {
            $id = DB::table($table)->where($nameCol, $name)->value($idCol);
            
            // Allow dynamic insertion ONLY for non-taxonomy metadata
            if (!$id && in_array($table, ['repositories', 'locations', 'tags', 'cultures', 'periods', 'dynasties', 'reigns', 'portfolios', 'constituent_roles', 'constituent_prefixes', 'constituent_suffixes', 'nationalities'])) {
                $id = DB::table($table)->insertGetId([$nameCol => $name]);
            }
            $this->cache[$table][$name] = $id;
        }
        return $this->cache[$table][$name];
    }

    private function getHierarchicalGeoId($table, $name, $nameCol, $idCol, $parentCol, $parentId)
    {
        if (!$name || !$parentId) return null; // Hierarchical taxonomy requires parent
        $name = trim($name);
        $cacheKey = $parentId . '|' . $name;
        if (!isset($this->cache[$table][$cacheKey])) {
            $id = DB::table($table)->where($nameCol, $name)->where($parentCol, $parentId)->value($idCol);
            
            // STRICT RULE: NEVER auto-generate geographic taxonomy from CSV
            $this->cache[$table][$cacheKey] = $id;
        }
        return $this->cache[$table][$cacheKey];
    }

    private function seedPivots($artWorkId, $valueStr, $table, $nameCol, $idCol, $pivotTable)
    {
        if (!$valueStr) return;
        $items = array_filter(explode('|', $valueStr));
        foreach ($items as $item) {
            $id = $this->getGeoId($table, $item, $nameCol, $idCol);
            if ($id) {
                DB::table($pivotTable)->insertOrIgnore([
                    'art_work_id' => $artWorkId,
                    $idCol => $id
                ]);
            }
        }
    }

    private function getConstituent($metId, $name, $bio, $sort, $begin, $end, $gender, $ulan, $wiki)
    {
        $metId = is_numeric($metId) ? (int)$metId : null;
        if ($metId > 2147483647 || $metId < -2147483648) {
            $metId = null;
        }
        $key = 'met|' . $metId . '|' . $name;
        if (!isset($this->cache['constituents'][$key])) {
            $id = $metId ? DB::table('constituents')->where('met_constituent_id', $metId)->value('constituent_id') : null;
            if (!$id && $name) {
                $id = DB::table('constituents')->where('display_name', $name)->value('constituent_id');
            }
            if (!$id) {
                $id = DB::table('constituents')->insertGetId([
                    'met_constituent_id' => $metId,
                    'display_name' => $name ?: 'Unknown',
                    'display_bio' => $bio ?: null,
                    'alpha_sort' => $sort ?: null,
                    'birth_year' => is_numeric($begin) ? (int)$begin : null,
                    'death_year' => is_numeric($end) ? (int)$end : null,
                    'gender' => $gender ?: null,
                    'ulan_url' => $ulan ?: null,
                    'wikidata_url' => $wiki ?: null,
                ]);
            }
            $this->cache['constituents'][$key] = $id;
        }
        return $this->cache['constituents'][$key];
    }

    private function getConstituentByDetails($name, $bio, $sort, $begin, $end, $gender, $ulan, $wiki)
    {
        $key = 'name|' . $name;
        if (!isset($this->cache['constituents'][$key])) {
            $id = DB::table('constituents')->where('display_name', $name)->value('constituent_id');
            if (!$id) {
                $id = DB::table('constituents')->insertGetId([
                    'display_name' => $name ?: 'Unknown',
                    'display_bio' => $bio ?: null,
                    'alpha_sort' => $sort ?: null,
                    'birth_year' => is_numeric($begin) ? $begin : null,
                    'death_year' => is_numeric($end) ? $end : null,
                    'gender' => $gender ?: null,
                    'ulan_url' => $ulan ?: null,
                    'wikidata_url' => $wiki ?: null,
                ]);
            }
            $this->cache['constituents'][$key] = $id;
        }
        return $this->cache['constituents'][$key];
    }
}
