<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FilterArtworkRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'department_id' => 'nullable|integer|exists:departments,department_id',
            'type_id' => 'nullable|integer|exists:object_types,type_id',
            'geo_id' => 'nullable|integer|exists:geo_locations,geo_id',
            'artist_id' => 'nullable|integer|exists:artists,artist_id',
            'year_start' => 'nullable|integer|min:0|max:9999',
            'year_end' => 'nullable|integer|min:0|max:9999|gte:year_start',
            'search' => 'nullable|string|max:255',
            'per_page' => 'nullable|integer|in:12,24,48',
            'page' => 'nullable|integer|min:1'
        ];
    }

    public function messages(): array
    {
        return [
            'year_end.gte' => 'End year must be greater than or equal to start year.',
            'department_id.exists' => 'Selected department does not exist.',
            'type_id.exists' => 'Selected type does not exist.',
            'geo_id.exists' => 'Selected location does not exist.',
            'artist_id.exists' => 'Selected artist does not exist.',
        ];
    }

    public function getCacheKey(): string
    {
        $filters = [
            'dept' => $this->input('department_id'),
            'type' => $this->input('type_id'),
            'geo' => $this->input('geo_id'),
            'artist' => $this->input('artist_id'),
            'yr_start' => $this->input('year_start'),
            'yr_end' => $this->input('year_end'),
            'search' => $this->input('search'),
            'page' => $this->input('page', 1),
            'per_page' => $this->input('per_page', 24),
        ];

        return 'artwork_' . md5(json_encode($filters));
    }
}
