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
    protected $em;

    public function __construct( \Doctrine\ORM\EntityManager $em) {
        $this->em = $em;
    }
    
    public function getFilters()
    {
        return [
            new TwigFilter('html', [$this, 'formatHtml']),
            new TwigFilter('excerpt', [$this, 'formatExcerpt']),
            new TwigFilter('result', [$this, 'getResult']),
            new TwigFilter('goal', [$this, 'getGoal']),
            new TwigFilter('progression', [$this, 'getProgression']),
            new TwigFilter('progressionClass', [$this, 'getProgressionClass']),
        ];
    }

    public function getResult($entity, ?Iteration $iteration = null){
        if($entity instanceof Indicator){
            return $this->em->getRepository(Result::class)->getValue($entity, $iteration);
        }
        
        if($entity instanceof Activity){
            return $this->em->getRepository(Result::class)->getValue($entity, $iteration);
        }
        
        if($entity instanceof Project){
            return $this->em->getRepository(Result::class)->getValue($entity, $iteration);
        }
        
        return 0;
    }

    public function getGoal($entity, ?Iteration $iteration = null){
        if($entity instanceof Indicator){
            return $this->em->getRepository(Goal::class)->getValue($entity, $iteration);
        }
        
        if($entity instanceof Activity){
            return $this->em->getRepository(Goal::class)->getValue($entity, $iteration);
        }
        
        if($entity instanceof Project){
            return $this->em->getRepository(Goal::class)->getValue($entity, $iteration);
        }
        
        return 0;
    }

    public function getProgression($entity, ?Iteration $iteration = null){
        if($entity instanceof Indicator){
            $value = $this->getResult($entity, $iteration);
            $goal  = $this->getGoal($entity, $iteration);
            if($goal != 0){
                return (int) ( $value / $goal * 100 ) ;
            }else{
                return 100;
            }
        }
        
        // Calculer la moyenne de la progression des indicateurs
        if($entity instanceof Activity){
            $progression = 0;
            $indicators = $this->em->getRepository(Indicator::class)->findBy(['activity' => $entity]);
            foreach($indicators as $indicator){
                $progression += $this->getProgression($indicator, $iteration);
            }

            if( count($indicators) > 0 ) {
                return (int) ( $progression / count($indicators) );
            }
        }
        
        // Calculer la moyenne de la progression des activités
        if($entity instanceof Project){
            $progression = 0;
            $activities = $this->em->getRepository(Activity::class)->findBy(['project' => $entity]);
            foreach($activities as $activity){
                $progression += $this->getProgression($activity,  $iteration);
            }

            if( count($activities) > 0 ) {
                return (int) ( $progression / count($activities) );
            }
        }
        
        return 0;
    }

    public function getProgressionClass($entity, ?Iteration $iteration = null){
        $progression = $this->getProgression($entity, $iteration);
        if($progression<=30){ return 'danger'; }
        if($progression<=50){ return 'warning'; }
        if($progression<=80){ return 'brand'; }
        return 'success';
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
}