<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ImagenController extends Controller
{
    public function store(Request $request)
    {
        if (!$request->hasFile('file')) {
            return response()->json(['error' => 'No se subió ningún archivo'], 400);
        }

        $imagen = $request->file('file');
        $nombreImagen = Str::uuid() . "." . $imagen->extension();

        // 1. Instanciar Intervention v3 con el driver GD
        $manager = new ImageManager(new Driver());

        // 2. Leer la imagen y recortarla proporcionalmente a 1000x1000 (equivalente a fit() de v2)
        $imagenServidor = $manager->read($imagen->getRealPath());
        $imagenServidor->cover(1000, 1000);

        // 3. Verificar directorio
        $directorio = public_path('uploads');
        if (!file_exists($directorio)) {
            mkdir($directorio, 0755, true);
        }

        // 4. Guardar la imagen procesada
        $imagenPath = $directorio . '/' . $nombreImagen;
        $imagenServidor->save($imagenPath);

        // 5. Retornar la respuesta JSON esperada por Dropzone
        return response()->json(['imagen' => $nombreImagen]);
    }
}