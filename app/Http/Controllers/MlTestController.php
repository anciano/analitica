<?php

namespace App\Http\Controllers;

use App\Models\MlModel;
use App\Models\MlModelVersion;
use Illuminate\Http\Request;

class MlTestController extends Controller
{
    public function index()
    {
        $activeModels = MlModel::with('activeVersion')->whereNotNull('active_version_id')->get();
        return view('ml.test.index', compact('activeModels'));
    }

    public function predict(Request $request)
    {
        // Enviar petición al motor FastAPI
        $client = new \GuzzleHttp\Client();
        
        try {
            $fastApiUrl = env('ML_ENGINE_URL', 'http://localhost:8000');
            $response = $client->post($fastApiUrl . '/predict/patient-path', [
                'json' => [
                    'sexo' => $request->sexo,
                    'edad' => (int)$request->edad,
                    'tiene_vm' => $request->tiene_vm ? 1 : 0, // Added this too
                    'dx_principal' => $request->diagnostico_principal, // Mapping
                    'dx_secundarios' => array_filter(array_map('trim', explode(',', $request->diagnosticos_secundarios))),
                    'proc_principal' => $request->procedimiento_principal,
                    'proc_secundarios' => array_filter(array_map('trim', explode(',', $request->procedimientos_secundarios))),
                ],
                'timeout' => 5,
            ]);

            $data = json_decode($response->getBody()->getContents(), true);

            // Bloque de Valorización Estimada
            $basePrice = (int) env('ML_GRD_BASE_PRICE', 4050000);
            $peso = $data['peso_estimado']['valor'];
            $rangoPeso = $data['peso_estimado']['rango'];
            $dias = $data['estancia_estimada']['esperada_dias'];
            $rangoDias = $data['estancia_estimada']['rango_dias'];

            $montoTotal = $peso * $basePrice;
            $montoRango = [
                $rangoPeso[0] * $basePrice,
                $rangoPeso[1] * $basePrice
            ];

            // Cálculo diario (evitar división por cero)
            $valorDia = $dias > 0 ? ($montoTotal / $dias) : $montoTotal;
            $valorDiaRango = [
                $rangoDias[1] > 0 ? ($montoRango[0] / $rangoDias[1]) : $montoRango[0],
                $rangoDias[0] > 0 ? ($montoRango[1] / $rangoDias[0]) : $montoRango[1]
            ];

            $data['valorizacion_estimada'] = [
                'precio_base_grd_hospital' => $basePrice,
                'monto_total' => round($montoTotal),
                'monto_rango' => [round($montoRango[0]), round($montoRango[1])],
                'valor_dia' => round($valorDia),
                'valor_dia_rango' => [round($valorDiaRango[0]), round($valorDiaRango[1])]
            ];

            return response()->json($data);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al comunicarse con el motor predictivo: ' . $e->getMessage()
            ], 500);
        }
    }
}
