<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware; // 1. Importar la interfaz
use Illuminate\Routing\Controllers\Middleware;    // 2. Importar Middleware class
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class PerfilController extends Controller implements HasMiddleware // 3. Implementar la interfaz
{
    // 4. Definir el método estático middleware()
    public static function middleware(): array
    {
        return [
            'auth', // O usando new Middleware('auth')
        ];
    }

    public function index()
    {
        return view('perfil.index');
    }

    public function store(Request $request)
    {
        //Modificar el Request
        $request->request->add(['username' => Str::slug($request->username)]);

        $request->validate([
            'username' => ['required', 'unique:users,username,' . auth()->user()->id, 'min:3', 'max:20', 'not_in:twitter,editar-perfil']
        ]);

        if($request->imagen){
            if (!$request->hasFile('imagen')) {
                return response()->json(['error' => 'No se subió ningún archivo'], 400);
            }

            $imagen = $request->file('imagen');
            $nombreImagen = Str::uuid() . "." . $imagen->extension();

            // 1. Instanciar Intervention v3 con el driver GD
            $manager = new ImageManager(new Driver());

            // 2. Leer la imagen y recortarla proporcionalmente a 1000x1000 (equivalente a fit() de v2)
            $imagenServidor = $manager->read($imagen->getRealPath());
            $imagenServidor->cover(1000, 1000);

            // 3. Verificar directorio
            $directorio = public_path('perfiles');
            if (!file_exists($directorio)) {
                mkdir($directorio, 0755, true);
            }

            // 4. Guardar la imagen procesada
            $imagenPath = $directorio . '/' . $nombreImagen;
            $imagenServidor->save($imagenPath);
        }

        // Guardar cambios
        $usuario = User::find(auth()->user()->id);
        $usuario->username = $request->username;
        $usuario->imagen = $nombreImagen ?? auth()->user()->imagen ?? null;
        $usuario->save();

        // Redireccionar
        return redirect()->route('posts.index', $usuario->username);
    }
}