<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

use App\Entity\User;
use App\Entity\Contribution\ProjectContribution;
use App\Entity\Contribution\ActivityContribution;
use App\Entity\Project;
use App\Entity\Activity;
use App\Entity\Iteration;
use App\Entity\Indicator;
use App\Entity\Goal;
use App\Entity\Result;
use App\Entity\Report;
use App\Entity\Periodicity;
use App\Entity\Unit;
use App\Entity\Address;
use App\Entity\Meta\ProjectMeta;
use App\Entity\Meta\ActivityMeta;
use App\Entity\Meta\IndicatorMeta;
use App\Entity\Meta\UserMeta;

class AppFixtures extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }
    
    public function load(ObjectManager $manager)
    {
        $madagascar = new Address();
        $madagascar->setCountry("MG");
        $madagascar->setAddressLine1("Madagascar");
        $manager->persist($madagascar);
        
        $france = new Address();
        $france->setCountry("FR");
        $france->setAddressLine1("France");
        $manager->persist($france);
        
        $usa = new Address();
        $usa->setCountry("US");
        $usa->setAddressLine1("Etats-Unis");
        $manager->persist($usa);
        
        $admin = new User();
        $admin->setActive(true);
        $admin->setStatus(User::STATUS_ACTIVE);
        $admin->setActivedAt(new \DateTime());
        $admin->setAddress($madagascar);
        $admin->setFullname('Jason Muller');
        $admin->setUsername('joelinjatovo');
        $admin->setEmail('joelinjatovo@gmail.com');
        $admin->setPassword($this->passwordEncoder->encodePassword($admin, 'admin'));
        $admin->setRoles(['ROLE_ADMIN', 'ROLE_SUPER_ADMIN']);
        $manager->persist($admin);

        $user = new User();
        $user->setActive(true);
        $user->setStatus(User::STATUS_ACTIVE);
        $user->setActivedAt(new \DateTime());
        $user->setAddress($usa);
        $user->setFullname('Matt Pears');
        $user->setUsername('user');
        $user->setEmail('joelinjatovo@yahoo.com');
        $user->setPassword($this->passwordEncoder->encodePassword($user, 'user'));
        $user->setRoles(['ROLE_USER']);
        $manager->persist($user);

        $user1 = new User();
        $user1->setActive(true);
        $user1->setStatus(User::STATUS_ACTIVE);
        $user1->setActivedAt(new \DateTime());
        $user1->setAddress($france);
        $user1->setFullname('Sergei Ford');
        $user1->setUsername('user2');
        $user1->setEmail('serge.ford@agmail.com');
        $user1->setPassword($this->passwordEncoder->encodePassword($user, 'user2'));
        $user1->setRoles(['ROLE_USER']);
        $manager->persist($user1);
        
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
        
        $projects = [
          [
              'title' => 'Project 1',
              'description' => 'I distinguish three main text objecttives.First, your objective could be merely to inform people.A second be to persuade people. You want people buy your products.',
              'author' => $admin,
          ],
          [
              'title' => 'Project 2',
              'description' => 'I distinguish three main text objecttives.First, your objective could be merely to inform people.A second be to persuade people. You want people buy your products.',
              'author' => $user,
          ]  ,
          [
              'title' => 'Project 3',
              'description' => 'I distinguish three main text objecttives.First, your objective could be merely to inform people.A second be to persuade people. You want people buy your products.',
              'author' => $user,
          ]  
        ];

        foreach($projects as $p){
            $project = new Project();
            $project->setTitle( $p['title'] );
            $project->setDescription( $p['description'] );
            $project->setAuthor( $p['author'] );
            $project->setStartAt( new \DateTime() );
            $project->setEndAt( new \DateTime() );

            $manager->persist($project);
            
            $meta = new ProjectMeta();
            $meta->setMetaKey('meta_key');
            $meta->setMetaValue('test_value'. $p['title']);
            $manager->persist($meta);
            
            $project->addMeta($meta);
            
            for($j=1; $j<5; $j++){
                $iteration = new Iteration();
                $iteration->setTitle( 'Trimestre ' . $j );
                $iteration->setProject( $project );
                $iteration->setAuthor($admin);
                $manager->persist( $iteration );
            }

            if ( $project->getAuthor() == $admin ){
                $contribution = new ProjectContribution();
                $contribution->setUser( $admin );
                $contribution->setProject( $project );
                $contribution->setRoles(['ROLE_ADMIN']);
                $manager->persist( $contribution );

                $contribution = new ProjectContribution();
                $contribution->setUser( $user );
                $contribution->setProject( $project );
                $contribution->setRoles(['ROLE_CONTRIBUTOR']);
                $manager->persist( $contribution );
            }else{
                $contribution = new ProjectContribution();
                $contribution->setUser( $user );
                $contribution->setProject( $project );
                $contribution->setRoles(['ROLE_ADMIN']);
                $manager->persist( $contribution );

                $contribution = new ProjectContribution();
                $contribution->setUser( $admin );
                $contribution->setProject( $project );
                $contribution->setRoles(['ROLE_CONTRIBUTOR']);
                $manager->persist( $contribution );
            }
        }

        $manager->flush();
    }
}
