<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Doctrine\Common\Collections\ArrayCollection;

use App\Entity\User;
use App\Entity\Project;
use App\Entity\Activity;
use App\Entity\Iteration;
use App\Form\ActivityType;
use App\Form\ProjectType;
use App\Service\PaginatorService;

/** @Route(name="user_") */
class UserController extends AbstractController
{
    /**
     * @Route("/{slug}", name="show", methods="GET")
     * @Entity("user", options={"mapping": {"slug": "slug"}})
     */
    public function show(User $user)
    {
        return $this->render('user/show.html.twig');
    }
}
