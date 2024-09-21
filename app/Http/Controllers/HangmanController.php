<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class HangmanController extends Controller
{
    public function hangman()
    {
        // Definir el arreglo de palabras
        $palabras = ["perro", "gato", "oso", "loro", "rata"];
        // Seleccionar una palabra del arreglo aletoriamente
        $palabra = $palabras[array_rand($palabras)];

        // Definir cantidad de intentos
        $intentos = 5;

        // Retornar una respuesta para enviar la palabra, intentos y letras adivinadas mediante cookies
        return response()->json([
            "mensaje" => "El juego comenzó",
        ], 200)
        ->cookie('palabra', $palabra)
        ->cookie('intentos', $intentos)
        ->cookie('letrasAdivinadas', json_encode([]));
    }

    public function empezar(Request $request){
        // Obtener las cookies
        $palabra = $request->cookie('palabra');
        $letrasAdivinadas = json_decode($request->cookie('letrasAdivinadas'));
        $intentos = $request->cookie('intentos');
        // Definir variable de progreso para mostrar la palabra
        $progreso = "";

        // Validar que la letra sea una letra
        $validator = Validator::make($request->all(), [
            'letra' => 'required|string|max:1',
        ]);

        // Obtener la letra
        $letra = $request->letra;

        // Si el validador falla mostrar mensaje de error
        if ($validator->fails()) {
            return response()->json([
                "error" => $validator->errors(),
            ], 400);
        }

        // Si se envió una letra
        if ($letra) {
            // Convertir la letra a minúscula
            $letra = strtolower($letra);

            // Si la letra no fue adivinada se guarda en el arreglo de letras adivinadas
            if (!in_array($letra, $letrasAdivinadas)) {
                $letrasAdivinadas[] = $letra;
            }

            // Si la letra no existe en la palabra restar intentos
            if (strpos($palabra, $letra) === false) {
                $intentos--;
            }
        }

        // Recorrer la palabra para concatenar la palabra adivinada o el guión
        for ($i = 0; $i < strlen($palabra); $i++) {
            // Si la letra ya fue adivinada mostrarla
            if (in_array($palabra[$i], $letrasAdivinadas)) {
                $progreso .= $palabra[$i];
            } else {
                // Si no mostrar un guion
                $progreso .= "-";
            }
        }

        // Si la variable de progreso no contiene un guion se gana
        if (!str_contains($progreso, '-')) {
            return response()->json([
                "adivinar" => $progreso,
                "mensaje" => "Ganaste",
                "palabras enviadas" => $letrasAdivinadas
            ])
            // Guardar las cookies
            ->cookie('palabra', $palabra)
            ->cookie('intentos', $intentos)
            ->cookie('letrasAdivinadas', json_encode($letrasAdivinadas));
        } else if ($intentos <= 0) {
            return response()->json([
                "adivinar" => $progreso,
                "mensaje" => "Perdiste",
                "palabras enviadas" => $letrasAdivinadas
            ])
            // Guardar las cookies
            ->cookie('palabra', $palabra)
            ->cookie('intentos', $intentos)
            ->cookie('letrasAdivinadas', json_encode($letrasAdivinadas));
        } else {
            return response()->json([
                "adivinar" => $progreso,
                "intentos" => $intentos,
                "palabras enviadas" => $letrasAdivinadas
            ])
            // Guardar las cookies
            ->cookie('palabra', $palabra)
            ->cookie('intentos', $intentos)
            ->cookie('letrasAdivinadas', json_encode($letrasAdivinadas));
        }
    }
}