<?php

namespace Database\Seeders;

use App\Models\ArtWorkExhibitionHistory;
use Database\Seeders\Concerns\ReadsMetMuseumCsv;
use Illuminate\Database\Seeder;

class ExhibitionHistorySeeder extends Seeder
{
    use ReadsMetMuseumCsv;

    public function run(): void
    {
        [$handle, $headers, $delimiter, $meta] = $this->readCsvRows('metmuseum_exhibition_history_final.csv', ',');

        if (!empty($meta['file_missing'])) {
            $this->consoleWarn('Exhibition history CSV not found: ' . $meta['path']);
            return;
        }

        $counts = [
            'imported' => 0,
            'updated' => 0,
            'duplicates_skipped' => 0,
            'skipped_missing_artwork' => 0,
            'skipped_empty' => 0,
        ];

        while (($row = fgetcsv($handle, 0, $delimiter, '"', '\\')) !== false) {
            $data = $this->mapCsvRow($headers, $row, $delimiter);
            $metObjectId = $data['met_object_id'] ?? $data['object_id'] ?? null;

            $artwork = $this->findArtworkByMetObjectId($metObjectId);
            if (!$artwork) {
                $counts['skipped_missing_artwork']++;
                $this->consoleWarn('Exhibition skipped, artwork missing: ' . $metObjectId);
                continue;
            }

            $match = [
                'art_work_id' => $artwork->art_work_id,
                'exhibition_title' => $this->normalizeText($data['exhibition_title'] ?? ''),
                'venue_name' => $this->normalizeText($data['venue_name'] ?? ''),
                'city_name' => $this->normalizeText($data['city_name'] ?? ''),
                'exhibition_date_display' => $this->normalizeText($data['exhibition_date_display'] ?? ''),
                'start_date' => $this->parseDate($data['start_date'] ?? null),
                'end_date' => $this->parseDate($data['end_date'] ?? null),
                'display_order' => (int) ($data['display_order'] ?? 1),
            ];

            if ($match['exhibition_title'] === '') {
                $counts['skipped_empty']++;
                continue;
            }

            $values = [
                'catalogue_reference' => $this->normalizeText($data['catalogue_reference'] ?? ''),
                'exhibition_notes' => null,
            ];

            $existing = ArtWorkExhibitionHistory::withTrashed()->where($match)->first();
            if ($existing) {
                $changed = false;

                if ($existing->trashed()) {
                    $existing->restore();
                    $changed = true;
                }

                foreach ($values as $key => $value) {
                    if ($existing->{$key} !== $value) {
                        $existing->{$key} = $value;
                        $changed = true;
                    }
                }

                if ($changed) {
                    $existing->save();
                    $counts['updated']++;
                } else {
                    $counts['duplicates_skipped']++;
                }

                continue;
            }

            $record = new ArtWorkExhibitionHistory($match + $values);
            $record->save();
            $counts['imported']++;
        }

        fclose($handle);

        $this->consoleInfo('Exhibition imported: ' . $counts['imported']);
        $this->consoleInfo('Exhibition updated: ' . $counts['updated']);
        $this->consoleInfo('Exhibition duplicates skipped: ' . $counts['duplicates_skipped']);
        $this->consoleInfo('Exhibition skipped artwork missing: ' . $counts['skipped_missing_artwork']);
        $this->consoleInfo('Exhibition skipped empty: ' . $counts['skipped_empty']);
    }
}