<?php

namespace CitrespBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;


class BackController extends Controller
{
    /**
     * @Route("/admin", name="security_admin")
     */
    public function adminAction()
    {
        // On vérifie que l'utilisateur dispose bien du rôle ROLE_AUTEUR
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
          // Sinon on déclenche une exception « Accès interdit »
          throw new AccessDeniedException('Accès limité aux administrateurs.');
        }

        return $this->render('@Citresp/Back/admin.html.twig');
    }


    /**
     * @Route("/admin-moderator", name="security_moderator")
     */
    public function moderatorAction()
    {
        // On vérifie que l'utilisateur dispose bien du rôle ROLE_AUTEUR
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_MODERATOR')) {
          // Sinon on déclenche une exception « Accès interdit »
          throw new AccessDeniedException('Accès limité aux modérateurs.');
        }

        return $this->render('@Citresp/Back/moderator.html.twig');
    }


    /**
     * @Route("/admin-city", name="security_city")
     */
    public function cityAction()
    {
        // On vérifie que l'utilisateur dispose bien du rôle ROLE_AUTEUR
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_CYTI')) {
          // Sinon on déclenche une exception « Accès interdit »
          throw new AccessDeniedException('Accès limité aux administrateurs de la ville.');
        }

        return $this->render('@Citresp/Back/cityadmin.html.twig');
    }
}
