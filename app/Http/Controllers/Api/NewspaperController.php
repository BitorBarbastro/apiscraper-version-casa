<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Newspaper;
use Goutte\Client;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class NewspaperController extends Controller
{
    public function store(Request $request)
    {
        // Validar los datos de entrada
        $validatedData = $request->validate([
            'url' => 'required|url',
            'urlrss' => 'required|url',
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
        $validatedData['urlrss'];

        // Crear un nuevo periódico
/*         dd($validatedData);
 */
        $newspaper = Newspaper::create($validatedData);

        // Devolver una respuesta
        return response()->json($newspaper, 201);
    }
    public function destroy($id)
    {
        $newspaper = Newspaper::find($id);

        if (!$newspaper) {
            return response()->json(['message' => 'Periódico no encontrado'], 404);
        }

        $newspaper->delete();

        return response()->json(['message' => 'Periódico eliminado con éxito'], 200);
    }

    public function update(Request $request, $id)
    {
        // Validar los datos de entrada
        $validatedData = $request->validate([
            'name' => 'sometimes|required|string',
            'url' => 'sometimes|required|url',
        ]);

        // Buscar el periódico por su ID
        $newspaper = Newspaper::find($id);

        // Verificar si el periódico existe
        if (!$newspaper) {
            return response()->json(['message' => 'Periódico no encontrado'], 404);
        }

        // Actualizar el periódico con los nuevos datos
        $newspaper->update($validatedData);

        // Devolver una respuesta
        return response()->json($newspaper, 200);
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
        $userId = Auth::id();
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
    public function checkHeadlines($newspaperId)
    {
        $data = [];
        $datarss = [];
        $commonData = [];//En esta array se almacenan los titulares comunes
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

        $client = new Client();
        $crawler = $client->request('GET', $newspaper->urlrss);
        $crawler->filter('item')->each(function ($node) use (&$datarss) {
            try {

                $title = $node->filter('title')->text();
                $link = $node->filter('guid')->text();

                $datarss[] = [$title => $link];
            } catch (\Exception $error) {
            }
        });
        //la "funcion" siguiente  se podria haber implementado en el try de arriba pero no me he dado cuenta antes

        for ($i = 0; $i < count($data); $i++) { //Una vuelta por cada titular en el scrap original
            if (isset($datarss[$i])) { //Si el rss tiene la posicion la comprueba
                for ($j = 0; $j < count($datarss); $j++) { //mira por toda la lista del rss si el titular original existe
                    if ($data[$i] == $datarss[$j]) {
                        $commonData[] = [$data[$i]];//Se registra en un json para mostrarlo despues
                    }
                }
            }
        }
        $precision = count($commonData) / count($data) * 100;
        // dd($precision);
        // mostrar los titulos de los enlaces recuperados.-
        return [
            response()->json([
                'data' => $commonData,
                'precision' => $precision . '%'
            ]),

        ];
    }

    public function showHeadlinesDiff()
    {
        return view('Dashboard');
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
    /*   public function subscribeButton(Request $request, $newspaperId)
      {
          $userId = Auth::id();
          $user = User::findOrFail($userId);
          $newspaper = Newspaper::findOrFail($newspaperId);
    
          // Añadir la suscripción
          $user->newspapers()->attach($newspaperId);
    
          return view();
      } */
    public function getAllHeadlinesButton()
    {

        // Obtiene el contenido de la URL como una cadena
        $jsonContent = file_get_contents('http://localhost:8000/api/newspapers/headlines');
        // Decodifica el JSON para trabajar con él como un array
        $data = json_decode($jsonContent, true);

        // Suponiendo que los titulares están en un array bajo la clave 'data'
        $headlines = $data['data'];

        // Luego, devuelve una vista pasando los titulares como datos
        return view('viewNewspapers', ['viewNewspapers' => $headlines]);
    }


    public function show($id)
    {
        // Buscar el periódico por su ID
        $newspaper = Newspaper::find($id);

        // Verificar si el periódico existe
        if (!$newspaper) {
            return response()->json(['message' => 'Periódico no encontrado'], 404);
        }

        // Devolver una respuesta
        return response()->json($newspaper, 200);
    }

    public function index()
    {
        // Obtener todos los periódicos
        $newspapers = Newspaper::all();

        // Devolver una respuesta
        return response()->json($newspapers, 200);
    }
}
