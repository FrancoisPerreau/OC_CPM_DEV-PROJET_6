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

use CitrespBundle\Form\EditReportingStatusType;

// use Symfony\Component\Security\Core\Exception\AccessDeniedException;


class BackController extends Controller
{
    /**
     * @Route("/admin/{slug}", name="security_admin")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function adminAction(City $city, Request $request)
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

        // Reportings
        $emReportings = $em->getRepository(Reporting::class);

        $reportings = $emReportings
            ->findBy(['city' => $city], ['dateCreated' => 'DESC']);


        $reportedReportingsNb = $emReportings->getReportingByReportedNbWhereNotModerate($city);

        $reportingsNotModerate = $emReportings->getReportingNotModerate($city);


        // Comments
        $emComments = $em->getRepository(Comment::class);

        // $reportedComments = $emComments->getCommentByReported($city);
        $reportedCommentsNb = $emComments->getCommentByReportedNbWhereModerate($city);


        // ACTION REPORTNG-MODERATE
        $action = $request->query->get('action');
        $reportingId = $request->query->get('reportingId');


        if ($action === 'reportingModerate')
        {
            $reportingId = $request->query->get('reportingId');
            $reporting = $emReportings->find($reportingId);
            $reportingModerate = $reporting->getModerate();


            if ($reportingModerate === false)
            {
                $reporting->setModerate(true);
            }
            else {
                $reporting->setModerate(false);
            }

            $em->flush();

            return $this->redirectToRoute('security_admin',[
                'slug' => $city->getSlug()
            ]);
        }


        return $this->render('@Citresp/Back/adminHome.html.twig',[
            'googleApi' => $googleApi,
            'city' => $city,
            'reportings' => $reportings,

            'reportedReportingsNb' => $reportedReportingsNb,
            'reportedCommentsNb' => $reportedCommentsNb,
            'reportingsList' => $reportingsNotModerate,

            // 'form' => $form->createView()
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


        $reportingsList = $emReportings->getReportingModerate($city);



        return $this->render('@Citresp/Back/adminModerator.html.twig',[
            'googleApi' => $googleApi,
            'city' => $city,
            'reportings' => $reportings,

            'reportedReportingsNb' => $reportedReportingsNb,
            'reportedCommentsNb' => $reportedCommentsNb,
            'reportingsList' => $reportingsNotModerate

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
            'reportings' => $reportings,

            'reportedReportingsNb' => $reportedReportingsNb,
            'reportedCommentsNb' => $reportedCommentsNb,
            'reportingsList' => $reportingsNotModerate
        ]);
    }




    /**
     * @Route("/admin/{slug}/Reportings/moderate", name="security_admin_show_moderate")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function adminShowModerateAction(City $city, Request $request)
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



        // Reportings
        $emReportings = $em->getRepository(Reporting::class);

        $reportings = $emReportings
            ->findBy(['city' => $city], ['dateCreated' => 'DESC']);


        // $reportedReportings = $emReportings->getReportingByReported($city);
        $reportedReportingsNb = $emReportings->getReportingByReportedNbWhereNotModerate($city);

        $reportingsModerate = $emReportings->getReportingModerate($city);


        // Comments
        $emComments = $em->getRepository(Comment::class);

        $reportedComments = $emComments->getCommentByReported($city);
        $reportedCommentsNb = $emComments->getCommentByReportedNbWhereModerate($city);


        // ACTION REPORTNG-MODERATE
        $action = $request->query->get('action');
        $reportingId = $request->query->get('reportingId');

        if ($action === 'reportingModerate')
        {
            $reportingId = $request->query->get('reportingId');
            $reporting = $emReportings->find($reportingId);
            $reportingModerate = $reporting->getModerate();


            if ($reportingModerate === false)
            {
                $reporting->setModerate(true);
            }
            else {
                $reporting->setModerate(false);
            }

            $em->flush();

            return $this->redirectToRoute('security_admin_show_moderate',[
                'slug' => $city->getSlug()
            ]);
        }



        return $this->render('@Citresp/Back/adminHome.html.twig',[
            'googleApi' => $googleApi,
            'city' => $city,
            'reportings' => $reportings,

            'reportedReportingsNb' => $reportedReportingsNb,
            'reportedCommentsNb' => $reportedCommentsNb,
            'reportingsList' => $reportingsModerate
        ]);

    }



    /**
     * @Route("/admin/{slug}/Reportings/reported", name="security_admin_show_reported")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function adminShowReportedAction(City $city, Request $request)
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



        // Reportings
        $emReportings = $em->getRepository(Reporting::class);

        $reportings = $emReportings
            ->findBy(['city' => $city], ['dateCreated' => 'DESC']);



        $reportedReportingsNb = $emReportings->getReportingByReportedNbWhereNotModerate($city);
        $reportedReportingsNotModerate = $emReportings->getReportingByReportedWhereNotModerate($city);



        // Comments
        $emComments = $em->getRepository(Comment::class);

        $reportedComments = $emComments->getCommentByReported($city);
        $reportedCommentsNb = $emComments->getCommentByReportedNbWhereModerate($city);


        // ACTION REPORTNG-MODERATE
        $action = $request->query->get('action');
        $reportingId = $request->query->get('reportingId');

        if ($action === 'reportingModerate')
        {
            $reportingId = $request->query->get('reportingId');
            $reporting = $emReportings->find($reportingId);
            $reportingModerate = $reporting->getModerate();


            if ($reportingModerate === false)
            {
                $reporting->setModerate(true);
            }
            else {
                $reporting->setModerate(false);
            }

            $em->flush();

            return $this->redirectToRoute('security_admin_show_moderate',[
                'slug' => $city->getSlug()
            ]);
        }



        return $this->render('@Citresp/Back/adminHome.html.twig',[
            'googleApi' => $googleApi,
            'city' => $city,
            'reportings' => $reportings,

            'reportedReportingsNb' => $reportedReportingsNb,
            'reportedCommentsNb' => $reportedCommentsNb,
            'reportingsList' => $reportedReportingsNotModerate
        ]);

    }




    /**
     * @Route("/admin/{slug}/edit-report/{reporting_id}", name="security_admin_edit_reporting_status")
     * @Entity("reporting", expr="repository.find(reporting_id)")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function adminEditReportingStatusAction(City $city, Reporting $reporting, Request $request)
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


        // FORMULAIRE STATUS
        $form = $this->createForm(EditReportingStatusType::class, $reporting);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $em->flush();

            return $this->redirectToRoute('security_admin',[
                'slug' => $city->getSlug()]);
        }


        return $this->render('@Citresp/Back/adminEditStatus.html.twig',[
            'googleApi' => $googleApi,
            'city' => $city,
            'reporting' => $reporting,

            'form' => $form->createView()
        ]);

    }



    /**
     * @Route("/admin/{slug}/comments/moderate", name="security_admin_show_moderate_comments")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function adminShowModerateCommentsAction(City $city, Request $request)
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



        // Reportings
        $emReportings = $em->getRepository(Reporting::class);

        $reportings = $emReportings
            ->findBy(['city' => $city], ['dateCreated' => 'DESC']);


        $reportedReportingsNb = $emReportings->getReportingByReportedNbWhereNotModerate($city);




        // Comments
        $emComments = $em->getRepository(Comment::class);

        $reportedCommentsModerate = $emComments->getCommentByReportedWhereModerate($city);
        $reportedCommentsNb = $emComments->getCommentByReportedNbWhereNotModerate($city);


        // ACTION COMMENT-MODERATE
        $action = $request->query->get('action');

        if ($action === 'commentModerate')
        {
            $commentId = $request->query->get('commentId');
            $comment = $emComments->find($commentId);
            $commentModerate = $comment->getModerate();


            if ($commentModerate === false)
            {
                $comment->setModerate(true);
            }
            else {
                $comment->setModerate(false);
            }

            $em->flush();

            return $this->redirectToRoute('security_admin_show_moderate_comments',[
                'slug' => $city->getSlug()
            ]);
        }



        return $this->render('@Citresp/Back/adminShowComments.html.twig',[
            'googleApi' => $googleApi,
            'city' => $city,
            'reportings' => $reportings,

            'reportingsList' => $reportings,

            'reportedReportingsNb' => $reportedReportingsNb,
            'reportedCommentsNb' => $reportedCommentsNb,
            'commentsList' => $reportedCommentsModerate
        ]);

    }



    /**
     * @Route("/admin/{slug}/comments/reported", name="security_admin_show_reported_comments")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function adminShowReportedCommentsAction(City $city, Request $request)
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



        // Reportings
        $emReportings = $em->getRepository(Reporting::class);

        $reportings = $emReportings
            ->findBy(['city' => $city], ['dateCreated' => 'DESC']);


        $reportedReportingsNb = $emReportings->getReportingByReportedNbWhereNotModerate($city);




        // Comments
        $emComments = $em->getRepository(Comment::class);

        $reportedCommentsNotModerate = $emComments->getCommentByReportedWhereNotModerate($city);
        $reportedCommentsNb = $emComments->getCommentByReportedNbWhereNotModerate($city);


        // ACTION COMMENT-MODERATE
        $action = $request->query->get('action');

        if ($action === 'commentModerate')
        {
            $commentId = $request->query->get('commentId');
            $comment = $emComments->find($commentId);
            $commentModerate = $comment->getModerate();


            if ($commentModerate === false)
            {
                $comment->setModerate(true);
            }
            else {
                $comment->setModerate(false);
            }

            $em->flush();

            return $this->redirectToRoute('security_admin_show_moderate_comments',[
                'slug' => $city->getSlug()
            ]);
        }



        return $this->render('@Citresp/Back/adminShowComments.html.twig',[
            'googleApi' => $googleApi,
            'city' => $city,
            'reportings' => $reportings,

            'reportingsList' => $reportings,

            'reportedReportingsNb' => $reportedReportingsNb,
            'reportedCommentsNb' => $reportedCommentsNb,
            'commentsList' => $reportedCommentsNotModerate
        ]);

    }


}
