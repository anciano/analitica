<?php

namespace App\Http\Controllers;

use App\Models\FinCentroCosto;
use App\Models\FinPlanItem;
use App\Models\FinEjecucionFact;
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

        // Buscar todos los IDs de la rama (padre e hijos) usando el prefijo del código
        // Esto es seguro dado que nuestra estructura jerárquica se basa en la extensión del código
        $subtreeIds = FinCentroCosto::where('codigo', 'like', $centro->codigo . '%')->pluck('id');

        // Verificar si algún nodo de la rama tiene programación o ejecución vinculada
        $hasPlanning = FinPlanItem::whereIn('centro_costo_id', $subtreeIds)->exists();
        $hasExecution = FinEjecucionFact::whereIn('centro_costo_id', $subtreeIds)->exists();

        if ($hasPlanning || $hasExecution) {
            // Si hay datos, desactivamos la rama completa en lugar de borrar para evitar errores de FK
            FinCentroCosto::whereIn('id', $subtreeIds)->update(['activo' => false]);
            return redirect()->back()->with('success', 'El Centro de Costo o sus dependencias tienen datos (presupuesto o ejecución) asociados. Se ha desactivado la rama completa.');
        }

        // Si no hay datos vinculados, procedemos con el borrado (el cascade en DB limpia los hijos en fin_centros_costo)
        $centro->delete();
        return redirect()->route('programacion.centros-costo.index')
            ->with('success', 'Centro de Costo eliminado exitosamente.');
    }
}
