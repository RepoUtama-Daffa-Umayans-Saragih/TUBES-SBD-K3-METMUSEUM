<?php

namespace Database\Seeders;

use Database\Seeders\Concerns\ReadsMetMuseumCsv;
use Illuminate\Database\Seeder;

class ProvenanceSeeder extends Seeder
{
    use ReadsMetMuseumCsv;

    public function run(): void
    {
        [$handle, $headers, $delimiter, $meta] = $this->readCsvRows('metmuseum_provenance_final.csv', ';');

        if (!empty($meta['file_missing'])) {
            $this->consoleWarn('Provenance CSV not found: ' . $meta['path']);
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
            $provenance = $this->normalizeText($data['provenance'] ?? '');

            if ($provenance === '') {
                $counts['skipped_empty']++;
                continue;
            }

            $artwork = $this->findArtworkByMetObjectId($metObjectId);
            if (!$artwork) {
                $counts['skipped_missing_artwork']++;
                $this->consoleWarn('Provenance skipped, artwork missing: ' . $metObjectId);
                continue;
            }

            if ($artwork->provenance !== $provenance) {
                $artwork->provenance = $provenance;
                $artwork->save();
                $counts['updated']++;
            }
        }

        fclose($handle);

        $this->consoleInfo('Provenance imported: 0');
        $this->consoleInfo('Provenance updated: ' . $counts['updated']);
        $this->consoleInfo('Provenance skipped artwork missing: ' . $counts['skipped_missing_artwork']);
        $this->consoleInfo('Provenance skipped empty: ' . $counts['skipped_empty']);
    }
}