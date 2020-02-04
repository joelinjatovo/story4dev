<?php

namespace App\Upload\Namer;

use Doctrine\Common\Persistence\ManagerRegistry;
use Vich\UploaderBundle\Mapping\PropertyMapping;
use Vich\UploaderBundle\Naming\DirectoryNamerInterface;
use Vich\UploaderBundle\Util\Transliterator;

class ConventionedDirectoryNamer implements DirectoryNamerInterface
{
    /**
     * @var ManagerRegistry
     */
    private $doctrine;

    /**
     * @param ManagerRegistry $doctrine
     */
    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function directoryName($object, PropertyMapping $mapping): string
    {
        return sprintf('%s', $this->getShortClassName($object));
    }

    /**
     * Get short class name of given object :
     *  - App\Entity\Project : project
     *  - App\Entity\User : user
     *  - App\Entity\File : file
     *
     * @param object $object
     *
     * @return string
     */
    private function getShortClassName($object)
    {
        $fqcn = get_class($object);
        $classParts = explode('\\', $fqcn);
        
        switch (true) {
            case $object instanceof \App\Entity\User:
                $name = 'user';
                break;
            case $object instanceof \App\Entity\Project:
                $name = 'project';
                break;
            case $object instanceof \App\Entity\File:
                $name = 'file';
                break;
            default:
                $name = 'default';
        }

        //return Transliterator::transliterate(array_pop($classParts));
        
        return $name;
    }
}