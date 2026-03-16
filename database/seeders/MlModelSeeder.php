<?php

namespace Database\Seeders;

use App\Models\MlModel;
use Illuminate\Database\Seeder;

class MlModelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $models = [
            [
                'name' => 'Clasificador de GRD',
                'code' => 'grd_classifier',
                'description' => 'Predice el código GRD probable basado en diagnósticos y procedimientos.',
            ],
            [
                'name' => 'Regresor de Estancia',
                'code' => 'estancia_regressor',
                'description' => 'Predice los días de estancia hospitalaria esperados.',
            ],
            [
                'name' => 'Regresor de Peso GRD',
                'code' => 'peso_regressor',
                'description' => 'Predice el peso GRD relativo del egreso.',
            ],
        ];

        foreach ($models as $model) {
            MlModel::firstOrCreate(['code' => $model['code']], $model);
        }
    }
}
