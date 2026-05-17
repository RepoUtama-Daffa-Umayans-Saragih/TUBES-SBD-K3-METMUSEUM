<?php

namespace Database\Seeders\Concerns;

use App\Models\ArtWork;

trait ReadsMetMuseumCsv
{
    protected function csvPath(string $filename): string
    {
        return database_path('data/' . $filename);
    }

    protected function readCsvRows(string $filename, ?string $forcedDelimiter = null): array
    {
        $path = $this->csvPath($filename);

        if (!is_file($path)) {
            return [null, null, null, [
                'file_missing' => true,
                'path' => $path,
            ]];
        }

        $handle = fopen($path, 'r');
        if ($handle === false) {
            return [null, null, null, [
                'file_missing' => true,
                'path' => $path,
            ]];
        }

        $headerLine = fgets($handle) ?: '';
        $delimiter = $forcedDelimiter ?: $this->detectDelimiter($headerLine);

        rewind($handle);

        $headers = fgetcsv($handle, 0, $delimiter, '"', '\\') ?: [];

        return [$handle, $headers, $delimiter, null];
    }

    protected function detectDelimiter(string $line): string
    {
        return substr_count($line, ';') > substr_count($line, ',') ? ';' : ',';
    }

    protected function mapCsvRow(array $headers, array $row, string $delimiter): array
    {
        if (count($row) === 1 && $delimiter === ';') {
            $fallback = str_getcsv($row[0], ',', '"', '\\');
            if (count($fallback) >= 3) {
                $row = $fallback;
            }
        }

        $mapped = [];
        foreach ($headers as $index => $header) {
            $key = trim((string) $header);
            if ($key === '') {
                continue;
            }

            $mapped[$key] = $row[$index] ?? null;
        }

        return $mapped;
    }

    protected function findArtworkByMetObjectId(mixed $metObjectId): ?ArtWork
    {
        if ($metObjectId === null || $metObjectId === '') {
            return null;
        }

        return ArtWork::where('met_object_id', (int) $metObjectId)->first();
    }

    protected function splitDelimitedValue(mixed $value, string $delimiter = '|'): array
    {
        if ($value === null) {
            return [];
        }

        $parts = array_map('trim', explode($delimiter, (string) $value));

        return array_values(array_filter($parts, static fn ($part) => $part !== ''));
    }

    protected function normalizeText(mixed $value): string
    {
        return trim((string) $value);
    }

    protected function parseDate(?string $value): ?string
    {
        $value = trim((string) $value);

        return $value !== '' ? $value : null;
    }

    protected function consoleInfo(string $message): void
    {
        if (isset($this->command)) {
            $this->command->info($message);
        }
    }

    protected function consoleWarn(string $message): void
    {
        if (isset($this->command)) {
            $this->command->warn($message);
        }
    }
}