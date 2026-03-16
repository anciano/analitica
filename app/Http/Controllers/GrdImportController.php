<?php

namespace App\Http\Controllers;

use App\Models\ImportRun;
use App\Models\DatasetVersion;
use App\Jobs\GrdStageImportJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GrdImportController extends Controller
{
    public function index()
    {
        $imports = ImportRun::whereHas('version.dataset', function($q) {
                $q->where('slug', 'grd_egresos');
            })
            ->with('version.dataset')
            ->latest()
            ->paginate(10);

        return view('grd.import.index', compact('imports'));
    }

    public function create()
    {
        $versions = DatasetVersion::whereHas('dataset', function($q) {
                $q->where('slug', 'grd_egresos');
            })
            ->where('is_active', true)
            ->get();

        $defaultVersionId = $versions->first() ? $versions->first()->id : null;

        return view('grd.import.create', compact('versions', 'defaultVersionId'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'dataset_version_id' => 'required|exists:dataset_versions,id',
            'file' => 'required|file|mimes:xlsx,xls,csv',
            'anio' => 'required|integer|min:2018|max:2030',
            'mes' => 'required|integer|min:1|max:12',
        ]);

        $file = $request->file('file');
        $fileName = time() . '_grd_' . $file->getClientOriginalName();
        $file->storeAs('imports/grd', $fileName);

        $importRun = ImportRun::create([
            'dataset_version_id' => $request->dataset_version_id,
            'user_id' => Auth::id(),
            'file_name' => 'imports/grd/' . $fileName,
            'target_anio' => $request->anio,
            'target_mes' => $request->mes,
            'status' => 'pending',
        ]);

        GrdStageImportJob::dispatch($importRun);

        return redirect()->route('grd.import.index')->with('success', 'Archivo GRD recibido. Procesamiento iniciado.');
    }

    public function show(ImportRun $import)
    {
        $import->load(['errors', 'version.dataset']);
        return view('grd.import.show', compact('import'));
    }

    public function destroy(ImportRun $import)
    {
        DB::table('grd_egresos_fact')
            ->where('import_run_id', $import->id)
            ->delete();

        $import->delete();

        return redirect()->route('grd.import.index')->with('success', 'Carga de GRD eliminada correctamente.');
    }
}
