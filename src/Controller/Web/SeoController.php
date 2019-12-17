<?php

namespace App\Controller\Web;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

use App\Entity\Project;
use App\Form\FeedbackType;
use App\Helper\MessageHelper;
use App\Service\OptionService;

/** 
 * @Route(name="seo_")
 *
 */
class SeoController extends AbstractController
{

    /**
    * @Route("/sitemap.xml", name="index")
    */
    public function index(Request $request)
    {
        // We define an array of urls
        $urls = [];
        
        // We store the hostname of our website
        $hostname = $request->getHost();

        $urls[] = ['loc' => $this->get('router')->generate('app_index'), 'changefreq' => 'weekly', 'priority' => '1.0'];    
        $urls[] = ['loc' => $this->get('router')->generate('app_login'), 'changefreq' => 'weekly', 'priority' => '1.0'];
        $urls[] = ['loc' => $this->get('router')->generate('app_register'), 'changefreq' => 'weekly', 'priority' => '1.0'];
        $urls[] = ['loc' => $this->get('router')->generate('app_forgot_password'), 'changefreq' => 'weekly', 'priority' => '1.0'];

        // Then, we will find all our articles stored in the database
        $projects = $this->getDoctrine()->getRepository(Project::class)->findAll();

        // We loop on them
        foreach ($projects as $project) {
            $urls[] = ['loc' => $this->get('router')->generate('project_show', ['slug' => $project->getSlug()]), 'changefreq' => 'weekly', 'priority' => '1.0'];
        }

        // Once our array is filled, we define the controller response
        $response = new Response();
        $response->headers->set('Content-Type', 'xml');

        return $this->render('seo/sitemap.xml.twig', [
            'urls'     => $urls,
            'hostname' => $hostname
        ]);
    }
    
}