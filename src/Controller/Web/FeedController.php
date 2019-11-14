<?php

namespace App\Controller\Web;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Component\HttpFoundation\JsonResponse;

use App\Entity\Project;
use App\Entity\Report;
use App\Service\PaginatorService;

/** 
 * @Route(name="feed_")
 *
 */
class FeedController extends AbstractController
{
    /**
     * @Route("/feed/json/{slug}/{page<\d+>?1}", name="json", methods="GET")
     * @Entity("project", options={"mapping": {"slug": "slug"}})
     */
    public function jsonFeed($page = 1, Project $project, PaginatorService $paginator)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $query = $entityManager->getRepository(Report::class)->feedByProject($project);
        $reports = $paginator->paginate($query);
        return $this->json([
            'pagination' => [
                'page'  => (int) $page,
                'found' => (int) $reports->count(),
            ], 
            'format'  => 'json', 
            'project' => $project,
            'data'    => $reports
        ], JsonResponse::HTTP_OK, [], ['groups' => ['raw', 'report_feed']]);
    }
}