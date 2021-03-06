<?php

namespace App\Controller;
use App\Entity\Employee;
use App\Repository\EmployeeRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request as HttpFoundationRequest;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

/**
 * @Route("/api/amazing-employees", name="api_employees_")
 * @UniqueEntity("email")
 */
 class ApiEmployeesController extends AbstractController
{
    /**
     * @Route(
     *      "",
     *      name="cget",
     *      methods={"GET"}
     * )
     */
    
    public function index(Request $request, EmployeeRepository $employeeRepository): Response
    {
        if($request->query->has('term')) {
            $people = $employeeRepository->findByTerm($request->query->get('term'));

            return $this->json($people);
        }

 

        return $this->json($employeeRepository->findAll());
    }

    /**
     * @Route(
     *      "/{id}",
     *      name="get",
     *      methods={"GET"},
     *      requirements={
     *          "id": "\d+"
     *      }
     * )
     */
    
    public function show(int $id, EmployeeRepository $employeeRepository): Response
    {
        $data = $employeeRepository->find($id);

        dump($id);
        dump($data);

        return $this->json($data);
    }


    /**
     * @Route(
     *      "",
     *      name="post",
     *      methods={"POST"}
     * )
     */
    
    public function add(
        Request $request,
        EntityManagerInterface $entityManager, 
        SluggerInterface $slug,
        ValidatorInterface $validator) : Response {
        $data = $request->request;
        

        $employee = new Employee();

        $employee->setName($data->get('name'));
        $employee->setEmail($data->get('email'));
        $employee->setAge($data->get('age'));
        $employee->setCity($data->get('City'));
        $employee->setPhone($data->get('phone'));

        if($request->files->has('avatar')){
            $avatarFile = $request->files->get('avatar');

            $avatarOriginalFilename = pathinfo($avatarFile->getClientOriginalName(), PATHINFO_FILENAME);
            
            $safeFilename = $slug->slug($avatarOriginalFilename);
            $avatarNewFilename = $safeFilename.'-'.uniqid().'-'.$avatarFile->guessExtension();
            dump($avatarNewFilename);

            try {
                $avatarFile->move(
                    $request->server->get('DOCUMENT_ROOT') . DIRECTORY_SEPARATOR . 'employee/avatar',
                    $avatarNewFilename
                );

            } catch (FileException $e){
                throw new \Exception($e->getMessage());
            }
        }

        $errors = $validator->validate($employee);

        if (count($errors) > 0){
            $dataerrors = [];
            
            foreach($errors as $error){
                $dataErrors[] = $error->getMessage();

            }

            return $this->json([
                'status'=>'error',
                'data'=> [
                    'errors'=> $dataErrors
                ],
                Response::HTTP_BAD_REQUEST

            ]);
        }

        $entityManager->persist($employee);

        // hasta aqui employee no tiene id
        $entityManager->flush();

        dump($employee);

        return  $this->json(
            $employee,
            Response::HTTP_CREATED,
            [
                'Location'=> $this->generateUrl(
                    'api_employees_get',
                    [
                        'id'=> $employee->getId()
                    ]
                )
            ]
        );
    }

    
    
    /**
     * @Route(
     *      "/{id}",
     *      name="put",
     *      methods={"PUT"},
     *      requirements={
     *          "id": "\d+"
     *      }
     * )
     */
    
     public function update(
         Employee $employee,
         EntityManagerInterface $entityManager,
         Request $request
        ): Response
    {
        $data = $request->request;
        
        $employee->setName($data->get('name'));
        $employee->setEmail($data->get('email'));
        $employee->setAge($data->get('age'));
        $employee->setCity($data->get('City'));
        $employee->setPhone($data->get('phone'));
        
        $entityManager->flush();

        return $this->json(
            null, Response::HTTP_NO_CONTENT
        );
    }

    /**
     * @Route(
     *      "/{id}",
     *      name="delete",
     *      methods={"DELETE"},
     *      requirements={
     *          "id": "\d+"
     *      }
     * )
     */
    
     public function remove(
         Employee $employee,
         EntityManagerInterface $entityManager
         ): Response
    {
        //remove() prepara el sistema pero NO ejecuta la sentencia

        $entityManager->remove($employee);
        $entityManager->flush();
        return $this->json(
            null, Response::HTTP_NO_CONTENT
        );
    }
}


