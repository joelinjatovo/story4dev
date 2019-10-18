<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;

use App\Entity\Option;

class OptionService
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }
    
    public function get(string $option_key, ?string $default = null, $object = false)
    {
        $option = $this->em->getRepository(Option::class)
                ->findOneBy([
                    'option_key' => $option_key
                ]);
        
        if($option){
            if($object === true ){
                return $option;
            }

            $value = $option->getOptionValue();
            
            return $value;
        }

        return $default;
    }
}
