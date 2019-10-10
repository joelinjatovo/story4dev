<?php

namespace App\Controller\Web\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;

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
    public function create(Request $request, FormError $formError, UserPasswordEncoderInterface $passwordEncoder)
    {
        $password = base64_encode (random_bytes( 10 ) );
        
        $user = new User();
        $user->setPassword($passwordEncoder->encodePassword($user, $password));
        
        $form = $this->createForm(UserType::class, $user);
        
        $form->handleRequest($request);
        
        if ( $form->isSubmitted() ) {
            if( $form->isValid() ) {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($user);
                $entityManager->flush();
        
                $this->addFlash('success', 'Account profile successfully updated. Password:' . $password);

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
     * @Entity("user", options={"mapping": {"id": "id"}})
     */
    public function show(User $user)
    {
        return $this->render('admin/user/show.html.twig', ['user' => $user]);
    }
    
    /**
     * @Route("/user/edit/{id}", name="edit", methods="GET", requirements={"id"="\d+"})
     * @Entity("user", options={"mapping": {"id": "id"}})
     */
    public function edit(User $user)
    {
        $form = $this->createForm(UserType::class, $user);
        
        return $this->render('admin/user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }
    
    /**
     * @Route("/user/edit/{id}", name="update", methods="POST", requirements={"id"="\d+"})
     */
    public function update(User $user, Request $request, FormError $formError)
    {
        $form = $this->createForm(UserType::class, $user);
        
        $form->handleRequest($request);
        
        if ( $form->isSubmitted() ) {
            if( $form->isValid() ) {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($user);
                $entityManager->flush();
        
                $this->addFlash('success', 'User Information successfully updated.');

                return $this->redirectToRoute('admin_user_edit', [
                    'id' => $user->getId()
                ]);
                
            }else{
                $this->addFlash('error', 'Invalid request. Try again!' . $form->getErrors() );
            }
        }
        
        return $this->render('admin/user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }
    
    /**
     * @Route("/users/{page<\d+>?1}", name="list", methods="GET")
     */
    public function list(PaginatorService $paginator, $page = 1, Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');
        
        $entityManager = $this->getDoctrine()->getManager();
        
        $search = $request->query->get('s');
        if( strlen($search) > 20 ) {
            $search = substr($search, 0, 20);
        }
        
        $query = $entityManager->getRepository(User::class)->getAll($search);

        $users = $paginator->paginate($query, 10);
        
        $params = ['users' => $users, 'search' => $search];
        
        return $this->render('admin/user/list.html.twig', $params);
    }
}
