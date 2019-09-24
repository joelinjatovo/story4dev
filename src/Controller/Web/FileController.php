<?php

namespace App\Controller\Web;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

use App\Entity\File;
use App\Entity\User;

/** 
 * @Route(name="file_") 
 *
 * @IsGranted("ROLE_USER") 
 */
class FileController extends AbstractController
{
    
    /**
     * @Route("/{slug}/files/{page<\d+>?1}", name="list", methods="GET")
     * @Entity("user", options={"mapping": {"slug": "slug"}})
     */
    public function list(User $user, $page = 1)
    {
        if($this->getUser() != $user ){
            throw $this->createNotFoundException('The author does not match');
        }
        
        $entityManager = $this->getDoctrine()->getManager();
        
        $files = $entityManager->getRepository(File::class)->findAll();
        
        return $this->render('file/list.html.twig', [
            'files' => $files
        ]);
    }
}
