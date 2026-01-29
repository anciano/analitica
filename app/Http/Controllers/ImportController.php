<?php

namespace App\Http\Controllers;

use App\Models\DatasetVersion;
use App\Models\ImportRun;
use App\Jobs\StageImportJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $versions = DatasetVersion::with('dataset')
            ->where('is_active', true)
            ->get();

        return view('imports.create', compact('versions'));
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

    public function show(ImportRun $importRun)
    {
        $importRun->load(['errors', 'version.dataset']);
        return view('imports.show', compact('importRun'));
    }
}
