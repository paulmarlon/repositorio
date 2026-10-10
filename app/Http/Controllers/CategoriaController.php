<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use Illuminate\Http\Request;

class CategoriaController extends Controller
{
    public function index()
    {
        $categorias = Categoria::all();
        return view('admin.categorias.index', compact('categorias'));
    }

    public function create()
    {
        return view('admin.categorias.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:categorias,nombre',
        ]);

        Categoria::create($request->all());

        return redirect()->route('admin.categorias.index')
            ->with('mensaje', 'Categoría creada exitosamente.')
            ->with('icon', 'success');
    }

    public function edit(Categoria $categoria)
    {
        return view('admin.categorias.edit', compact('categoria'));
    }

    public function update(Request $request, Categoria $categoria)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:categorias,nombre,' . $categoria->id,
        ]);

        $categoria->update($request->all());

        return redirect()->route('admin.categorias.index')
            ->with('mensaje', 'Categoría actualizada exitosamente.')
            ->with('icon', 'success');
    }

    public function destroy(Categoria $categoria)
    {
        $categoria->delete();

        return redirect()->route('admin.categorias.index')
            ->with('mensaje', 'Categoría enviada a la papelera.')
            ->with('icon', 'success');
    }

    public function trash()
    {
        $categorias = Categoria::onlyTrashed()->get();
        return view('admin.categorias.trash', compact('categorias'));
    }

    public function restore(int $id)
    {
        $categoria = Categoria::onlyTrashed()->findOrFail($id);
        $categoria->restore();

        return redirect()->route('admin.categorias.index')
            ->with('mensaje', 'Categoría restaurada exitosamente.')
            ->with('icon', 'success');
    }
}
