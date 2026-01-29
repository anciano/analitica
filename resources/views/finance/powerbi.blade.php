@extends('layouts.app')

@section('content')
    <style>
        .powerbi-container {
            background: #FFFFFF;
            border: 1px solid #E6ECF2;
            border-radius: 12px;
            overflow: hidden;
            height: calc(100vh - 200px);
            /* Adjust based on header height */
            min-height: 600px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            position: relative;
        }

        .powerbi-iframe {
            width: 100%;
            height: 100%;
            border: none;
        }

        .no-url-message {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            color: #6B7280;
        }

        .no-url-message svg {
            color: #3B82F6;
            margin-bottom: 1rem;
        }

        .page-header {
            margin-bottom: 1.5rem;
        }

        .page-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #1F2937;
        }
    </style>

    <div class="page-header">
        <h1 class="page-title">Tableros Power BI</h1>
        <p style="color: #6B7280; font-size: 0.875rem;">Análisis avanzado y visualizaciones interactivas de gestión
            hospitalaria.</p>
    </div>

    <div class="powerbi-container">
        @if($url && !str_contains($url, 'eyJrIjoi...'))
            <iframe class="powerbi-iframe" src="{{ $url }}" frameborder="0" allowFullScreen="true">
            </iframe>
        @else
            <div class="no-url-message">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect>
                    <line x1="8" y1="21" x2="16" y2="21"></line>
                    <line x1="12" y1="17" x2="12" y2="21"></line>
                </svg>
                <h3 style="font-size: 1.125rem; font-weight: 600; color: #374151; margin-bottom: 0.5rem;">Reporte no configurado
                </h3>
                <p>Para visualizar el reporte, debe configurar la variable <code>POWER_BI_URL</code> en su archivo
                    <code>.env</code>.</p>
            </div>
        @endif
    </div>
@endsection