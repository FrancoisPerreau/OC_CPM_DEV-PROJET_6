<?php

namespace CitrespBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class CitrespBundle extends Bundle
{
  public function getParent()
  {
    return 'FOSUserBundle';
  }
}
