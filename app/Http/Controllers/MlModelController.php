<?php

namespace App\Http\Controllers;

use App\Models\MlModel;
use App\Models\MlModelVersion;
use Illuminate\Http\Request;

class MlModelController extends Controller
{
    public function index()
    {
        $models = MlModel::with('activeVersion')->get();
        return view('ml.models.index', compact('models'));
    }

    public function show(MlModel $model)
    {
        $versions = $model->versions()->orderBy('created_at', 'desc')->get();
        return view('ml.models.show', compact('model', 'versions'));
    }

    public function activateVersion(Request $request, MlModel $model)
    {
        $versionId = $request->input('version_id');
        
        // Deactivate old version status if needed, but the FK active_version_id is the source of truth
        $model->update(['active_version_id' => $versionId]);

        return redirect()->back()->with('success', 'Versión activada correctamente para ' . $model->name);
    }
}
