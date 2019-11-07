<?php

namespace App\Controller\Web;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;

use App\Entity\Project;

/** 
 * @Route(name="feed_")
 *
 */
class FeedController extends AbstractController
{
    /**
     * @Route("/feed/{id}", name="index", methods="GET", requirements={"id"="\d+"})
     * @Entity("project", options={"mapping": {"id": "id"}})
     */
    public function index(Project $project)
    {
        return $this->json(['data'=>'OK']);
    }
}