<?php

namespace App\Upload\Namer;

use Vich\UploaderBundle\Mapping\PropertyMapping;
use Vich\UploaderBundle\Naming\NamerInterface;
use Vich\UploaderBundle\Naming\Polyfill\FileExtensionTrait;
use Vich\UploaderBundle\Util\Transliterator;

class ConventionedFileNamer implements NamerInterface
{
    use FileExtensionTrait;

    public function name($object, PropertyMapping $mapping): string
    {
        $file = $mapping->getFile($object);
        $name = '';
        //$name = Transliterator::transliterate($mapping->getFileNamePropertyName());

        // append the file extension if there is one
        if ($extension = $this->getExtension($file)) {
            //$name = sprintf('%s.%s', $name, $extension);
            $name = '.' . $extension;
        }

        return uniqid() . $name;
    }
}