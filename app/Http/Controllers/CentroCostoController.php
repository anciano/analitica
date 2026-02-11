<?php

namespace App\Http\Controllers;

use App\Models\FinCentroCosto;
use Illuminate\Http\Request;

class CentroCostoController extends Controller
{
    public function index(Request $request)
    {
        $query = FinCentroCosto::with('parent');

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($qq) use ($q) {
                $qq->where('nombre', 'ilike', "%$q%")
                    ->orWhere('codigo', 'ilike', "%$q%");
            });
        }

        if ($request->filled('nivel')) {
            $query->where('nivel', $request->nivel);
        }

        if ($request->has('activo')) {
            $query->where('activo', $request->activo == '1');
        }

        $centros = $query->orderBy('codigo')->get();
        return view('programacion.centros-costo.index', compact('centros'));
    }

    public function create()
    {
        return view('programacion.centros-costo.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'codigo' => 'required|string|unique:fin_centros_costo',
            'nombre' => 'required|string',
        ]);

        FinCentroCosto::create($validated);

        return redirect()->route('programacion.centros-costo.index')
            ->with('success', 'Centro de Costo creado exitosamente.');
    }

    public function edit($id)
    {
        $centro = FinCentroCosto::findOrFail($id);
        return view('programacion.centros-costo.edit', compact('centro'));
    }

    public function update(Request $request, $id)
    {
        $centro = FinCentroCosto::findOrFail($id);

        $validated = $request->validate([
            'codigo' => 'required|string|unique:fin_centros_costo,codigo,' . $id,
            'nombre' => 'required|string',
            'activo' => 'required|boolean',
        ]);

        $centro->update($validated);

        return redirect()->route('programacion.centros-costo.index')
            ->with('success', 'Centro de Costo actualizado exitosamente.');
    }

    public function destroy($id)
    {
        $centro = FinCentroCosto::findOrFail($id);

        if ($centro->planItems()->exists()) {
            $centro->update(['activo' => false]);
            return redirect()->back()->with('success', 'El Centro de Costo tiene programación asociada. Se ha desactivado en lugar de eliminar.');
        }

        $centro->delete();
        return redirect()->route('programacion.centros-costo.index')
            ->with('success', 'Centro de Costo eliminado exitosamente.');
    }
}
