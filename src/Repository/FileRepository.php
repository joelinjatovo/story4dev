<?php

namespace App\Repository;

use App\Entity\File;
use App\Entity\User;
use App\Entity\Project;
use App\Entity\ProjectContribution;
use App\Entity\Activity;
use App\Entity\ActivityFile;
use App\Entity\Report;
use App\Entity\ReportFile;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method File|null find($id, $lockMode = null, $lockVersion = null)
 * @method File|null findOneBy(array $criteria, array $orderBy = null)
 * @method File[]    findAll()
 * @method File[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FileRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, File::class);
    }
    
    public function findAllQuery(User $user)
    {
        return $this->createQueryBuilder('f')
            ->innerJoin('f.activityFiles', 'af')
            ->innerJoin('af.activity', 'a')
            ->innerJoin('a.project', 'p')
            ->innerJoin('p.contributions', 'c')
            ->where('c.user = :user OR p.author = :user')
            ->setParameter('user', $user)
            ->getQuery()
        ;
    }
    
    /**
    * Tous les fichiers situés dans les projets
    * sur lesquels l'utilisateur contribue
    */
    public function findByUser(User $user, $args = [])
    {
        $default = [
            'type'    => "all",  
            'limit'   => 0,  
        ];
        $args = array_merge($default, (array) $args);
        
        $subquery = '';
        switch($args['type']){
            case 'image':
                $subquery = ' AND f.mimeType LIKE :mimeType';
            break;
            case 'document':
                $subquery = ' AND f.mimeType NOT LIKE :mimeType';
            break;
        }
        
        $em = $this->getEntityManager();
        
        $query = $em->createQuery(
            "SELECT f FROM " . File::class . " f " .
            "WHERE " .
                " ( (" .
                    "f IN " .
                        "(" .
                            "SELECT IDENTITY(af.file) FROM " . ActivityFile::class . " af " .
                            " LEFT JOIN " . Activity::class . " act1 WITH act1 = af.activity " .
                            " LEFT JOIN " . ProjectContribution::class . " pc1 WITH pc1.project = act1.project " .
                            " WHERE pc1.user = :user" .
                        ")" .
                ") OR (" .
                    "f IN " .
                        "(" .
                            "SELECT IDENTITY(rf.file) FROM " . ReportFile::class . " rf " .
                            " LEFT JOIN " . Report::class . " rep WITH rep = rf.report " .
                            " LEFT JOIN " . Activity::class . " act2 WITH act2 = rep.activity " .
                            " LEFT JOIN " . ProjectContribution::class . " pc2 WITH pc2.project = act2.project " .
                            " WHERE pc2.user = :user" .
                        ")" .
                ") ) " .
                ( ! empty( $subquery ) ? $subquery : '')
        )->setParameter("user", $user);
        
        if( ! empty( $subquery )){
            $query->setParameter("mimeType", 'image/%');
        }
        
        if( $args['limit'] > 0 ){
            return $query->setMaxResults((int) $args['limit']);
        }
        return $query;
        
        return $this->createQueryBuilder('f')
            ->innerJoin('f.activityFiles', 'af')
            ->innerJoin('af.activity', 'a')
            ->innerJoin('a.project', 'p')
            ->innerJoin('p.contributions', 'c')
            ->where('c.user = :user OR p.author = :user')
            ->setParameter('user', $user)
            ->getQuery()
        ;
    }
    
    /**
    * Compter tous les fichiers du projet
    */
    public function countByProject(Project $project)
    {
        $em = $this->getEntityManager();
        
        return $em->createQuery(
            "SELECT count(f.id) FROM " . File::class . " f " .
            "WHERE " .
                "(" .
                    "f IN " .
                        "(" .
                            "SELECT IDENTITY(af.file) FROM " . ActivityFile::class . " af " .
                            " LEFT JOIN " . Activity::class . " act1 WITH act1 = af.activity " .
                            " WHERE act1.project = :project" .
                        ")" .
                ") OR (" .
                    "f IN " .
                        "(" .
                            "SELECT IDENTITY(rf.file) FROM " . ReportFile::class . " rf " .
                            " LEFT JOIN " . Report::class . " rep WITH rep = rf.report " .
                            " LEFT JOIN " . Activity::class . " act2 WITH act2 = rep.activity " .
                            " WHERE act2.project = :project" .
                        ")" .
                ")"
        )->setParameter("project", $project);
    }
    
    /**
    * Tous les fichiers situés dans le projet
    */
    public function findByProject(Project $project, ?User $user = null)
    {
        $em = $this->getEntityManager();
        
        return $em->createQuery(
            "SELECT f FROM " . File::class . " f " .
            "WHERE " .
                "(" .
                    "f IN " .
                        "(" .
                            "SELECT IDENTITY(af.file) FROM " . ActivityFile::class . " af " .
                            " LEFT JOIN " . Activity::class . " act1 WITH act1 = af.activity " .
                            " WHERE act1.project = :project" .
                        ")" .
                ") OR (" .
                    "f IN " .
                        "(" .
                            "SELECT IDENTITY(rf.file) FROM " . ReportFile::class . " rf " .
                            " LEFT JOIN " . Report::class . " rep WITH rep = rf.report " .
                            " LEFT JOIN " . Activity::class . " act2 WITH act2 = rep.activity " .
                            " WHERE act2.project = :project" .
                        ")" .
                ")"
        )->setParameter("project", $project);
    }
    
    /**
    * Tous les fichiers situés dans l'activité
    */
    public function findByActivity(Activity $activity, $type = 'all', $limit = 0)
    {
        $subquery = '';
        switch($type){
            case 'image':
                $subquery = ' AND f.mimeType LIKE :mimeType';
            break;
            case 'document':
                $subquery = ' AND f.mimeType NOT LIKE :mimeType';
            break;
        }
        
        $em = $this->getEntityManager();
        
        $query = $em->createQuery(
            "SELECT f FROM " . File::class . " f " .
            "WHERE " .
                " ( (" .
                    "f IN " .
                        "(" .
                            "SELECT IDENTITY(af.file) FROM " . ActivityFile::class . " af " .
                            " WHERE af.activity = :activity" .
                        ")" .
                ") OR (" .
                    "f IN " .
                        "(" .
                            "SELECT IDENTITY(rf.file) FROM " . ReportFile::class . " rf " .
                            " LEFT JOIN " . Report::class . " rep WITH rep = rf.report " .
                            " WHERE rep.activity = :activity" .
                        ")" .
                ") ) " .
                ( ! empty( $subquery ) ? $subquery : '')
        )->setParameter("activity", $activity);
        
        if( ! empty( $subquery )){
            $query->setParameter("mimeType", 'image/%');
        }
        
        if($limit > 0){
            return $query->setMaxResults($limit);
        }
        return $query;
    }
    
    public function findByProjectPerMonth(Project $project)
    {

        return $this->createQueryBuilder('f')
             ->select("f.createdAt as dateAsMonth, count(f.id) as count")
            ->leftJoin('f.activityFiles', 'af')
            ->leftJoin('af.activity', 'a')
            ->leftJoin('a.project', 'p')
            ->leftJoin('p.contributions', 'c')
            ->where('a.project = :project')
            ->setParameter('project', $project)
            ->orderBy('f.id', 'ASC')
            ->groupBy("dateAsMonth")
            ->getQuery()
            ->getResult()
        ;
    }
    
    public function findByProjectPerActivity(Project $project)
    {

        return $this->createQueryBuilder('f')
            ->select("a.title, count(f.id) as count")
            ->leftJoin('f.activityFiles', 'af')
            ->leftJoin('af.activity', 'a')
            ->leftJoin('a.project', 'p')
            ->leftJoin('p.contributions', 'c')
            ->where('a.project = :project')
            ->setParameter('project', $project)
            ->orderBy('f.id', 'ASC')
            ->groupBy("a.id")
            ->getQuery()
            ->getResult()
        ;
    }

    // /**
    //  * @return File[] Returns an array of File objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('f.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?File
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}