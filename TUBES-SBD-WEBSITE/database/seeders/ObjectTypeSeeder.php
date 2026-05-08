<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ObjectTypeSeeder extends Seeder
{
    public function run()
    {
        $types = [
            'Prints',
            'Books',
            'Illustrations',
            'Posters',
            'Drawings',
            'Costume',
            'Photographs',
            'Paintings',
            'Sculpture',
            'Ceremonial masks',
            'Masks',
            'Vessels',
            'Jugs',
            'Printing blocks',
            'Wood blocks',
            'Ephemera',
            'Salvers',
            'Card photographs',
            'Cartes-de-visite',
            'Albumen silver prints'
        ];

        foreach ($types as $type) {
            DB::table('object_types')->updateOrInsert(
                ['object_type_name' => $type],
                ['object_type_name' => $type]
            );
        }
    }
}
