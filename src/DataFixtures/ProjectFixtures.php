<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

use App\Entity\Meta\MetaProject;
use App\Entity\Project;

class ProjectFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        for($i=1; $i<5; $i++){
            
            $project = new Project();
            $project->setTitle( 'Project ' . $i );
            $project->setDescription( 'Description ' . $i );
            $project->setStartAt( new \DateTime() );
            $project->setEndAt( new \DateTime() );

            $manager->persist($project);
            
            $meta = new MetaProject();
            $meta->setMetaKey('meta_key');
            $meta->setMetaValue('test_value'. $i );
            $manager->persist($meta);
            
            $project->addMeta($meta);
        }

        $manager->flush();
    }
}
