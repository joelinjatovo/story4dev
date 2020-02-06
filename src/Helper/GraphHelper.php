<?php

namespace App\Helper;

use App\Entity\Axe;
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

class GraphHelper
{
    protected $em;

    public function __construct( \Doctrine\ORM\EntityManagerInterface $em) {
        $this->em = $em;
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
                    'unit'  => $indicator->getUnit()->getTitle(),
                ];
            }

            return $series;
        }
        
        if($entity instanceof Activity){
            // Get indicators it from repository to avoid EntityNotFoundException
            $indicators = $this->em->getRepository(Indicator::class)->findBy(['activity' => $entity]);
            return $this->getChartSeries($indicators);
        }
        
        if($entity instanceof Project){
            // Get indicators it from repository to avoid EntityNotFoundException
            $indicators = $this->em->getRepository(Indicator::class)->findByProject($entity)->execute();
            return $this->getChartSeries($indicators);
        }

        return [];
    }

    public function getChartData($entity, $axes, $withIndicator = true){
        $datas = [];

        if( is_array( $entity ) ){
            $indicators = $entity;
            
            foreach($axes as $axe){
                $data = [
                    "iteration"   => $axe->getTitle(),
                    "value"       => $this->getResult($indicators, $axe),
                ];

                if( $withIndicator ) {
                    foreach($indicators as $indicator){
                        $data['i_'.$indicator->getId()] = $this->getResult($indicator, $axe);
                    }
                }

                $datas[] = $data;
            }

            return $datas;
        }

        if($entity instanceof Activity){
            // Get indicators it from repository to avoid EntityNotFoundException
            $indicators = $this->em->getRepository(Indicator::class)->findBy(['activity' => $entity]);
            return $this->getChartData($indicators, $axes, $withIndicator);
        }

        if($entity instanceof Project){
            // Get indicators it from repository to avoid EntityNotFoundException
            $indicators = $this->em->getRepository(Indicator::class)->findByProject($entity)->execute();
            return $this->getChartData($indicators, $axes, $withIndicator);
        }

        if($entity instanceof Indicator){
            return $this->getChartData([$entity], $axes, $withIndicator);
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
    public function getResult($entity, ?Axe $axe = null){
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
            return $this->em->getRepository(Result::class)->getValueByAxe($indicators, $axe);
        }

        return 0;
    }
}