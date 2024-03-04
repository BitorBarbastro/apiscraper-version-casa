<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Newspaper;
use Goutte\Client;
use Illuminate\Http\Request;
use App\Models\User;

class NewspaperController extends Controller
{
    public function store(Request $request)
    {
        // Validar los datos de entrada
        $validatedData = $request->validate([
            'url' => 'required|url',
        ]);

        // Crear una nueva instancia de Goutte
        $client = new Client();

        // Hacer una solicitud GET a la URL proporcionada
        $crawler = $client->request('GET', $validatedData['url']);

        // Buscar el título del periódico en la página
        // Este selector puede variar dependiendo de la estructura de la página web
        $title = $crawler->filter('title')->first()->text();

        // Añadir el título extraído a los datos validados
        $validatedData['name'] = $title;

        // Crear un nuevo periódico
        $newspaper = Newspaper::create($validatedData);

        // Devolver una respuesta
        return response()->json($newspaper, 201);
    }
    public function subscribe(Request $request, $userId, $newspaperId)
    {
        $user = User::findOrFail($userId);
        $newspaper = Newspaper::findOrFail($newspaperId);

        // Añadir la suscripción
        $user->newspapers()->attach($newspaperId);

        return response()->json(['message' => 'Usuario suscrito al periódico'], 200);
    }

    public function unsubscribe(Request $request, $userId, $newspaperId)
    {
        $user = User::findOrFail($userId);
        $newspaper = Newspaper::findOrFail($newspaperId);

        // Eliminar la suscripción
        $user->newspapers()->detach($newspaperId);

        return response()->json(['message' => 'Usuario desuscrito del periódico'], 200);
    }

    public function getHeadlines($newspaperId)
    {
        $data = [];
        $newspaper = Newspaper::findOrFail($newspaperId);

        $client = new Client();
        $crawler = $client->request('GET', $newspaper->url);
        $crawler->filter('article')->each(function ($node) use (&$data) {
            try {
                $link = $node->filter('a')->attr('href');
                if ($node->filter('h1')->count() > 0) {
                    $title = $node->filter('h1')->text();
                } else if ($node->filter('h2')->count() > 0) {
                    $title = $node->filter('h2')->text();
                } else {
                    $title = $node->filter('h3')->text();
                }
                $data[] = [$title => $link];
            } catch (\Exception $error) {
            }
        });
        // Recoger los titulos de los enlaces recuperados.-
        return response()->json([
            'data' => $data
        ]);
    }
    public function getAllHeadlines()
    {
        // Obtener todos los periódicos de la base de datos
        $newspapers = Newspaper::all();

        // Inicializar un array para almacenar los titulares de todos los periódicos
        $allHeadlines = [];

        foreach ($newspapers as $newspaper) {
            // Llamar al método getHeadlines para cada periódico
            $headlines = $this->getHeadlines($newspaper->id);

            // Añadir los titulares del periódico actual al array de todos los titulares
            $allHeadlines[$newspaper->name] = $headlines;
        }

        // Devolver los titulares de todos los periódicos en una respuesta JSON
        return response()->json(['headlines' => $allHeadlines], 200);
    }
}
