<?php

namespace App\Controller\Web\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

use App\Entity\User;
use App\Entity\Project;
use App\Entity\Activity;
use App\Entity\Report;
use App\Service\PaginatorService;

/** 
 * @Route(name="admin_trash_")
 *
 * @IsGranted("ROLE_SUPER_ADMIN") 
 */
class TrashController extends AbstractController
{
    
    /**
     * @Route("/admin/trash/{type}/{page<\d+>?1}", name="list", methods="GET")
     */
    public function list($type, PaginatorService $paginator, int $page, Request $request)
    {
        $search = $request->query->get('s');
        if( strlen($search) > 20 ) {
            $search = substr($search, 0, 20);
        }

        $entityManager = $this->getDoctrine()->getManager();
        	
        $entityManager->getFilters()->disable("deleted");

        switch($type){
            case 'users':
                $query = $entityManager->getRepository(User::class)->getAllDeleted();
                $users = $paginator->paginate($query);
                return $this->render('admin/trash/users.html.twig', [
                    'users'  => $users, 
                    'search' => $search, 
                ]);
            break;
            case 'projects':
                $query = $entityManager->getRepository(Project::class)->getAllDeleted();
                $projects = $paginator->paginate($query);
                return $this->render('admin/trash/projects.html.twig', [
                    'projects' => $projects, 
                    'search'   => $search, 
                ]);
            break;
            case 'activities':
                $query = $entityManager->getRepository(Activity::class)->getAllDeleted();
                $activities = $paginator->paginate($query);
                return $this->render('admin/trash/activities.html.twig', [
                    'activities' => $activities, 
                    'search'     => $search, 
                ]);
            break;
            case 'reports':
                $query = $entityManager->getRepository(Report::class)->getAllDeleted();
                $reports = $paginator->paginate($query);
                return $this->render('admin/trash/reports.html.twig', [
                    'reports' => $reports, 
                    'search'   => $search, 
                ]);
            break;
            default:
                throw $this->createNotFoundException('Not found');
            break;
        }
    }
}
