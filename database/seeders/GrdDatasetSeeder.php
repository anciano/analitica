<?php

namespace Database\Seeders;

use App\Models\Dataset;
use App\Models\DatasetVersion;
use Illuminate\Database\Seeder;

class GrdDatasetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $dataset = Dataset::updateOrCreate(
            ['slug' => 'grd_egresos'],
            ['name' => 'Egresos Hospitalarios (GRD)']
        );

        DatasetVersion::updateOrCreate(
            [
                'dataset_id' => $dataset->id,
                'version' => '1.0',
            ],
            ['is_active' => true]
        );
    }
}
