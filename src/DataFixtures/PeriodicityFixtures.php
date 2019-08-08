<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

use App\Entity\Periodicity;

class PeriodicityFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $periodicities = [
            'Jour'      => 1,
            'Semaine'   => 7,
            'Mois'      => 30,
            'Trimestre' => 90,
            'Semestre'  => 180,
            'Année'     => 365,
        ];
        foreach($periodicities as $title => $delay){
            $periodicity = new Periodicity();
            $periodicity->setDelay($delay);
            $periodicity->setTitle($title);
            $manager->persist($periodicity);
        }

        $manager->flush();
    }
}
