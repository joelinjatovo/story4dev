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

        $user2 = new User();
        $user2->setActive(true);
        $user2->setStatus(User::STATUS_ACTIVE);
        $user2->setActivedAt(new \DateTime());
        $user2->setAddress(null);
        $user2->setFullname('Haja TIANA');
        $user2->setUsername('haja.joelinjatovo');
        $user2->setEmail('haja.joelinjatovo@agmail.com');
        $user2->setPassword($this->passwordEncoder->encodePassword($user, 'joelinjatovo'));
        $user2->setRoles(['ROLE_USER']);
        $manager->persist($user2);

        $user3 = new User();
        $user3->setActive(true);
        $user3->setStatus(User::STATUS_ACTIVE);
        $user3->setActivedAt(new \DateTime());
        $user3->setAddress(null);
        $user3->setFullname('Haja MANJAKA');
        $user3->setUsername('haja.emedia');
        $user3->setEmail('haja.emedia@agmail.com');
        $user3->setPassword($this->passwordEncoder->encodePassword($user, 'user'));
        $user3->setRoles(['ROLE_USER']);
        $manager->persist($user3);
        
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
            'pers' => 'Personne',
            'nbr'  => 'Nombre',
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
              'description' => '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Quis eleifend quam adipiscing vitae proin sagittis nisl. Quam lacus suspendisse faucibus interdum. Bibendum arcu vitae elementum curabitur vitae nunc sed velit dignissim. Euismod quis viverra nibh cras pulvinar mattis nunc. Mattis aliquam faucibus purus in massa tempor nec feugiat nisl. Mi eget mauris pharetra et ultrices neque ornare aenean euismod. Nibh venenatis cras sed felis eget velit aliquet sagittis id. Velit sed ullamcorper morbi tincidunt ornare massa eget egestas. Vestibulum rhoncus est pellentesque elit. In dictum non consectetur a erat nam at lectus. Faucibus nisl tincidunt eget nullam non nisi. Nec sagittis aliquam malesuada bibendum arcu vitae elementum curabitur. Tempus quam pellentesque nec nam aliquam sem et tortor. Maecenas ultricies mi eget mauris pharetra et ultrices neque ornare. Vulputate sapien nec sagittis aliquam. Velit euismod in pellentesque massa placerat duis ultricies. Massa eget egestas purus viverra accumsan. Ipsum dolor sit amet consectetur adipiscing. Eleifend quam adipiscing vitae proin sagittis.</p> <p>Ornare arcu dui vivamus arcu felis. Nunc pulvinar sapien et ligula ullamcorper. Odio ut enim blandit volutpat. Ut tortor pretium viverra suspendisse potenti nullam ac tortor. Sed id semper risus in. Accumsan lacus vel facilisis volutpat. Orci a scelerisque purus semper eget duis at tellus at. Massa tempor nec feugiat nisl pretium. Velit aliquet sagittis id consectetur purus ut faucibus pulvinar elementum. Porttitor massa id neque aliquam vestibulum morbi blandit. Aliquet bibendum enim facilisis gravida neque convallis a cras. Quam pellentesque nec nam aliquam sem et tortor. Proin nibh nisl condimentum id. Vulputate odio ut enim blandit volutpat maecenas volutpat blandit aliquam. Curabitur vitae nunc sed velit. Ut porttitor leo a diam sollicitudin tempor id.</p>',
              'author' => $admin,
          ],
          [
              'title' => 'Project 2',
              'description' => '<p>Semper viverra nam libero justo laoreet sit amet. Adipiscing at in tellus integer feugiat scelerisque varius. Feugiat scelerisque varius morbi enim. Quis enim lobortis scelerisque fermentum dui faucibus in ornare. Urna cursus eget nunc scelerisque viverra mauris in aliquam sem. Et magnis dis parturient montes nascetur ridiculus mus. Egestas maecenas pharetra convallis posuere morbi leo urna molestie. A lacus vestibulum sed arcu non. Dui ut ornare lectus sit amet. Egestas purus viverra accumsan in nisl nisi scelerisque eu ultrices. Ut sem viverra aliquet eget sit amet. Leo integer malesuada nunc vel risus commodo. Vitae et leo duis ut. Accumsan tortor posuere ac ut. Ut eu sem integer vitae justo eget magna fermentum iaculis.</p><p>Eget egestas purus viverra accumsan. Lectus urna duis convallis convallis tellus id. At volutpat diam ut venenatis tellus in metus vulputate eu. Mauris a diam maecenas sed enim ut sem viverra aliquet. Nisl nisi scelerisque eu ultrices vitae auctor eu. Nunc mi ipsum faucibus vitae aliquet nec ullamcorper sit. Lectus magna fringilla urna porttitor rhoncus dolor purus. Faucibus in ornare quam viverra orci sagittis. Leo a diam sollicitudin tempor id eu. Nec ultrices dui sapien eget mi. Sit amet consectetur adipiscing elit duis tristique sollicitudin. Eu turpis egestas pretium aenean pharetra magna ac placerat vestibulum.</p>',
              'author' => $user,
          ]  ,
          [
              'title' => 'Project 3',
              'description' => '<p>Condimentum mattis pellentesque id nibh tortor id. Quisque non tellus orci ac auctor augue. Lobortis scelerisque fermentum dui faucibus in ornare quam viverra. Felis imperdiet proin fermentum leo vel orci. In nisl nisi scelerisque eu ultrices. Dapibus ultrices in iaculis nunc sed augue. Facilisis mauris sit amet massa vitae tortor condimentum lacinia. Non pulvinar neque laoreet suspendisse interdum consectetur. Diam quis enim lobortis scelerisque fermentum. Nam aliquam sem et tortor consequat id porta. Interdum consectetur libero id faucibus nisl tincidunt eget nullam. Sit amet dictum sit amet justo. Sed risus ultricies tristique nulla aliquet enim tortor. Suspendisse faucibus interdum posuere lorem. Nunc pulvinar sapien et ligula ullamcorper malesuada proin. Pharetra convallis posuere morbi leo urna molestie at. Et malesuada fames ac turpis egestas integer eget aliquet. Turpis egestas maecenas pharetra convallis posuere morbi. Facilisi cras fermentum odio eu. Morbi tempus iaculis urna id volutpat lacus laoreet non curabitur.</p>',
              'author' => $user,
          ]  
        ];

        $first = true;
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
            
            if( $first ){
                $activities = [
                    [
                        'title' => 'Activity 1.0',
                        'contactEmail' => 'joelinjatovo@gmail.com',
                        'contactPhone' => '+261 331377768',
                    ],
                    [
                        'title' => 'Activity 1.1',
                        'contactEmail' => 'joelinjatovo@yahoo.com',
                        'contactPhone' => '+261 341377768',
                    ],
                    [
                        'title' => 'Activity 1.2',
                        'contactEmail' => 'haja.joelinjatovo@gmail.com',
                        'contactPhone' => '+261 321377768',
                    ],
                    [
                        'title' => 'Activity 1.3',
                        'contactEmail' => 'haja.joelinjatovo@gmail.com',
                        'contactPhone' => '+261 321377768',
                    ],
                    [
                        'title' => 'Activity 1.4',
                        'contactEmail' => 'haja.joelinjatovo@gmail.com',
                        'contactPhone' => '+261 321377768',
                    ]
                ];
                
                foreach($activities as $activity){
                    $activityObject = new Activity();
                    $activityObject->setTitle($activity['title']);
                    $activityObject->setContactEmail($activity['contactEmail']);
                    $activityObject->setContactPhone($activity['contactPhone']);
                    $activityObject->setBudget(0);
                    $manager->persist( $activityObject );
                }
                    
                $first = false;
            }
        }

        $manager->flush();
    }
}
