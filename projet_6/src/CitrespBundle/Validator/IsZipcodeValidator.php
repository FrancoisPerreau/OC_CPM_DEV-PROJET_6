<?php
// src/CitrespBundle/Validator/IsZipcodeValidator.php

namespace CitrespBundle\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class IsZipcodeValidator extends ConstraintValidator
{
  public function validate($value, Constraint $constraint)
  {
    
    if (!preg_match ("/^(([0-8][0-9])|(9[0-5]))[0-9]{3}$/", $value))
    {

      $this->context->addViolation($constraint->message);
    }
  }
}
