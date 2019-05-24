<?php

namespace CitrespBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Doctrine\ORM\EntityManagerInterface;

use CitrespBundle\Entity\City;
use CitrespBundle\Entity\Reporting;
use CitrespBundle\Entity\Comment;

use CitrespBundle\Form\CitySelectType;
use CitrespBundle\Form\BaseCitiesSearchType;
use CitrespBundle\Form\Security\RegistrationType;



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

        if ($formSearch->isSubmitted() && $formSearch->isValid())
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
     * @Security("has_role('ROLE_USER')")
     */
    public function cityAction(City $city)
    {
        $em = $this->getDoctrine()->getManager();
        $googleApi = $this->container->getParameter('google_api');

        // $user = $this->getUser();
        $cityGps = $city->getGpsCoordinates();

        // Coordonnées GPS de la ville pour google map
        $coordinates = explode(', ', $cityGps);
        $cityLat = $coordinates[0];
        $cityLng = $coordinates[1];

        $reportings = $em
            ->getRepository(Reporting::class)
            ->findBy(['city' => $city]);


        // MARKER
        $markers = null ;


        foreach ($reportings as $reporting) {

            $reportingGPS = $reporting->getGpsCoordinates();
            $coordinates = explode(', ', $reportingGPS);

            $markers[] = ['markerLat' => $coordinates[0], 'markerLng' => $coordinates[1]];
        }





        // // Si les slug sont différents on redirige vers la homepage
        // if ($user->getCity()->getSlug() != $city->getSlug())
        // {
        //     return $this->redirectToRoute('homepage');
        // }

        return $this->render('@Citresp/Front/city.html.twig', [
          'googleApi' => $googleApi,
          'city' => $city,
          'cityLat'=> $cityLat,
          'cityLng' => $cityLng,
          'markers' =>$markers,
          'reportings' => $reportings
        ]);
    }



    /**
     * @Route("/city/{slug}/reportings/{reporting_id}", name="show_reporting")
     * @Entity("reporting", expr="repository.find(reporting_id)")
     * @Security("has_role('ROLE_USER')")
     */
    public function showReportingAction(City $city, Reporting $reporting)
    {
        $em = $this->getDoctrine()->getManager();
        $googleApi = $this->container->getParameter('google_api');

        // $user = $this->getUser();
        $cityGps = $city->getGpsCoordinates();

        // Coordonnées GPS de la ville pour google map
        $coordinates = explode(', ', $cityGps);
        $cityLat = $coordinates[0];
        $cityLng = $coordinates[1];



        // MARKER
        $reportingGPS = $reporting->getGpsCoordinates();
        $coordinates = explode(', ', $reportingGPS);

        $markerLat = $coordinates[0];
        $markerLng = $coordinates[1];



        // COMMENTS
        $comments =  $em
            ->getRepository(Comment::class)
            ->findBy(['reporting' => $reporting]);;




        // // Si les slug sont différents on redirige vers la homepage
        // if ($user->getCity()->getSlug() != $city->getSlug())
        // {
        //     return $this->redirectToRoute('homepage');
        // }

        return $this->render('@Citresp/Front/showReporting.html.twig', [
          'googleApi' => $googleApi,
          'city' => $city,
          'cityLat'=> $cityLat,
          'cityLng' => $cityLng,
          'markerLat' => $markerLat,
          'markerLng' => $markerLng,
          'reporting' => $reporting,
          'comments' => $comments
        ]);
    }

}
