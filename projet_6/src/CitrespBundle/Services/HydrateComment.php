<?php
//src/CitrespBundle/Services/HydrateComment.php

namespace CitrespBundle\Services;

use CitrespBundle\Entity\Comment;
use CitrespBundle\Entity\Reporting;
use CitrespBundle\Entity\User;

class HydrateComment
{
  public function hydrate(User $user, Reporting $reporting, Comment $comment)
  {
    $comment->setUser($user);
    $comment->setReporting($reporting);
  }
}
