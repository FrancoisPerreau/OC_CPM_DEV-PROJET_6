<?php

namespace CitrespBundle\Controller;


use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;

use Doctrine\ORM\EntityManagerInterface;

use CitrespBundle\Entity\City;

use CitrespBundle\Form\CitySelectType;
use CitrespBundle\Form\BaseCitiesSearchType;



class FrontController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        
        // Formulaire CitySelect
        $formSelect = $this->createForm(CitySelectType::class);
        $formSelect->handleRequest($request);

        if ($formSelect->isSubmitted() && $formSelect->isValid())
        {
          $data = $formSelect->getData();

          $selectedCityName = $data['selectedCity']->getName();
          $selectedCityZipCode = $data['selectedCity']->getZipcode();
          $selectCitySlug = $data['selectedCity']->getSlug();

          return $this->redirectToRoute('city',[
              'slug' => $selectCitySlug]);
        }


        // Formulaire BaseCitiesSearch
        $formSearch = $this->createForm(BaseCitiesSearchType::class);
        $formSearch->handleRequest($request);

        if ($formSelect->isSubmitted() && $formSelect->isValid())
        {
          $data = $formSearch->getData();
          $searchCityZipcode = $data['searchedCity'];

          return $this->redirectToRoute('register_city',[
              'searchCityZipcode' => $searchCityZipcode
          ]);
        }


        return $this->render('@Citresp/Front/homepage.html.twig', [
          'formSelect' => $formSelect->createView(),
          'formSearch' => $formSearch->createView()
        ]);

    }




    /**
     * @Route("/city/{slug}", name="city")
     * @ParamConverter("city", class="CitrespBundle\Entity\City")
     */
    public function cityAction(City $city)
    {

        return $this->render('@Citresp/Front/city.html.twig', [
          'city' => $city
    ]);
    }
}
