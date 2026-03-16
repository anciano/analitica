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

            // Registrar la predicción en la BD (Opcional por ahora, pero recomendado)
            // \App\Models\MlPredictionLog::create([...]);

            return response()->json($data);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al comunicarse con el motor predictivo: ' . $e->getMessage()
            ], 500);
        }
    }
}
