<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

use App\Entity\Iteration;
use App\Entity\Periodicity;
use App\Entity\Unit;
use App\Entity\Project;
use App\Entity\ProjectContribution;
use App\Entity\Activity;
use App\Entity\Indicator;
use App\Entity\Goal;
use App\Entity\Report;
use App\Entity\Result;
use App\Entity\User;
use App\Entity\File;

class AppExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('html', [$this, 'formatHtml']),
            new TwigFilter('excerpt', [$this, 'formatExcerpt']),
            new TwigFilter('link', [$this, 'formatLink'], ['needs_environment' => true]),
        ];
    }

    public function formatExcerpt($text, $length = 200, $more = '...'){
        $text = strip_tags($text);
        if( strlen($text) > $length ){
            return mb_substr($text, 0, $length).$more;
        }
        
        return $text;
    }
    
    public function formatHtml($html)
    {
        $html = trim($html);
        
        if($html){
            // Unsafe HTML tags that members may abuse
            $unsafe=array(
                '/<iframe(.*?)<\/iframe>/is',
                '/<title(.*?)<\/title>/is',
                '/<pre(.*?)<\/pre>/is',
                '/<frame(.*?)<\/frame>/is',
                '/<frameset(.*?)<\/frameset>/is',
                '/<object(.*?)<\/object>/is',
                '/<script(.*?)<\/script>/is',
                '/<embed(.*?)<\/embed>/is',
                '/<applet(.*?)<\/applet>/is',
                '/<meta(.*?)>/is',
                '/<!doctype(.*?)>/is',
                '/<link(.*?)>/is',
                '/<body(.*?)>/is',
                '/<\/body>/is',
                '/<head(.*?)>/is',
                '/<\/head>/is',
                '/onclick="(.*?)"/is',
                '/onload="(.*?)"/is',
                '/onunload="(.*?)"/is',
                '/<html(.*?)>/is',
                '/<\/html>/is'
            );

            // Remove these tags and all parameters within them
            $html = preg_replace($unsafe, "", $html);
        }
        
        return $html;
    }
    
    public function formatLink(\Twig_Environment $env, $type, $subject, $admin = false): string
    {
        $router = $env->getExtension('routing');

        if( $admin ) {
        }else{
            if( $subject instanceof Project ){
                switch ($type) {
                    case 'index':
                    case 'create':
                        return $router->getPath('project_index');
                    break;
                    case 'edit':
                    case 'update':
                        return $router->getPath('project_edit', [
                                'id' => $subject->getId(),
                            ]);
                    break;
                    case 'list':
                        return $router->getPath('project_list');
                    break;
                }
            }
        }

        return $router->getPath('app_index');
    }
}