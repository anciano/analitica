<?php

namespace App\Http\Controllers;

use App\Models\DatasetVersion;
use App\Models\ImportRun;
use App\Jobs\StageImportJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ImportController extends Controller
{
    public function index()
    {
        $imports = ImportRun::with('version.dataset')
            ->latest()
            ->paginate(10);

        return view('imports.index', compact('imports'));
    }

    public function create()
    {
        // Simplificación: Pre-seleccionamos la versión 1 de Ejecución Presupuestaria
        $versions = DatasetVersion::with('dataset')
            ->where('is_active', true)
            ->get();

        $defaultVersionId = $versions->first() ? $versions->first()->id : null;

        return view('imports.create', compact('versions', 'defaultVersionId'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'dataset_version_id' => 'required|exists:dataset_versions,id',
            'file' => 'required|file|mimes:xlsx,xls,csv',
            'anio' => 'required|integer|min:2020|max:2030',
            'mes' => 'required|integer|min:1|max:12',
        ]);

        $file = $request->file('file');
        $fileName = time() . '_' . $file->getClientOriginalName();
        $file->storeAs('imports', $fileName);

        $importRun = ImportRun::create([
            'dataset_version_id' => $request->dataset_version_id,
            'user_id' => Auth::id(),
            'file_name' => 'imports/' . $fileName,
            'target_anio' => $request->anio,
            'target_mes' => $request->mes,
            'status' => 'pending',
        ]);

        StageImportJob::dispatch($importRun);

        return redirect()->route('imports.index')->with('success', 'Archivo recibido. Procesamiento iniciado.');
    }

    public function show(ImportRun $import)
    {
        $import->load(['errors', 'version.dataset']);
        return view('imports.show', compact('import'));
    }

    public function edit(ImportRun $import)
    {
        return view('imports.edit', compact('import'));
    }

    public function update(Request $request, ImportRun $import)
    {
        $request->validate([
            'target_anio' => 'required|integer|min:2020|max:2030',
            'target_mes' => 'required|integer|min:1|max:12',
        ]);

        $import->update([
            'target_anio' => $request->target_anio,
            'target_mes' => $request->target_mes,
        ]);

        // Actualización en cascada en la tabla de fact
        DB::table('fin_ejecucion_fact')
            ->where('import_run_id', $import->id)
            ->update([
                'anio' => $request->target_anio,
                'mes' => $request->target_mes,
            ]);

        return redirect()->route('imports.index')->with('success', 'Referencia temporal actualizada correctamente.');
    }

    public function destroy(ImportRun $import)
    {
        // Borrado en cascada manual (o por BD si tuviera FK con delete cascade)
        DB::table('fin_ejecucion_fact')
            ->where('import_run_id', $import->id)
            ->delete();

        $import->delete();

        return redirect()->route('imports.index')->with('success', 'Carga eliminada correctamente.');
    }
}
