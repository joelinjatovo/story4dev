<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

use App\Entity\Unit;

class UnitFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $units = [
            'L'    => 'Litre',
            'm'    => 'Mètre',
            's'    => 'Seconde',
            'min'  => 'Minute',
            'h'    => 'Heure',
            'jr'   => 'Jours',
            'sem'  => 'Semaine',
            'mois' => 'Mois',
            'an'   => 'Année',
            'km'   => 'Kilomètre',
            'pers' => 'Personne',
        ];
        foreach($units as $label => $title ){
            $unit = new Unit();
            $unit->setLabel($label);
            $unit->setTitle($title);
            $manager->persist($unit);
        }

        $manager->flush();
    }
}
