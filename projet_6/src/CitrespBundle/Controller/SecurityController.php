<?php

namespace CitrespBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use CitrespBundle\Entity\BaseCities;
use CitrespBundle\Entity\City;


use CitrespBundle\Form\BaseCitiesChoiceType;
use CitrespBundle\Form\Security\RegistrationByCityType;


use CitrespBundle\Repository;

class SecurityController extends Controller
{

    /**
    * @Route("/registercity/{searchCityZipcode}", name="register_city")
    */
    public function registerCityAction(Request $request, $searchCityZipcode)
    {
        // On récupère les Villes dans BasesCities qui ont ce code postal
        $repository = $this->getDoctrine()->getRepository(BaseCities::class);
        $selectedBaseCities= $repository->getCitiesBaseByCodePostal($searchCityZipcode);

        // On récupère les Villes déjà créée qui ont ce code postal
        $checkCities = $this->getDoctrine()->getRepository(City::class)->findByZipcode($searchCityZipcode);


        $resultCheck = $this->container->get('citresp.citiesNotCreated')->resultCheck($selectedBaseCities, $checkCities);

        if (count($resultCheck) < 1) {
            $this->addFlash('errorSelectedBaseCities', 'Ce code postal ne correspond à aucune ville, ou un compte pour votre ville est déjà créé.');

            return $this->redirectToRoute('homepage');
        }


        $form = $this->createForm(BaseCitiesChoiceType::class, null , ['allow_extra_fields' => $resultCheck] );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $data = $form->getData();

            $city = new City;
            $this->container->get('citresp.HydrateCity')->hydrate($city, $data);

            // Création session SelectedCity
            $session = $this->get('session');
            $session->start();
            $session->set('SelectedCity', $city);

            return $this->redirectToRoute('register_city_admin');
        }

        return $this->render('@Citresp/Security/registerCity.html.twig', [
            'searchCityZipcode' => $searchCityZipcode,
            'form' => $form->createView()
        ]);
    }



    /**
     * @Route("/registercityadmin", name="register_city_admin")
     */
    public function registerCityAdminAction(Request $request)
    {
        $session = $this->get('session');
        $selectedCity = $session->get('SelectedCity');

        $cityName = $selectedCity->getName();

        $form = $this->createForm(RegistrationByCityType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $user = $form->getData();

            $user->setCity($selectedCity);
            $user->addRole("ROLE_ADMIN");
            $user->setEnabled(true);

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('homepage');
        }


        return $this->render('@Citresp/Security/registerCityAdmin.html.twig',
        [
            'form' => $form->createView(),
            'cityName' => $cityName
        ]);
    }

}
