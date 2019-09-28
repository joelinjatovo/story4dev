<?php

namespace App\Controller\Web\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

use App\Entity\User;
use App\Form\UserType;
use App\Service\PaginatorService;
use App\Service\FormError;

/**
 * @Route("/admin", name="admin_user_")
 *
 * @IsGranted("ROLE_ADMIN")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/user", name="index", methods="GET")
     */
    public function index()
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        
        return $this->render('admin/user/create.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }
    
    /**
     * @Route("/user", name="create", methods="POST")
     */
    public function create(Request $request, FormError $formError)
    {
        $user = new User();
        
        $form = $this->createForm(UserType::class, $user);
        
        $form->handleRequest($request);
        
        if ( $form->isSubmitted() ) {
            if( $form->isValid() ) {
                $user->setPassword("njnj");
                
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($user);
                $entityManager->flush();
        
                $this->addFlash('success', 'Account profile successfully updated.');

                return $this->redirectToRoute('user_show', [
                    'slug' => $user->getSlug()
                ]);
                
            }else{
                $this->addFlash('error', 'Invalid request. Try again!' . $form->getErrors() );
            }
        }
        
        return $this->render('admin/user/create.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }
    
    /**
     * @Route("/user/{id}", name="show", methods="GET", requirements={"id"="\d+"})
     */
    public function show(User $user)
    {
        return $this->render('admin/user/show.html.twig', ['user' => $user]);
    }
    
    /**
     * @Route("/user/edit/{id}", name="edit", methods="GET", requirements={"id"="\d+"})
     */
    public function edit(User $user)
    {
        return $this->render('admin/user/edit.html.twig', ['user' => $user]);
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
        
        return $this->render('admin/user/list.html.twig', ['users' => $users]);
    }
}
