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
        return sprintf('%s/%s', $this->getShortClassName($object), $this->getIdentifier($object));
    }

    /**
     * Get short class name of given object :
     *  - App\Entity\Project : project
     *  - App\Entity\User : user
     *
     * @param object $object
     *
     * @return string
     */
    private function getShortClassName($object)
    {
        $fqcn = get_class($object);
        $classParts = explode('\\', $fqcn);

        return Transliterator::transliterate(array_pop($classParts));
    }

    /**
     * Get identifier given object.
     * Use Doctrine metadata as a generic method.
     *
     * @param object $object
     *
     * @return string
     */
    private function getIdentifier($object)
    {
        $fqcn = get_class($object);
        $identifiers = $this->doctrine->getManagerForClass($fqcn)
            ->getClassMetadata($fqcn)
            ->getIdentifierValues($object);

        return Transliterator::transliterate(reset($identifiers));
    }
}