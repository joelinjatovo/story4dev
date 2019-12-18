<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class EmailBlackList extends Constraint
{
    public $message = 'The email "{{ email }}" is not allowed to register here.';
    
    public function validatedBy()
    {
        return \get_class($this).'Validator';
    }
}
