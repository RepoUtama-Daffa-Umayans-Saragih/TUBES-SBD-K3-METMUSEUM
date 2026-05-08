<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClassificationSeeder extends Seeder
{
    public function run()
    {
        $classifications = [
            'Relief prints',
            'Lithographs',
            'Planographic prints',
            'Woodcuts',
            'Engraving',
            'Color lithographs',
            'Lithography',
            'Offset lithography',
            'Offset lithographs',
            'Wood engravings',
            'Photomechanical reproductions',
            'Photolithographs',
            'Etching',
            'Photoengraving',
            'Stipple engraving',
            'Metalwork'
        ];

        foreach ($classifications as $classification) {
            DB::table('classifications')->updateOrInsert(
                ['classification_name' => $classification],
                ['classification_name' => $classification]
            );
        }
    }
}
