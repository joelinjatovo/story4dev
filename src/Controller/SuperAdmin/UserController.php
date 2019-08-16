<?php

namespace App\Controller\SuperAdmin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

use App\Entity\User;
use App\Service\PaginatorService;

/**
 * @Route("/sadmin", name="sadmin_user_")
 *
 * @IsGranted("ROLE_SUPER_ADMIN")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/user", name="index", methods="GET")
     */
    public function index()
    {
        return $this->render('sadmin/user/create.html.twig');
    }
    
    /**
     * @Route("/user", name="create", methods="POST")
     */
    public function create(ValidatorInterface $validator): Response
    {
        // you can fetch the EntityManager via $this->getDoctrine()
        // or you can add an argument to the action: createProduct(EntityManagerInterface $entityManager)
        $entityManager = $this->getDoctrine()->getManager();

        $user = new User();
        
        $errors = $validator->validate($user);
        if (count($errors) > 0) {
            return new Response((string) $errors, 400);
        }

        // tell Doctrine you want to (eventually) save the Product (no queries yet)
        $entityManager->persist($user);

        // actually executes the queries (i.e. the INSERT query)
        $entityManager->flush();

        return new Response('Saved new user with id '.$project->getId());
    }
    
    /**
     * @Route("/user/{id}", name="show", methods="GET", requirements={"id"="\d+"})
     */
    public function show(User $user)
    {
        return $this->render('sadmin/user/show.html.twig', ['user' => $user]);
    }
    
    /**
     * @Route("/user/edit/{id}", name="edit", methods="GET", requirements={"id"="\d+"})
     */
    public function edit(User $user)
    {
        return $this->render('sadmin/user/edit.html.twig', ['user' => $user]);
    }
    
    /**
     * @Route("/user/edit/{id}", name="update", methods="POST", requirements={"id"="\d+"})
     */
    public function update(User $user)
    {
        $entityManager = $this->getDoctrine()->getManager();
        
        //$user->setTitle('New user name!');
        
        $entityManager->flush();

        return $this->redirectToRoute('sadmin_user_show', ['id' => $user->getId()]);
    }
    
    /**
     * @Route("/users/{page<\d+>?1}", name="list", methods="GET")
     */
    public function list(PaginatorService $paginator, $page = 1)
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');
        
        $entityManager = $this->getDoctrine()->getManager();
        
        $query = $entityManager->getRepository(User::class)->getAll();

        $users = $paginator->paginate($query, 2);
        
        return $this->render('sadmin/user/list.html.twig', ['users' => $users]);
    }
}
