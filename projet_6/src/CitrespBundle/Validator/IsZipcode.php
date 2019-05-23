<?php
// src/CitrespBundle/Validator/IsZipcode.php
namespace CitrespBundle\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class IsZipcode extends Constraint
{
  public $message = "Ce n'est pas un code postal valide.";
}
