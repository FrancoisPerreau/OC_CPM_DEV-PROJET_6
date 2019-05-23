<?php

namespace CitrespBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use CitrespBundle\Entity\BaseCities;
use CitrespBundle\Entity\City;

// use CitrespBundle\Form\BaseCitiesType;
// use CitrespBundle\Form\CityType;
use CitrespBundle\Form\CityChoiceType;
use CitrespBundle\Form\Security\RegistrationByCityType;


use CitrespBundle\Repository;

class SecurityController extends Controller
{

    /**
    * @Route("/registercity/{searchCityZipcode}", name="register_city")
    */
    public function registerCityAction(Request $request, $searchCityZipcode)
    {
        $repository = $this->getDoctrine()->getRepository(BaseCities::class);
        $selectedBaseCities= $repository->getCitiesBaseByCodePostal($searchCityZipcode);


        $baseCities = new BaseCities;
        $form = $this->createForm(CityChoiceType::class, null , ['allow_extra_fields' => $selectedBaseCities] );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $data = $form->getData();

            $city = new City;
            $city->setName($data['selectedCity']->getNomCommune());
            $city->setZipcode($data['selectedCity']->getCodePostal());
            $city->setGpsCoordinates($data['selectedCity']->getCoordonneesGps());

            $session = $this->get('session');
            $session->start();
            $session->set('SelectedCity', $city);

            $getCitySession = $session->get('SelectedCity');

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
        // dump($selectedCity);
        // die;

        $form = $this->createForm(RegistrationByCityType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $user = $form->getData();

            $user->setCity($selectedCity);
            $user->addRole("ROLE_ADMIN");
            $user->setEnabled(true);

            // dump($selectedCity);
            // dump($user);
            // die;

            // $selectedCityName = $data['selectedCity']->getNomCommune();
            // $selectedCityZipCode = $data['selectedCity']->getCodePostal();
            // $selectedCityID = $data['selectedCity']->getId();

            // dump($selectedCityName);
            // dump($selectedCityZipCode);
            // dump($selectedCityID);
            // dump($city);
            // dump($getCitySession);
            // die;

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('homepage');
        }


        return $this->render('@Citresp/Security/register_city_admin.html.twig',
        [
            'form' => $form->createView(),
            'cityName' => $cityName
            // 'last_username' => $authenticationUtils->getLastUsername(),
            // 'error' => $authenticationUtils->getLastAuthenticationError(),
        ]);
    }


}
