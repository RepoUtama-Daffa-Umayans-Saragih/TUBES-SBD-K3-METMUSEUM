<?php

namespace Database\Seeders;

use Database\Seeders\Concerns\ReadsMetMuseumCsv;
use Illuminate\Database\Seeder;

class DescriptionSeeder extends Seeder
{
    use ReadsMetMuseumCsv;

    public function run(): void
    {
        [$handle, $headers, $delimiter, $meta] = $this->readCsvRows('metmuseum_descriptions.csv', ',');

        if (!empty($meta['file_missing'])) {
            $this->consoleWarn('Descriptions CSV not found: ' . $meta['path']);
            return;
        }

        $counts = [
            'updated' => 0,
            'skipped_missing_artwork' => 0,
            'skipped_empty' => 0,
        ];

        while (($row = fgetcsv($handle, 0, $delimiter, '"', '\\')) !== false) {
            $data = $this->mapCsvRow($headers, $row, $delimiter);
            $metObjectId = $data['object_id'] ?? $data['met_object_id'] ?? null;
            $description = $this->normalizeText($data['description'] ?? '');

            if ($description === '') {
                $counts['skipped_empty']++;
                continue;
            }

            $artwork = $this->findArtworkByMetObjectId($metObjectId);
            if (!$artwork) {
                $counts['skipped_missing_artwork']++;
                $this->consoleWarn('Descriptions skipped, artwork missing: ' . $metObjectId);
                continue;
            }

            if ($artwork->description !== $description) {
                $artwork->description = $description;
                $artwork->save();
                $counts['updated']++;
            }
        }

        fclose($handle);

        $this->consoleInfo('Descriptions imported: 0');
        $this->consoleInfo('Descriptions updated: ' . $counts['updated']);
        $this->consoleInfo('Descriptions skipped artwork missing: ' . $counts['skipped_missing_artwork']);
        $this->consoleInfo('Descriptions skipped empty: ' . $counts['skipped_empty']);
    }
}