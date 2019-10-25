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
use App\Entity\Report;
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
     * @Route("/user/{id}/{page<\d+>?1}", name="show", methods="GET", requirements={"id"="\d+"})
     * @Entity("user", options={"mapping": {"id": "id"}})
     */
    public function show(User $user, $page = 1, PaginatorService $paginator)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $query = $entityManager->getRepository(Report::class)->findByUser($user);
        
        $reports = $paginator->paginate($query, 10);

        return $this->render('admin/user/show.html.twig', [
                'user' => $user,
            'reports' => $reports
            ]);
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
     * @Route("/users/role/{role}/{page<\d+>?1}", name="list_role", methods="GET")
     * @Route("/users/status/{status}/{page<\d+>?1}", name="list_status", methods="GET")
     */
    public function list(PaginatorService $paginator, $role = null, $status = null, $page = 1, Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        
        $entityManager = $this->getDoctrine()->getManager();
        
        $search = $request->query->get('s');
        if( strlen($search) > 20 ) {
            $search = substr($search, 0, 20);
        }
        
        $roleParam = null;
        switch($role){
            case "admin":
                $roleParam = "ROLE_ADMIN";
            break;
            case "user":
                $roleParam = "ROLE_USER";
            break;
            default:
                $role = null;
            break;
        }
        
        $statusParam = null;
        switch($status){
            case User::STATUS_ACTIVE:
            case User::STATUS_PING:
            case User::STATUS_BLOCKED:
            case User::STATUS_CANCELED:
                $statusParam = $status;
            break;
            default:
                $status = null;
            break;
        }
        
        $query = $entityManager->getRepository(User::class)->getAll($search, $roleParam, $statusParam);

        $users = $paginator->paginate($query, 10);
        
        $params = ['users' => $users, 'search' => $search, 'role' => $role, 'status' => $status];
        
        return $this->render('admin/user/list.html.twig', $params);
    }
    
    /**
     * @Route("/user/trash", name="trash", methods="POST")
     * 
     * @IsGranted("ROLE_SUPER_ADMIN") 
     * 
     */
    public function trash(Request $request)
    {
        if ( $request->isXmlHttpRequest() ) {
            $id = (int) $request->request->get('id');
            
            if( $id > 0 ) {
                $entityManager = $this->getDoctrine()->getManager();
        	
                $entityManager->getFilters()->disable("deleted");
                
                $user = $entityManager->getRepository(User::class)->find($id);
                if( $user && ! $user->isSuperAdmin() ){
                    // give all project to the current superadmin
                    foreach($user->getProjects() as $project){
                        $project->setAuthor($this->getUser());
                        $entityManager->persist($project);
                    }
                    $entityManager->remove($user);
                    $entityManager->flush();
                    
                    return $this->json([
                        'success' => true,
                        'message' => 'L\'utilisateur a été supprimé avec succès.',
                    ]);
                }
            }
            
            return $this->json([
                'success' => false,
                'title'   => 'Invalid Request',
                'message' => 'An error was occured. :)',
            ]);
        }
    }
    
    /**
     * @Route("/user/remove", name="remove", methods="POST")
     * 
     * @IsGranted("ROLE_SUPER_ADMIN") 
     * 
     */
    public function remove(Request $request)
    {
        if ( $request->isXmlHttpRequest() ) {
            $id = (int) $request->request->get('id');
            
            if( $id > 0 ) {
                $entityManager = $this->getDoctrine()->getManager();
        	
                $entityManager->getFilters()->disable("deleted");
                
                $user = $entityManager->getRepository(User::class)->find($id);
                if( $user && ! $user->isSuperAdmin() && $user->isDeleted() ){
                    $entityManager->remove($user);
                    $entityManager->flush();
                    
                    return $this->json([
                        'success' => true,
                        'message' => 'L\'utilisateur a été supprimé avec succès.',
                    ]);
                }
            }
            
            return $this->json([
                'success' => false,
                'title'   => 'Invalid Request',
                'message' => 'An error was occured. :)',
            ]);
        }
    }
    
    /**
     * @Route("/user/restore", name="restore", methods="POST")
     * 
     * @IsGranted("ROLE_SUPER_ADMIN") 
     * 
     */
    public function restore(Request $request)
    {
        if ( $request->isXmlHttpRequest() ) {
            $id = (int) $request->request->get('id');
            
            if( $id > 0 ) {
                $entityManager = $this->getDoctrine()->getManager();
        	
                $entityManager->getFilters()->disable("deleted");

                $user = $entityManager->getRepository(User::class)->find($id);
                if( $user && $user->isDeleted()){
                    $user->setDeletedAt(null);
                    $entityManager->persist($user);
                    $entityManager->flush();
                    
                    return $this->json([
                        'success' => true,
                        'message' => 'L\'utilisateur a été restauré avec succès',
                    ]);
                }
            }
            
            return $this->json([
                'success' => false,
                'title'   => 'Invalid Request',
                'message' => 'An error was occured. :)',
            ]);
        }
    }
}
