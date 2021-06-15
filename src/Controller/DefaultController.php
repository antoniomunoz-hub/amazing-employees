<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

// AbstractController es un controlador de Symfony
// que pone a disposición nuestra multitud de características.
class DefaultController extends AbstractController
{
    const PEOPLE = [
        ['name' => 'Carlos', 'email' => 'carlos@correo.com', 'age' => 30, 'city' => 'Benalmádena'],
        ['name' => 'Carmen', 'email' => 'carmen@correo.com', 'age' => 25, 'city' => 'Fuengirola'],
        ['name' => 'Carmelo', 'email' => 'carmelo@correo.com', 'age' => 35, 'city' => 'Torremolinos'],
        ['name' => 'Carolina', 'email' => 'carolina@correo.com', 'age' => 38, 'city' => 'Málaga'],        
    ];
    
    
    /**
     * @Route("/default", name="default_index")
     * 
     * La clase ruta debe estar precedida en los comentario por una arroba.
     * El primer parámetro de Route es la URL a la que queremos asociar la acción.
     * El segundo parámetro de Route es el nombre que queremos dar a la ruta.
     */
    public function index(Request $solicitud): Response
    {
        // echo '<pre>query: '; var_dump($solicitud->query); echo '</pre>';
        // echo '<pre>request: '; var_dump($solicitud->request); echo '</pre>';
        // echo '<pre>server: '; var_dump($solicitud->server); echo '</pre>';
        // echo '<pre>file: '; var_dump($solicitud->files); echo '</pre>';
        // die();


        // Una acción siempre debe devolver una respesta.
        // Por defecto deberá ser un objeto de la clase,
        // Symfony\Component\HttpFoundation\Response

        // render() es un método hereado de AbstractController
        // que devuelve el contenido declarado en una plantillas de Twig.
        // https://twig.symfony.com/doc/3.x/templates.html

        //symfony

        $name ='Antoñin';
        return $this->render('default/index.html.twig',[
        'people' => self::PEOPLE]);
    }

    /**
     * @Route("/hola", name="default_hola")
     */
    public function hola(): Response {
        return new Response('<html><body>hola</body></html>');
    }


     /**
     * @Route(
     *      "/default.{_format}",
     *      name="default_index_json",
     *      requirements = {
     *          "_format": "json"
     *      }
     * )
     */
    public function indexJson(): JsonResponse {
        return $this->json(self::PEOPLE);
    }
    /**
     * @Route("/adios", name="default_adios")
     */
    public function adios(): Response {
        return new Response('<html><body>coge la soga y aprietala</body></html>');
    }

    /**
     * @Route("/default/{id}", 
     * name="default_show",
     * requirements ={ 
     *  "id": "[0-3]"})
     */
    
     public function show(int $id): Response{
        return $this->render('default/show.html.twig', [
            'id'=> $id,
            'person' => self::PEOPLE[$id]
        ]);

    }
    
      /**
     * @Route("/redirect-to-home", 
     * name="default_redirect_to_home")
     */
    public function redirectToHome(): Response {
        return new RedirectResponse('/');
        // return $this->redirect('/');

        //Redirigir a la url
        // return $this->redirect('/');

        //Reirigir una tura utilizando su nombre
        //Return $this->redirectRoute('default_show', ['id'=>1]);    
    }

        /**
     * @Route(
     *      "/default.{_format}",
     *      name="default_index_json",
     *      requirements = {
     *          "_format": "json"
     *      }
     * )
     * 
     * El comando:
     * symfony console router:match /default.json
     * buscará la acción coincidente con la ruta indicada
     * y mostrará la información asociada.
     */
    public function indexJsonRequest(Request $request): JsonResponse {
        $data = $request->query->has('id') ? self::PEOPLE[$request->query->get('id')] : self::PEOPLE;

        return $this->json($data);
    }
}


    
