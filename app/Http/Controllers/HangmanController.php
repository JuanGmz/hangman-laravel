<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class HangmanController extends Controller
{
    public function jugar()
    {
        $palabras = ["perro", "gato", "oso", "loro", "rata"];
        $palabra = $palabras[array_rand($palabras)];

        $acertadas = array_fill(0, strlen($palabra), '_');

        $intentos = 5;

        return response()->json([
            "prueba" => str_repeat('-', strlen($palabra)),
            "intentos" => $intentos
        ], 200)
            ->cookie('palabra', $palabra)
            ->cookie('intentos', $intentos)
            ->cookie('acertadas', $acertadas);
    }

    public function adivinar(Request $request)
    {
        $palabra = $request->cookie('palabra');

        $longitud = strlen($palabra);

        $acertadas = $request->cookie('acertadas');

        $intentos = $request->cookie('intentos');

        $encontrada = false;

        $validated = Validator::make($request->all(), [
            "letra" => "string|max:1",
        ]);

        $letra = $request->letra;

        if ($validated->fails()) {
            return response()->json([
                'error' => $validated->errors(),
            ], 400);
        }

        for ($i = 0; $i < $longitud; $i++) {
            if ($letra == $palabra[$i]) {
                $acertadas[$i] = $letra;
                $encontrada = true;
            }
        }

        if (!$encontrada) {
            $intentos--;
        }

        if ($intentos <= 0) {
            return response()->json([
                "fin del juego" => "Has perdido",
                "palabra" => $palabra
            ])
            ->cookie('palabra', $palabra)
            ->cookie('acertadas', $acertadas)
            ->cookie('intentos', $intentos);
        };

        return response()->json([
            "palabra" => $acertadas,
            "intentos" => $intentos,
        ]);
    }
}
