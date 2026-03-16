@extends('layouts.app')

@section('content')
<div class="mb-8">
    <a href="{{ route('grd.import.index') }}" class="text-sm text-blue-500 hover:underline flex items-center gap-1 mb-2">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path d="M19 12H5M12 19l-7-7 7-7"></path>
        </svg>
        Volver al historial
    </a>
    <h1 class="text-2xl font-bold text-[--text-main]">Detalles de Carga #{{ $import->id }}</h1>
    <p class="text-[--text-muted] text-sm mt-1">Resumen de procesamiento para los egresos de {{ $import->target_mes }}/{{ $import->target_anio }}</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <div class="card flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center text-blue-600">
            <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </div>
        <div>
            <p class="text-xs text-[--text-muted] uppercase font-bold tracking-wider">Estado</p>
            <p class="text-lg font-bold text-[--text-main] capitalize">{{ $import->status }}</p>
        </div>
    </div>
    <div class="card flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-green-100 flex items-center justify-center text-green-600">
            <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
            </svg>
        </div>
        <div>
            <p class="text-xs text-[--text-muted] uppercase font-bold tracking-wider">Filas Válidas</p>
            <p class="text-lg font-bold text-[--text-main]">{{ number_format($import->valid_rows) }} / {{ number_format($import->total_rows) }}</p>
        </div>
    </div>
    <div class="card flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-red-100 flex items-center justify-center text-red-600">
            <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </div>
        <div>
            <p class="text-xs text-[--text-muted] uppercase font-bold tracking-wider">Errores</p>
            <p class="text-lg font-bold text-[--text-main]">{{ number_format($import->error_rows) }}</p>
        </div>
    </div>
</div>

@if($import->errors->count() > 0)
<div class="card">
    <h3 class="text-lg font-bold text-[--text-main] mb-4">Errores de Validación</h3>
    <table class="sing-table">
        <thead>
            <tr>
                <th>Fila</th>
                <th>Columna</th>
                <th>Mensaje</th>
            </tr>
        </thead>
        <tbody>
            @foreach($import->errors as $error)
                <tr>
                    <td class="font-bold">#{{ $error->row_number }}</td>
                    <td class="text-red-500">{{ $error->column_name }}</td>
                    <td>{{ $error->error_message }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@else
<div class="card py-12 text-center">
    <div class="w-16 h-16 rounded-full bg-green-50 flex items-center justify-center mx-auto mb-4">
        <svg width="32" height="32" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" class="text-green-500">
            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
    </div>
    <h3 class="text-xl font-bold text-[--text-main]">¡Sin errores!</h3>
    <p class="text-[--text-muted] max-w-md mx-auto mt-2">Todos los registros han sido procesados y cargados correctamente a la tabla de egresos hospitalarios.</p>
</div>
@endif
@endsection
