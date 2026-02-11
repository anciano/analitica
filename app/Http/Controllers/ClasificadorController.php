<?php

namespace App\Http\Controllers;

use App\Models\FinClasificadorItem;
use Illuminate\Http\Request;

class ClasificadorController extends Controller
{
    public function index(Request $request)
    {
        $query = FinClasificadorItem::orderBy('codigo');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('codigo', 'like', "%{$search}%")
                    ->orWhere('denominacion', 'like', "%{$search}%");
            });
        }

        if ($request->filled('nivel')) {
            $query->where('nivel', $request->nivel);
        }

        if ($request->filled('anio')) {
            $query->where('anio_vigencia', $request->anio);
        } else {
            $query->where('anio_vigencia', 2026);
        }

        $items = $query->paginate(20)->withQueryString();
        $anios = FinClasificadorItem::distinct()->pluck('anio_vigencia')->sortDesc();

        return view('programacion.clasificador.index', compact('items', 'anios'));
    }

    public function create()
    {
        $anios = [2025, 2026, 2027];
        return view('programacion.clasificador.create', compact('anios'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'codigo' => 'required|string|max:20',
            'denominacion' => 'required|string|max:255',
            'anio_vigencia' => 'required|integer',
            'activo' => 'boolean',
        ]);

        // La jerarquía se calcula en el model (booted)
        FinClasificadorItem::create($validated);

        return redirect()->route('programacion.clasificador.index')
            ->with('success', 'Ítem creado exitosamente.');
    }

    public function edit($id)
    {
        $item = FinClasificadorItem::findOrFail($id);
        $anios = [2025, 2026, 2027];
        return view('programacion.clasificador.edit', compact('item', 'anios'));
    }

    public function update(Request $request, $id)
    {
        $item = FinClasificadorItem::findOrFail($id);

        $validated = $request->validate([
            'codigo' => 'required|string|max:20',
            'denominacion' => 'required|string|max:255',
            'anio_vigencia' => 'required|integer',
            'activo' => 'boolean',
        ]);

        $item->update($validated);

        return redirect()->route('programacion.clasificador.index')
            ->with('success', 'Ítem actualizado exitosamente.');
    }

    public function destroy($id)
    {
        $item = FinClasificadorItem::findOrFail($id);
        $item->update(['activo' => false]);

        return redirect()->route('programacion.clasificador.index')
            ->with('success', 'Ítem desactivado exitosamente.');
    }
}
