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
            new TwigFilter('json', [$this, 'getJson']),
            new TwigFilter('result', [$this, 'getResult']),
            new TwigFilter('goal', [$this, 'getGoal']),
            new TwigFilter('progression', [$this, 'getProgression']),
            new TwigFilter('progressionClass', [$this, 'getProgressionClass']),
        ];
    }

    public function getJson($data){
        return json_encode($data);
    }

    public function getChartSeries($entity){
        if( is_array( $entity ) ){
            $indicators = $entity;
            foreach($indicators as $indicator){
                $title = $indicator->getTitle();
                $series[] = [
                    '_id'   => $indicator->getId(),
                    'id'    => 'i_'.$indicator->getId(),
                    'title' => $title,
                ];
            }

            return $series;
        }
        
        if($entity instanceof Activity && $entity->getProject()){
            // Get indicators it from repository to avoid EntityNotFoundException
            $indicators = $this->em->getRepository(Indicator::class)->findBy(['activity' => $entity]);
            return $this->getChartSeries($indicators);
        }

        return [];
    }

    public function getChartData($entity, $iterations, $withIndicator = trues){
        $datas = [];

        if( is_array( $entity ) ){
            $indicators = $entity;
            
            foreach($iterations as $iteration){
                $data = [
                    "iteration"   => $iteration->getTitle(),
                    "value"       => $this->getResult($indicators, $iteration),
                    "goal"        => $this->getGoal($indicators, $iteration),
                    "progression" => $this->getProgression($indicators, $iteration),
                ];

                if( $withIndicator ) {
                    foreach($indicators as $indicator){
                        $data['i_'.$indicator->getId()] = $this->getProgression($indicator, $iteration);
                    }
                }

                $datas[] = $data;
            }

            return $datas;
        }

        if($entity instanceof Activity && $entity->getProject()){
            $project = $entity->getProject();
            // Get iterations it from repository to avoid EntityNotFoundException
            $iterations = $this->em->getRepository(Iteration::class)->findBy(['project' => $project]);
            // Get indicators it from repository to avoid EntityNotFoundException
            $indicators = $this->em->getRepository(Indicator::class)->findBy(['activity' => $entity]);
            return $this->getChartData($indicators, $iterations, false);
        }
        
        return $datas;
    }

    /**
    * Usage
    ** Per Indicators
    *** getResult(Array of Indicator)
    *** getResult(Array of Indicator, Iteration)
    ** Per Indicator
    *** getResult(Indicator)
    *** getResult(Indicator, Iteration)
    ** Per Activity
    *** getResult(Activity)
    *** getResult(Activity, Iteration)
    ** Per Project
    *** getResult(Project)
    *** getResult(Project, Iteration)
    */
    public function getResult($entity, ?Iteration $iteration = null){
        $indicators = [];
        switch(true){
            case is_array( $entity ):
                $indicators = $entity;
            break;
            case $entity instanceof Indicator:
                $indicators = [$entity];
            break;
            case $entity instanceof Activity:
                // Get indicators it from repository to avoid EntityNotFoundException
                $indicators = $this->em->getRepository(Indicator::class)->findBy(['activity' => $entity]);
            break;
            case $entity instanceof Project:
                // Get indicators it from repository to avoid EntityNotFoundException
                $indicators = $this->em->getRepository(Indicator::class)->findByProject($entity)->execute();
            break;
        }

        if( is_array($indicators) && !empty($indicators)){
            return $this->em->getRepository(Result::class)->getValue($indicators, $iteration);
        }

        return 0;
    }

    /**
    * Usage
    ** Per Indicators
    *** getGoal(Array of Indicator)
    *** getGoal(Array of Indicator, Iteration)
    ** Per Indicator
    *** getGoal(Indicator)
    *** getGoal(Indicator, Iteration)
    ** Per Activity
    *** getGoal(Activity)
    *** getGoal(Activity, Iteration)
    ** Per Project
    *** getGoal(Project)
    *** getGoal(Project, Iteration)
    */
    public function getGoal($entity, ?Iteration $iteration = null){
        $indicators = [];
        switch(true){
            case is_array( $entity ):
                $indicators = $entity;
            break;
            case $entity instanceof Indicator:
                $indicators = [$entity];
            break;
            case $entity instanceof Activity:
                // Get indicators it from repository to avoid EntityNotFoundException
                $indicators = $this->em->getRepository(Indicator::class)->findBy(['activity' => $entity]);
            break;
            case $entity instanceof Project:
                // Get indicators it from repository to avoid EntityNotFoundException
                $indicators = $this->em->getRepository(Indicator::class)->findByProject($entity)->execute();
            break;
        }

        if( is_array($indicators) && !empty($indicators) ){
            return $this->em->getRepository(Goal::class)->getValue($indicators, $iteration);
        }

        return 0;
    }

    /**
    * Usage
    ** Per Indicators
    *** getProgression(Array of Indicator)
    *** getProgression(Array of Indicator, Iteration)
    ** Per Indicator
    *** getProgression(Indicator)
    *** getProgression(Indicator, Iteration)
    ** Per Activity
    *** getProgression(Activity)
    *** getProgression(Activity, Iteration)
    ** Per Project
    *** getProgression(Project)
    *** getProgression(Project, Iteration)
    */
    public function getProgression($entity, ?Iteration $iteration = null){
        if ( is_array( $entity ) ){
            $progression = 0;
            foreach($entity as $indicator){
                $progression += $this->getProgression($indicator, $iteration);
            }

            if( count($entity) > 0 ) {
                return (int) ( $progression / count($entity) );
            }
        }

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
            // Get indicators it from repository to avoid EntityNotFoundException
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
            // Get activities it from repository to avoid EntityNotFoundException
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