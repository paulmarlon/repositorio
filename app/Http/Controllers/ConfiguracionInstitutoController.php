<?php

namespace App\Http\Controllers;

use App\Models\ConfiguracionInstituto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ConfiguracionInstitutoController extends Controller
{
    public function index()
    {
        // Como es una tabla de configuración única, obtenemos el primer registro o creamos uno vacío por defecto
        $instituto = ConfiguracionInstituto::first() ?? new ConfiguracionInstituto();

        return view('configuracion.index', compact('instituto'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'nombre_instituto' => ['required', 'string', 'max:255'],
            'direccion' => ['nullable', 'string', 'max:255'],
            'celular' => ['nullable', 'string', 'max:50'],
            'latitud' => ['nullable', 'numeric', 'between:-90,90'],
            'longitud' => ['nullable', 'numeric', 'between:-180,180'],
            'logo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,svg', 'max:2048'],
        ]);

        $instituto = ConfiguracionInstituto::first() ?? new ConfiguracionInstituto();

        $instituto->nombre_instituto = $request->nombre_instituto;
        $instituto->direccion = $request->direccion;
        $instituto->celular = $request->celular;
        $instituto->latitud = $request->latitud;
        $instituto->longitud = $request->longitud;

if ($request->hasFile('logo')) {
            if ($instituto->logo) {
                Storage::disk('public')->delete($instituto->logo);
            }

            // 1. Obtenemos la extensión original del archivo (ej. png, jpg)
            $extension = $request->file('logo')->getClientOriginalExtension();

            // 2. Construimos un nombre personalizado limpio (ej: logo_2026-11-04_191300.png)
            // O usando un identificador de tiempo preciso para evitar duplicados exactos
            $nombrePersonalizado = 'logo_' . date('Ymd_His') . '_' . uniqid() . '.' . $extension;

            // 3. Guardamos el archivo especificando la carpeta y el nombre personalizado
            $instituto->logo = $request->file('logo')->storeAs('instituto', $nombrePersonalizado, 'public');
        }

        $instituto->save();

        return back()->with('success', 'Configuración actualizada correctamente.');
    }
}
