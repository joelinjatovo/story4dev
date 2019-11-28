<?php

namespace App\Controller\Web\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

use App\Entity\User;
use App\Entity\Project;
use App\Entity\Activity;
use App\Entity\Report;
use App\Entity\File;

/** 
 * @Route("/admin", name="admin_")
 *
 * @IsGranted("ROLE_ADMIN")
 */
class IndexController extends AbstractController
{
    /**
    * @Route("/", name="index")
    */
    public function index(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        
        $entityManager = $this->getDoctrine()->getManager();
        
        $users = $entityManager->getRepository(User::class)->getRecent(5);
        
        $query = $entityManager->createQuery("SELECT DATE(u.createdAt) as date, COUNT(u.id) as value FROM App:User AS u GROUP BY date");
        //$query = $entityManager->createQuery("SELECT DATE(s.updatedAt) as date, COUNT(s.sess_id) as value FROM App:Session AS s GROUP BY date");
        $data = $query->getResult();
        
        $count = [];
        $count['pinged']     = $entityManager->getRepository(User::class)->createQueryBuilder('u')->select('count(u.id)')->where('u.status = :status')->setParameter('status', User::STATUS_PING)->getQuery()->getSingleScalarResult();
        $count['users']      = $entityManager->getRepository(User::class)->createQueryBuilder('u')->select('count(u.id)')->getQuery()->getSingleScalarResult();
        $count['projects']   = $entityManager->getRepository(Project::class)->createQueryBuilder('p')->select('count(p.id)')->getQuery()->getSingleScalarResult();
        $count['activities'] = $entityManager->getRepository(Activity::class)->createQueryBuilder('a')->select('count(a.id)')->getQuery()->getSingleScalarResult();
        $count['reports']    = $entityManager->getRepository(Report::class)->createQueryBuilder('r')->select('count(r.id)')->getQuery()->getSingleScalarResult();
        $count['files']      = $entityManager->getRepository(File::class)->createQueryBuilder('f')->select('count(f.id)')->getQuery()->getSingleScalarResult();

        return $this->render('admin/index.html.twig', [
            'users' => $users,
            'count' => $count,
            'data' => json_encode($data),
        ]);
    }
}