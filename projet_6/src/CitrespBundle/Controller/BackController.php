<?php

namespace CitrespBundle\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use CitrespBundle\Entity\Category;
use CitrespBundle\Entity\City;
use CitrespBundle\Entity\Comment;
use CitrespBundle\Entity\Reporting;

// use Symfony\Component\Security\Core\Exception\AccessDeniedException;


class BackController extends Controller
{
    /**
     * @Route("/admin/{slug}", name="security_admin")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function adminAction(City $city)
    {
        // Si les Villes sont différents on redirige vers la homepage
        $user = $this->getUser();
        if ($user->getCity() != $city)
        {
            $this->addFlash('errorCityAccess', 'Votre compte n\'est pas pour la ville de ' . $city->getName());

            return $this->redirectToRoute('homepage');
        }

        $em = $this->getDoctrine()->getManager();

        // Google map
        $googleApi = $this->container->getParameter('google_api');
        // $markers = null ;

        // Reportings
        $reportings = $em
            ->getRepository(Reporting::class)
            ->findBy(['city' => $city], ['dateCreated' => 'DESC']);



        return $this->render('@Citresp/Back/admin.html.twig',[
            'googleApi' => $googleApi,
            'city' => $city,
            'reportings' => $reportings
        ]);
    }


    /**
     * @Route("/admin-moderator/{slug}", name="security_moderator")
     * @Security("has_role('ROLE_MODERATOR')")
     */
    public function moderatorAction(City $city)
    {
        // Si les Villes sont différents on redirige vers la homepage
        $user = $this->getUser();
        if ($user->getCity() != $city)
        {
            $this->addFlash('errorCityAccess', 'Votre compte n\'est pas pour la ville de ' . $city->getName());

            return $this->redirectToRoute('homepage');
        }

        $em = $this->getDoctrine()->getManager();

        // Google map
        $googleApi = $this->container->getParameter('google_api');
        // $markers = null ;

        // Reportings
        $reportings = $em
            ->getRepository(Reporting::class)
            ->findBy(['city' => $city], ['dateCreated' => 'DESC']);



        return $this->render('@Citresp/Back/adminModerator.html.twig',[
            'googleApi' => $googleApi,
            'city' => $city,
            'reportings' => $reportings
        ]);
    }


    /**
     * @Route("/admin-city/{slug}", name="security_city")
     * @Security("has_role('ROLE_CYTI')")
     */
    public function cityAction(City $city)
    {
        // Si les Villes sont différents on redirige vers la homepage
        $user = $this->getUser();
        if ($user->getCity() != $city)
        {
            $this->addFlash('errorCityAccess', 'Votre compte n\'est pas pour la ville de ' . $city->getName());

            return $this->redirectToRoute('homepage');
        }

        $em = $this->getDoctrine()->getManager();

        // Google map
        $googleApi = $this->container->getParameter('google_api');
        // $markers = null ;

        // Reportings
        $reportings = $em
            ->getRepository(Reporting::class)
            ->findBy(['city' => $city], ['dateCreated' => 'DESC']);



        return $this->render('@Citresp/Back/adminCity.html.twig',[
            'googleApi' => $googleApi,
            'city' => $city,
            'reportings' => $reportings
        ]);
    }
}
