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

            
            if (strtoupper($adressGoogle['city']) != strtoupper($city->getName() ))
            {
                $this->addFlash('errorCreateReporting', 'Cette adresse ne correspond pas à la ville de ' . $city->getName() . ' (' . $city->getZipcode() .')');

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
            ->findBy(['city' => $city]);

        // Formulaire
        $form = $this->createForm(RegisterReportingType::class, $reporting, ['textArea' => $reporting->getDescription()]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $image = $reporting->getImage();

            if ($image) {
                $alt = $this->container->get('citresp.createAtlContent')->altContent($reporting, $city);
                $image->setAlt($alt);
            }

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




    // ***********************************
    // ***********************************
    // À SUPPRIMER EN PROD !!!
    // ***********************************
    // ***********************************

    
    // TESTS TEMPLATES MAILS
    // ************************************

    /**
     * @Route("/mail-template", name="mail_template")
     */
    public function viewMailTemplateAction()
    {
        $reporting = $this->getDoctrine()->getRepository('CitrespBundle:Reporting')->find(6);
        $reportingUser = $reporting->getUser();

        $this->container->get('citresp.NotificationMailer')->sendNewReportingModerate($reportingUser, $reporting);

        return $this->render('@Citresp/Emails/notificationNewModerateReporting.html.twig', [
            'reporting' => $reporting,
            'user' => $reportingUser
        ]);

    }




    /**
     * @Route("/mail-comment-moderate", name="mail_comment_moderate")
     */
    public function viewMailCommentModerateAction()
    {
        $comment = $this->getDoctrine()->getRepository('CitrespBundle:Comment')->find(10);
        $commentReporting = $comment->getReporting();
        $commentUser = $comment->getUser();

        // dump($comment);
        // dump($reporting);
        // dump($user);
        // die;

        $this->container->get('citresp.NotificationMailer')->sendNewCommentModerate($comment, $commentUser, $commentReporting);

        return $this->render('@Citresp/Emails/notificationNewModerateComment.html.twig', [
            'comment' => $comment,
            'reporting' => $commentReporting,
            'user' => $commentUser
        ]);

    }


}
