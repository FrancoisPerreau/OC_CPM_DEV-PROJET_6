<?php

namespace CitrespBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use CitrespBundle\Entity\BaseCities;
use CitrespBundle\Entity\City;

use CitrespBundle\Form\BaseCitiesType;
use CitrespBundle\Form\CityType;
use CitrespBundle\Form\CityChoiceType;

use CitrespBundle\Repository;

class SecurityController extends Controller
{
    /**
     * @Route("/login", name="security_login")
     */
    public function loginAction(Request $request)
    {

        if ($this->get('security.authorization_checker')
                 ->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirectToRoute('homepage');
        }

        $authenticationUtils = $this->get('security.authentication_utils');

        return $this->render('@Citresp/Security/login.html.twig',
        [
            'last_username' => $authenticationUtils->getLastUsername(),
            'error' => $authenticationUtils->getLastAuthenticationError(),
        ]);
    }



    /**
    * @Route("/registercity/{searchCityZipcode}", name="register_city")
    */
    public function registerCityAction(Request $request, $searchCityZipcode)
    {
        $repository = $this->getDoctrine()->getRepository(BaseCities::class);
        $selectedBaseCities= $repository->getCitiesBaseByCodePostal($searchCityZipcode);


        // dump($selectedBaseCities);
        // die;
        $baseCities = new BaseCities;
        $form = $this->createForm(CityChoiceType::class, null , ['allow_extra_fields' => $selectedBaseCities] );
        $form->handleRequest($request);

        if ($form->isValid() && $form->isSubmitted())
        {
            $data = $form->getData();
            $selectedCityName = $data['selectedCity']->getNomCommune();
            $selectedCityZipCode = $data['selectedCity']->getCodePostal();
            $selectedCityID = $data['selectedCity']->getId();
            
            dump($selectedCityName);
            dump($selectedCityZipCode);
            dump($selectedCityID);

            die;
        }


        return $this->render('@Citresp/Security/registerCity.html.twig', [
            'searchCityZipcode' => $searchCityZipcode,
            'form' => $form->createView()
        ]);
    }


}
