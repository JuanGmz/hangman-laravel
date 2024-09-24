<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class HangmanController extends Controller
{
    public function palabra(Request $request, int $indice = null, bool $jugar = false)
    {
        $palabras = ["perro", "gato", "oso", "loro", "rata"];

        if ($indice >= 0 && $indice < count($palabras)) {
            if ($jugar) {
                $palabra = $palabras[$indice];

                $letras = $request->letras;
    
                $letrasAdivinadas = []; 
    
                $intentos = 6;
    
                $validator = Validator::make($request->all(), [
                    'letras' => 'array',
                    'letras.*' => 'string|min:1|max:1',
                ]);
    
                if ($validator->fails()) {
                    return response()->json([
                        "error" => $validator->errors(),
                    ], 400);
                }
    
                foreach ($letras as $letra) {
                    $letra = strtolower($letra);
    
                    if (!in_array($letra, $letrasAdivinadas)) {
                        $letrasAdivinadas[] = $letra;
    
                        if (strpos($palabra, $letra) === false) {
                            $intentos--;
                        }
                    }
                }
    
                $progreso = '';
                for ($i = 0; $i < strlen($palabra); $i++) {
                    if (in_array($palabra[$i], $letrasAdivinadas)) {
                        $progreso .= $palabra[$i];
                    } else {
                        $progreso .= "-";
                    }
                }
    
                if ($progreso === $palabra) {
                    return response()->json([
                        "mensaje" => "Felicidades, has adivinado la palabra",
                    ], 200);
                } else if ($intentos === 0) {
                    return response()->json([
                        "mensaje" => "Has perdido",
                    ], 200);
                } else {
                    return response()->json([
                        "progreso" => $progreso,
                        "intentos" => $intentos,
                        "letrasAdivinadas" => $letrasAdivinadas,
                    ], 200);
                }
            } else {
                return response()->json([
                    "mensaje" => "El juego ha comenzado coloca /jugar para jugar",
                ], 200);
            }

        } else {
            return response()->json([
                "mensaje" => "Índice de palabra no encontrado",
            ], 404);
        }
    }
}
