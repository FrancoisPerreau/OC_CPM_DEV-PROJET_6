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

use CitrespBundle\Entity\Category;
use CitrespBundle\Entity\City;
use CitrespBundle\Entity\Comment;
use CitrespBundle\Entity\Reporting;
use CitrespBundle\Entity\Status;
use CitrespBundle\Entity\User;

use CitrespBundle\Form\BaseCitiesSearchType;
use CitrespBundle\Form\CitySelectType;
use CitrespBundle\Form\CommentType;
use CitrespBundle\Form\RegisterReportingType;
use CitrespBundle\Form\ReportingType;
use CitrespBundle\Form\Security\RegistrationType;



class FrontController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
      // Si l'utilisateur est connécté on le redirige vers sa page ville
      $user = $this->getUser();
      if ($user)
      {
        $city = $user->getCity();
        $citySlug = $city->getSlug();
        return $this->redirectToRoute('city',[
            'slug' => $citySlug,
            'page' => 1
        ]);
      }


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
            'slug' => $selectCitySlug,
            'page' => 1
            ]);
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
     * @Route("/city/{slug}/{page}", requirements={"page"="\d+"}, name="city")
     * @Security("has_role('ROLE_USER')")
     */
    public function cityAction(City $city, $page)
    {        
        $user = $this->getUser();
        // Si les Villes sont différents on redirige vers la homepage
        $checkUserCity = $this->container->get('citresp.checkUserCity')->checkIsCity($user, $city);

        if ($checkUserCity['isCity'] === false)
        {
            $message = $checkUserCity['message'];
            $this->addFlash('errorCityAccess', $message);

            return $this->redirectToRoute('homepage');
        }        

        $nbReportingsPerPage = $this->container->getParameter('front_nb_reportings_per_page');

        $em = $this->getDoctrine()->getManager();

        // Google map
        $googleApi = $this->container->getParameter('google_api');
        // $markers = null ;

        // Reportings
        $reportings = $em
            ->getRepository(Reporting::class)
            ->getReportingWhereResolvedLessOneMonth($city)
            ->getQuery()
            ->getResult()
        ;

        $reportingsPerPage = $em
          ->getRepository(Reporting::class)
          ->getAllPageIn($city, $page, $nbReportingsPerPage)
        ;

        $pagination = [
            'page' => $page,
            'nbPages' => ceil(count($reportingsPerPage) / $nbReportingsPerPage),
            'routeName' => 'city',
            'routeParams' => []
        ];

        

        return $this->render('@Citresp/Front/city.html.twig', [
          'googleApi' => $googleApi,
          'city' => $city,
          'reportings' => $reportings,
          'reportingsPerPage' => $reportingsPerPage,
          'pagination' => $pagination
        ]);
    }



    /**
     * @Route("/city/{slug}/reportings/{reporting_id}", name="show_reporting")
     * @Entity("reporting", expr="repository.find(reporting_id)")
     * @Security("has_role('ROLE_USER')")
     */
    public function showReportingAction(City $city, Reporting $reporting, Request $request)
    {
        $user = $this->getUser();
        // Si les Villes sont différents on redirige vers la homepage
        $checkUserCity = $this->container->get('citresp.checkUserCity')->checkIsCity($user, $city);

        if ($checkUserCity['isCity'] === false)
        {
            $message = $checkUserCity['message'];
            $this->addFlash('errorCityAccess', $message);

            return $this->redirectToRoute('homepage');
        }

        $em = $this->getDoctrine()->getManager();

        // Google map
        $googleApi = $this->container->getParameter('google_api');

        // COMMENTS
        $comments =  $em
            ->getRepository(Comment::class)
            ->findBy(['reporting' => $reporting], ['dateCreated' => 'DESC']);

        // FORMULAIRE COMMENT
        $form = $this->createForm(CommentType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
          $comment = $form->getData();
          $user = $this->getUser();

          $this->container->get('citresp.HydrateComment')->hydrate($user, $reporting, $comment);

          $em->persist($comment);
          $em->flush();

          // Envoi d'un mail si le createur du signalement a valider Notification dans son profile
          $reportingUser = $reporting->getUser();

          if ($reportingUser->getNotification() === true)
          {
            $this->container->get('citresp.NotificationMailer')->sendNewCommentMessage($reportingUser, $reporting);
          }
          
          return $this->redirectToRoute('show_reporting',[
              'slug' => $city->getSlug(),
              'reporting_id' => $reporting->getId()
          ]);
        }

        // ACTION REPORTNG-REPORT
        $action = $request->query->get('action');
        if ($action === 'reportingReport')
        {
            $this->container->get('citresp.addOneToReportedCount')->add($reporting);

            $em->persist($reporting);
            $em->flush();
        }

        // ACTION REPORTNG-REPORT
        if ($action === 'commentReport')
        {
            $commentId = $request->query->get('id');
            $comment = $em
                ->getRepository(Comment::class)
                ->find($commentId);

            $this->container->get('citresp.addOneToReportedCount')->add($comment);

            $em->persist($comment);
            $em->flush();
        }

        return $this->render('@Citresp/Front/showReporting.html.twig', [
          'googleApi' => $googleApi,
          'city' => $city,
          'reporting' => $reporting,
          'comments' => $comments,
          'form' => $form->createView()
        ]);
    }



    /**
     * @Route("/city/{slug}/report", name="create_reporting")
     * @Security("has_role('ROLE_USER')")
     */
    public function createReportingAction(City $city, Request $request)
    {
        $user = $this->getUser();
        // Si les Villes sont différents on redirige vers la homepage
        $checkUserCity = $this->container->get('citresp.checkUserCity')->checkIsCity($user, $city);

        if ($checkUserCity['isCity'] === false)
        {
            $message = $checkUserCity['message'];
            $this->addFlash('errorCityAccess', $message);

            return $this->redirectToRoute('homepage');
        }

        $em = $this->getDoctrine()->getManager();        

        // Google map
        $googleApi = $this->container->getParameter('google_api');

        // Reportings
        $reportings = $em
            ->getRepository(Reporting::class)
            ->findBy(['city' => $city]);

        // Formulaire
        $reporting = new Reporting;
        $form = $this->createForm(RegisterReportingType::class, $reporting, ['textArea' => '']);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid())
        {
            $reporting = $form->getData();
            $autocompleteInput = $reporting->getAutocompleteInput();            
            $adressGoogle =  $this->container->get('citresp.googleMapApi')->geocodeAddress($googleApi,$autocompleteInput);              
            
        
            if ($adressGoogle === null)
            {                
                $this->addFlash('errorCreateReporting', 'Aucune adresse correspondante n\'a été trouvée dans ' . $city->getName() . ' (' . $city->getZipcode() .')');

                return $this->redirectToRoute('city',[
                    'slug' => $city->getSlug(),
                    'page' => 1
                ]); 
            }


            $city_name = $adressGoogle['city'];
            $city_str = $this->container->get('citresp.cityStrReplace')->replace($city_name);
            
            if (strtoupper($city_str) != strtoupper($city->getName()) || 
            $adressGoogle['postal_code'] != $city->getZipcode())
            {    
                $message="";

                if (strtoupper($city_str) != strtoupper($city->getName())) {
                    $message = 'Le nom ne correspond pas à la ville de ' . $city->getName() . ' (' . $city->getZipcode() .')';
                } 
                
                if ($adressGoogle['postal_code'] != $city->getZipcode()) {
                    $message = 'Le code postal ne correspond pas à la ville de ' . $city->getName() . ' (' . $city->getZipcode() .')';
                }
                $this->addFlash('errorCreateReporting', $message);

                return $this->redirectToRoute('city',[
                    'slug' => $city->getSlug(),
                    'page' => 1
                ]); 
            }
            else
            {                
                $addressReporting = ($adressGoogle['address']);
                $gpsLat = ($adressGoogle['lat']);
                $gpsLng = ($adressGoogle['lng']);

                $user = $this->getUser();
                $status = $em
                    ->getRepository(Status::class)
                    ->find(1);

                 $this->container->get('citresp.HydrateReporting')->hydrate($user, $city, $reporting, $status, $addressReporting, $gpsLat, $gpsLng); 

                $em->persist($reporting);
                $em->flush();                

                // Envoie d'un e-mail aux utilisateurs ayant un rôle Admin, Morérateur, Ville
                $users = $em
                    ->getRepository(User::class)
                    ->getUsersAreAdminByCity($city)
                    ->getQuery()
                    ->getResult()
                ;  
                
                $this->container->get('citresp.NotificationMailer')->sendNewReportingMessage($users, $city, $reporting);

                return $this->redirectToRoute('city',[
                    'slug' => $city->getSlug(),
                    'page' => 1
                    ]);
            }            
            
        }

        return $this->render('@Citresp/Front/createReporting.html.twig', [
            'googleApi' => $googleApi,
            'city' => $city,
            'reportings' => $reportings,
            'form' => $form->createView()
        ]);
    }



    /**
     * @Route("/city/{slug}/edit-report/{reporting_id}", name="edit_reporting")
     * @Entity("reporting", expr="repository.find(reporting_id)")
     * @Security("has_role('ROLE_USER')")
     */
    public function editReportingAction(City $city, Reporting $reporting, Request $request)
    {
        $user = $this->getUser();
        // Si les Villes sont différents on redirige vers la homepage
        $checkUserCity = $this->container->get('citresp.checkUserCity')->checkIsCity($user, $city);

        if ($checkUserCity['isCity'] === false)
        {
            $message = $checkUserCity['message'];
            $this->addFlash('errorCityAccess', $message);

            return $this->redirectToRoute('homepage');
        }

        $em = $this->getDoctrine()->getManager();

        // Google map
        $googleApi = $this->container->getParameter('google_api');

        // Reportings
        $reportings = $em
            ->getRepository(Reporting::class)
            ->getReportingWhereResolvedLessOneMonth($city)
            ->getQuery()
            ->getResult()
        ;

        // Formulaire
        $form = $this->createForm(RegisterReportingType::class, $reporting, ['textArea' => $reporting->getDescription()]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $image = $reporting->getImage();
            $autocompleteInput = $reporting->getAutocompleteInput();            
            $adressGoogle =  $this->container->get('citresp.googleMapApi')->geocodeAddress($googleApi,$autocompleteInput);  


            if ($image) {
                $alt = $this->container->get('citresp.createAtlContent')->altContent($reporting, $city);
                $image->setAlt($alt);
            }


            if ($adressGoogle === null)
            {                
                $this->addFlash('errorCreateReporting', 'Aucune adresse correspondante n\'a été trouvée dans ' . $city->getName() . ' (' . $city->getZipcode() .')');

                return $this->redirectToRoute('city',[
                    'slug' => $city->getSlug(),
                    'page' => 1
                ]); 
            }


            $city_name = $adressGoogle['city'];
            $city_str = $this->container->get('citresp.cityStrReplace')->replace($city_name);

            
            if (strtoupper($city_str) != strtoupper($city->getName()) || 
            $adressGoogle['postal_code'] != $city->getZipcode())
            {    
                $message="";

                if (strtoupper($city_str) != strtoupper($city->getName())) {
                    $message = 'Le nom ne correspond pas à la ville de ' . $city->getName() . ' (' . $city->getZipcode() .')';
                } 
                
                if ($adressGoogle['postal_code'] != $city->getZipcode()) {
                    $message = 'Le code postal ne correspond pas à la ville de ' . $city->getName() . ' (' . $city->getZipcode() .')';
                }
                $this->addFlash('errorCreateReporting', $message);

                return $this->redirectToRoute('city',[
                    'slug' => $city->getSlug(),
                    'page' => 1
                ]); 
            }
            else
            {                
                $addressReporting = ($adressGoogle['address']);
                $gpsLat = ($adressGoogle['lat']);
                $gpsLng = ($adressGoogle['lng']);
            }

            $user = $this->getUser();
            $status = $reporting->getStatus();

            $this->container->get('citresp.HydrateReporting')->hydrate($user, $city, $reporting, $status, $addressReporting, $gpsLat, $gpsLng);



          $em->flush();

          return $this->redirectToRoute('city',[
              'slug' => $city->getSlug(),
              'page' => 1
              ]);
        }


        return $this->render('@Citresp/Front/editReporting.html.twig', [
            'googleApi' => $googleApi,
            'city' => $city,
            'reporting' => $reporting,
            'reportings' => $reportings,
            'form' => $form->createView()
        ]);
    }



    /**
     * @Route("/mentions-legales", name="mentions_legales")
     */
    public function mentionsLegalesAction()
    {
      return $this->render('@Citresp/Front/mentionsLegales.html.twig');
    }

}
