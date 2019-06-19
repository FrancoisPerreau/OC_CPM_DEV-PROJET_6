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

        // Google map
        $googleApi = $this->container->getParameter('google_api');
        // $markers = null ;

        // Reportings
        $reportings = $em
            ->getRepository(Reporting::class)
            ->findBy(['city' => $city], ['dateCreated' => 'DESC']);

        // // Si les slug sont différents on redirige vers la homepage
        // if ($user->getCity()->getSlug() != $city->getSlug())
        // {
        //     return $this->redirectToRoute('homepage');
        // }

        return $this->render('@Citresp/Front/city.html.twig', [
          'googleApi' => $googleApi,
          'city' => $city,
          'reportings' => $reportings
        ]);
    }



    /**
     * @Route("/city/{slug}/reportings/{reporting_id}", name="show_reporting")
     * @Entity("reporting", expr="repository.find(reporting_id)")
     * @Security("has_role('ROLE_USER')")
     */
    public function showReportingAction(City $city, Reporting $reporting, Request $request)
    {
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

          // $comment->setUser($user);
          // $comment->setReporting($reporting);

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
            $reportedCount = $reporting->getReportedCount() + 1;
            $reporting->setReportedCount($reportedCount);

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

            $reportedCount = $comment->getReportedCount() + 1;
            $comment->setReportedCount($reportedCount);

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
          $user = $this->getUser();

          $this->container->get('citresp.HydrateReporting')->hydrate($user, $city, $reporting);

          $em->persist($reporting);
          $em->flush();

          return $this->redirectToRoute('city',[
              'slug' => $city->getSlug()]);
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
              'slug' => $city->getSlug()]);
        }


        return $this->render('@Citresp/Front/editReporting.html.twig', [
            'googleApi' => $googleApi,
            'city' => $city,
            'reporting' => $reporting,
            'reportings' => $reportings,
            'form' => $form->createView()

        ]);
    }


}
