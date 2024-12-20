<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class CurrentPassword extends Constraint
{
    public $message = 'Le mot de passe actuel est incorrect.';
}