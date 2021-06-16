<?php

declare(strict_types=1);

namespace App\Controller;
use App\Entity\Employee;
use App\Repository\EmployeeRepository;
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
        
    ];
    
    
    /**
     * @Route("/default", name="default_index")
     * 
     * La clase ruta debe estar precedida en los comentario por una arroba.
     * El primer parámetro de Route es la URL a la que queremos asociar la acción.
     * El segundo parámetro de Route es el nombre que queremos dar a la ruta.
     */
    public function index(Request $request, EmployeeRepository $employeeRepository): Response
    {
        if($request->query->has('term')){
            $people = $employeeRepository->findByTerm($request->query->get('term'));

            return $this->render('default/index.html.twig', [
                'people' => $people
             ]);
        }
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
        
        $order = [];

        if($request->query->has('orderBy')) {
            $order[$request->query->get('orderBy')] = $request->query->get('orderDir', 'ASC');
        }

        dump($order);

        // Metodo 2: creando un parámetro indicando el tipo (type hint).
        $people = $employeeRepository->findBy([], $order); // Employee::class = App\Entity\Employee



        // $people = $this->getDoctrine()->getRepository(Employee::class)->findAll(); // Employee::class = App\Entity\Employee

        return $this->render('default/index.html.twig', [
           'people' => $people
        ]);

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
    public function indexJson(EmployeeRepository $employeeRepository): JsonResponse {
        $people= $employeeRepository->findAll();
        
        return $this->json($people);
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
     *  "id": "\d+"
     * })
     */
    
      // La técinca ParamConverte inyecta directamente,
    // un objeto del tipo indicado como parámetro
    // intentando hacer un match del parámetro de la ruta
    // con alguna de las propiedades del objeto requerido.
    
    public function show(Employee $employee): Response {
        return $this->render('default/show.html.twig', [
            'person' => $employee
        ]);
    }

    public function userJson(int $id, EmployeeRepository $employeeRepository): JsonResponse {
        $data = $employeeRepository->find($id);
       
        return $this->json($data);
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


    
