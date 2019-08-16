<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

use App\Entity\User;
use App\Entity\Contribution;
use App\Entity\Project;
use App\Entity\Activity;
use App\Entity\Iteration;
use App\Entity\Indicator;
use App\Entity\Goal;
use App\Entity\Result;
use App\Entity\Report;
use App\Entity\Periodicity;
use App\Entity\Unit;
use App\Entity\Meta\MetaProject;
use App\Entity\Meta\MetaActivity;
use App\Entity\Meta\MetaIndicator;
use App\Entity\Meta\MetaUser;

class AppFixtures extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }
    
    public function load(ObjectManager $manager)
    {
        $admin = new User();
        $admin->setActive(true);
        $admin->setStatus(User::STATUS_ACTIVE);
        $admin->setActivedAt(new \DateTime());
        $admin->setUsername('joelinjatovo');
        $admin->setEmail('joelinjatovo@gmail.com');
        $admin->setPassword($this->passwordEncoder->encodePassword($admin, 'admin'));
        $admin->setRoles(['ROLE_ADMIN']);
        $manager->persist($admin);

        $user = new User();
        $user->setActive(true);
        $user->setStatus(User::STATUS_ACTIVE);
        $user->setActivedAt(new \DateTime());
        $user->setUsername('user');
        $user->setEmail('joelinjatovo@yahoo.com');
        $user->setPassword($this->passwordEncoder->encodePassword($user, 'user'));
        $user->setRoles(['ROLE_USER']);
        $manager->persist($user);
        
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
            $periodicity->setAuthor($admin);
            $manager->persist($periodicity);
        }
        
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
            $unit->setAuthor($admin);
            $manager->persist($unit);
        }

        for($i=1; $i<=5; $i++){
            $project = new Project();
            $project->setTitle( 'Project ' . $i );
            $project->setDescription( 'Description ' . $i );
            $project->setStartAt( new \DateTime() );
            $project->setEndAt( new \DateTime() );
            $project->setAuthor($admin);

            $manager->persist($project);
            
            $meta = new MetaProject();
            $meta->setMetaKey('meta_key');
            $meta->setMetaValue('test_value'. $i );
            $manager->persist($meta);
            
            $project->addMeta($meta);
            
            for($j=1; $j<5; $j++){
                $iteration = new Iteration();
                $iteration->setTitle( 'Trimestre ' . $j );
                $iteration->setProject( $project );
                $iteration->setAuthor($admin);
                $manager->persist( $iteration );
            }

            $contribution = new Contribution();
            $contribution->setUser( $admin );
            $contribution->setProject( $project );
            $contribution->setRoles(['ROLE_ADMIN']);
            $manager->persist( $contribution );

            $contribution = new Contribution();
            $contribution->setUser( $user );
            $contribution->setProject( $project );
            $contribution->setRoles(['ROLE_CONTRIBUTOR']);
            $manager->persist( $contribution );
        }

        $manager->flush();
    }
}
