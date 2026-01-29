@extends('layouts.app')

@section('content')
    <div class="mb-4">
        <h1 class="mb-2">Nuevos Registros de Datos</h1>
        <p style="color: var(--text-muted)">Sube archivos Excel/CSV para validación y carga en el sistema.</p>
    </div>

    <div class="card" style="max-width: 600px;">
        <form action="{{ route('imports.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-size: 0.875rem;">Dataset y Versión</label>
                <select name="dataset_version_id"
                    style="width: 100%; padding: 0.75rem; background: rgba(15, 23, 42, 0.5); border: 1px solid var(--border); border-radius: 0.5rem; color: var(--text);">
                    @foreach($versions as $version)
                        <option value="{{ $version->id }}">{{ $version->dataset->name }} ({{ $version->version }})</option>
                    @endforeach
                </select>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
                <div>
                    <label style="display: block; margin-bottom: 0.5rem; font-size: 0.875rem;">Año</label>
                    <input type="number" name="anio" value="{{ date('Y') }}"
                        style="width: 100%; padding: 0.75rem; background: rgba(15, 23, 42, 0.5); border: 1px solid var(--border); border-radius: 0.5rem; color: var(--text);">
                </div>
                <div>
                    <label style="display: block; margin-bottom: 0.5rem; font-size: 0.875rem;">Mes</label>
                    <select name="mes"
                        style="width: 100%; padding: 0.75rem; background: rgba(15, 23, 42, 0.5); border: 1px solid var(--border); border-radius: 0.5rem; color: var(--text);">
                        @for($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}" {{ date('n') == $i ? 'selected' : '' }}>
                                {{ date('F', mktime(0, 0, 0, $i, 1)) }}
                            </option>
                        @endfor
                    </select>
                </div>
            </div>

            <div style="margin-bottom: 2rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-size: 0.875rem;">Archivo (Excel / CSV)</label>
                <div
                    style="border: 2px dashed var(--border); border-radius: 1rem; padding: 2rem; text-align: center; background: rgba(255, 255, 255, 0.02);">
                    <input type="file" name="file" required style="cursor: pointer;">
                    <p style="font-size: 0.75rem; color: var(--text-muted); margin-top: 1rem;">Máximo 10MB</p>
                </div>
            </div>

            <button type="submit" style="width: 100%; justify-content: center; padding: 1rem;">Iniciar
                Procesamiento</button>
        </form>
    </div>
@endsection