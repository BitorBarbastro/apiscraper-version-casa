<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserNewspaperController extends Controller
{

    public function getSubscribedNewspapers()
    {
        // Obtener el usuario autenticado
        $user = auth()->user();

        // Obtener los periódicos a los que el usuario está suscrito
        $newspapers = $user->newspapers()->with('newspaper')->get()->pluck('newspaper');

        // Preparar la respuesta
        $data = [];
        foreach ($newspapers as $newspaper) {
            $data[] = [
                'id' => $newspaper->id,
                'name' => $newspaper->name,
                'url' => $newspaper->url,
            ];
        }
        // Devolver los periódicos suscritos en una respuesta JSON
        return response()->json(['newspapers' => $data], 200);
    }
}
