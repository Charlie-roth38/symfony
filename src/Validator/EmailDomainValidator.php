<?php
// src/Validator/ContainsAlphanumericValidator.php
namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class EmailDomainValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {

        if(!str_ends_with($value,'deloitte.com')){
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ string }}', $value)
                ->addViolation();
        }


    }
}
?>