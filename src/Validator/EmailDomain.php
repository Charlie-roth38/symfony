<?php
// src/Validator/ContainsAlphanumeric.php
namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class EmailDomain extends Constraint
{
    public $message = 'L\'adresse email"{{ string }}" ne se termine pas par deloitte.com';

    // in the base Symfony\Component\Validator\Constraint class
    public function validatedBy()
    {
        return static::class.'Validator';
    }
}