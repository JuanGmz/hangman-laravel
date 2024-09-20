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

        $intentos = 5;

        return response()->json([
            "prueba" => str_repeat('-', strlen($palabra)),
            "intentos" => $intentos,
            "mensaje" => "El juego ha comenzado",
        ], 200)
            ->cookie('palabra', $palabra)
            ->cookie('intentos', $intentos)
            ->cookie('acertadas', json_encode([]));
    }

    public function adivinar(Request $request)
    {
        $palabra = $request->cookie('palabra');


        $acertadas = json_decode($request->cookie('acertadas', []), true);

        $intentos = $request->cookie('intentos');

        $validated = Validator::make($request->all(), [
            "letra" => "string|max:1",
        ]);

        $letra = $request->letra;

        if ($validated->fails()) {
            return response()->json([
                'error' => $validated->errors(),
            ], 400);
        }

        if (in_array($letra, $acertadas)) {
            return response()->json([
                "error" => "Ya has acertado esta letra",
            ], 400);
        }

        $acertadas[] = $letra;

        if (!in_array($letra, $palabra)) {
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
        }
        ;

        return response()->json([
            "palabra" => $acertadas,
            "intentos" => $intentos,
        ]);
    }
}
