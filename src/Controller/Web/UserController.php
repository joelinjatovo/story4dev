<?php

namespace App\Controller\Web;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;

use App\Entity\User;
use App\Entity\Report;
use App\Service\PaginatorService;

/**
 * @Route(name="user_")
 *
 * @IsGranted("ROLE_USER") 
 */
class UserController extends AbstractController
{
    /**
     * @Route("/{slug}/{page<\d+>?1}", name="show", methods="GET")
     * @Entity("user", options={"mapping": {"slug": "slug"}})
     */
    public function show(User $user, $page = 1, PaginatorService $paginator)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $query = $entityManager->getRepository(Report::class)->findByUser($user);
        
        $reports = $paginator->paginate($query, 10);

        return $this->render('user/show.html.twig', [
            'user'    => $user,
            'reports' => $reports
        ]);
    }
}
