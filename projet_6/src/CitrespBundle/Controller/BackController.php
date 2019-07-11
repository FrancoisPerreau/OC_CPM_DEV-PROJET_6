<?php

namespace CitrespBundle\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use CitrespBundle\Entity\Category;
use CitrespBundle\Entity\City;
use CitrespBundle\Entity\Comment;
use CitrespBundle\Entity\Reporting;
use CitrespBundle\Entity\User;

use CitrespBundle\Form\EditReportingStatusType;

// use Symfony\Component\Security\Core\Exception\AccessDeniedException;


class BackController extends Controller
{
    /**
     * @Route("/admin/{slug}/{page}", requirements={"page"="\d+"}, name="security_admin")
     * @Security("has_role('ROLE_MODERATOR')")
     */
    public function adminAction(City $city, $page, Request $request)
    {
        // Si les Villes sont différents on redirige vers la homepage
        $user = $this->getUser();
        if ($user->getCity() != $city)
        {
            $this->addFlash('errorCityAccess', 'Votre compte n\'est pas pour la ville de ' . $city->getName());

            return $this->redirectToRoute('homepage');
        }

        $em = $this->getDoctrine()->getManager();

        // Pagination
        $nbReportingsPerPage = $this->container->getParameter('back_nb_reportings_per_page');

        $reportingsPerPage = $em
          ->getRepository(Reporting::class)
          ->getAllPageInWhereNotModerate($city, $page, $nbReportingsPerPage)
        ;

        $pagination = [
            'page' => $page,
            'nbPages' => ceil(count($reportingsPerPage) / $nbReportingsPerPage),
            'routeName' => 'security_admin',
            'routeParams' => []
        ];

        // dump($reportingsPerPage);
        // die;

        // Google map
        $googleApi = $this->container->getParameter('google_api');

        // Reportings
        $emReportings = $em->getRepository(Reporting::class);

        $reportings = $emReportings
            ->getReportingByReportedWhereNotModerate($city)
            ->getQuery()
            ->getResult()
            ;


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
                'slug' => $city->getSlug(),
                'page' => $page
            ]);
        }


        return $this->render('@Citresp/Back/adminHome.html.twig',[
            'googleApi' => $googleApi,
            'city' => $city,
            'reportings' => $reportings,

            'reportedReportingsNb' => $reportedReportingsNb,
            'reportedCommentsNb' => $reportedCommentsNb,
            'reportingsList' => $reportingsPerPage,
            'pagination' => $pagination
        ]);
    }


    /**
     * @Route("/admin-city/{slug}/{page}", requirements={"page"="\d+"}, name="security_city")
     * @Security("has_role('ROLE_CITY')")
     */
    public function cityAction(City $city, $page, Request $request)
    {
        // Si les Villes sont différents on redirige vers la homepage
        $user = $this->getUser();
        if ($user->getCity() != $city)
        {
            $this->addFlash('errorCityAccess', 'Votre compte n\'est pas pour la ville de ' . $city->getName());

            return $this->redirectToRoute('homepage');
        }

        $em = $this->getDoctrine()->getManager();

        // Pagination
        $nbReportingsPerPage = $this->container->getParameter('back_nb_reportings_per_page');

        $reportingsPerPage = $em
          ->getRepository(Reporting::class)
          ->getAllPageInWhereNotModerate($city, $page, $nbReportingsPerPage)
        ;

        $pagination = [
            'page' => $page,
            'nbPages' => ceil(count($reportingsPerPage) / $nbReportingsPerPage),
            'routeName' => 'security_city',
            'routeParams' => []
        ];


        // Google map
        $googleApi = $this->container->getParameter('google_api');

        // Reportings
        $emReportings = $em->getRepository(Reporting::class);

        $reportings = $emReportings
            ->findBy(['city' => $city], ['dateCreated' => 'DESC']);




        return $this->render('@Citresp/Back/adminCity.html.twig',[
            'googleApi' => $googleApi,
            'city' => $city,
            'reportings' => $reportings,

            'reportingsList' => $reportingsPerPage,
            'pagination' => $pagination
        ]);
    }




    /**
     * @Route("/admin/{slug}/Reportings/moderate/{page}", requirements={"page"="\d+"}, name="security_admin_show_moderate")
     * @Security("has_role('ROLE_MODERATOR')")
     */
    public function adminShowModerateAction(City $city, $page, Request $request)
    {
        // Si les Villes sont différents on redirige vers la homepage
        $user = $this->getUser();
        if ($user->getCity() != $city)
        {
            $this->addFlash('errorCityAccess', 'Votre compte n\'est pas pour la ville de ' . $city->getName());

            return $this->redirectToRoute('homepage');
        }

        $em = $this->getDoctrine()->getManager();

        // Pagination
        $nbReportingsPerPage = $this->container->getParameter('back_nb_reportings_per_page');

        $reportingsPerPage = $em
          ->getRepository(Reporting::class)
          ->getAllPageInWhereModerate($city, $page, $nbReportingsPerPage)
        ;

        $pagination = [
            'page' => $page,
            'nbPages' => ceil(count($reportingsPerPage) / $nbReportingsPerPage),
            'routeName' => 'security_admin_show_moderate',
            'routeParams' => []
        ];


        // Google map
        $googleApi = $this->container->getParameter('google_api');



        // Reportings
        $emReportings = $em->getRepository(Reporting::class);

        $reportings = $emReportings
            ->getReportingModerate($city)
            ->getQuery()
            ->getResult()
            ;

        $reportedReportingsNb = $emReportings->getReportingByReportedNbWhereNotModerate($city);


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
                'slug' => $city->getSlug(),
                'page' => $page
            ]);
        }



        return $this->render('@Citresp/Back/adminHome.html.twig',[
            'googleApi' => $googleApi,
            'city' => $city,
            'reportings' => $reportings,

            'reportedReportingsNb' => $reportedReportingsNb,
            'reportedCommentsNb' => $reportedCommentsNb,
            'reportingsList' => $reportingsPerPage,
            'pagination' => $pagination
        ]);

    }



    /**
     * @Route("/admin/{slug}/Reportings/reported/{page}", requirements={"page"="\d+"}, name="security_admin_show_reported")
     * @Security("has_role('ROLE_MODERATOR')")
     */
    public function adminShowReportedAction(City $city, $page, Request $request)
    {
        // Si les Villes sont différents on redirige vers la homepage
        $user = $this->getUser();
        if ($user->getCity() != $city)
        {
            $this->addFlash('errorCityAccess', 'Votre compte n\'est pas pour la ville de ' . $city->getName());

            return $this->redirectToRoute('homepage');
        }

        $em = $this->getDoctrine()->getManager();

        // Pagination
        $nbReportingsPerPage = $this->container->getParameter('back_nb_reportings_per_page');

        $reportingsPerPage = $em
          ->getRepository(Reporting::class)
          ->getAllPageInWhereReportedAndNotModerate($city, $page, $nbReportingsPerPage)
        ;

        $pagination = [
            'page' => $page,
            'nbPages' => ceil(count($reportingsPerPage) / $nbReportingsPerPage),
            'routeName' => 'security_admin_show_reported',
            'routeParams' => []
        ];


        // Google map
        $googleApi = $this->container->getParameter('google_api');

        // Reportings
        $emReportings = $em->getRepository(Reporting::class);

        $reportings = $emReportings
            ->getReportingByReportedWhereNotModerate($city)
            ->getQuery()
            ->getResult()
        ;

        $reportedReportingsNb = $emReportings->getReportingByReportedNbWhereNotModerate($city);


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

            return $this->redirectToRoute('security_admin_show_reported',[
                'slug' => $city->getSlug(),
                'page' => $page
            ]);
        }


        return $this->render('@Citresp/Back/adminHome.html.twig',[
            'googleApi' => $googleApi,
            'city' => $city,
            'reportings' => $reportings,

            'reportedReportingsNb' => $reportedReportingsNb,
            'reportedCommentsNb' => $reportedCommentsNb,
            'reportingsList' => $reportingsPerPage,
            'pagination' => $pagination
        ]);

    }




    /**
     * @Route("/admin/{slug}/edit-report/{reporting_id}", name="security_admin_edit_reporting_status")
     * @Entity("reporting", expr="repository.find(reporting_id)")
     * @IsGranted({"ROLE_MODERATOR", "ROLE_CITY"})
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
            $reporting = $form->getData();

            $reportingStatus = $reporting->getStatus();

            
            if ($reportingStatus->getId() === 4)
            {
                $reporting->setResolved(true);
                $reporting->setDateResolved(new \Datetime('now'));
            }
            else
            {
                $reporting->setResolved(false);
                $reporting->setDateResolved(null);
            }

            
            $em->flush();


            if ($user->hasRole('ROLE_CITY'))
            {
                return $this->redirectToRoute('security_city',[
                    'slug' => $city->getSlug(),
                    'page' => 1
                ]);
            }
            else
            {
                return $this->redirectToRoute('security_admin',[
                    'slug' => $city->getSlug(),
                    'page' => 1
                ]);
            }
        }


        return $this->render('@Citresp/Back/adminEditStatus.html.twig',[
            'googleApi' => $googleApi,
            'city' => $city,
            'reporting' => $reporting,

            'form' => $form->createView()
        ]);

    }



    /**
     * @Route("/admin/{slug}/comments/moderate/{page}", requirements={"page"="\d+"}, name="security_admin_show_moderate_comments")
     * @Security("has_role('ROLE_MODERATOR')")
     */
    public function adminShowModerateCommentsAction(City $city, $page, Request $request)
    {
        // Si les Villes sont différents on redirige vers la homepage
        $user = $this->getUser();
        if ($user->getCity() != $city)
        {
            $this->addFlash('errorCityAccess', 'Votre compte n\'est pas pour la ville de ' . $city->getName());

            return $this->redirectToRoute('homepage');
        }

        $em = $this->getDoctrine()->getManager();

        // Pagination
        $nbCommentsPerPage = $this->container->getParameter('back_nb_comments_per_page');

        $commentsPerPage = $em
          ->getRepository(Comment::class)
          ->getAllPageInWhereModerate($city, $page, $nbCommentsPerPage)
        ;

        $pagination = [
            'page' => $page,
            'nbPages' => ceil(count($commentsPerPage) / $nbCommentsPerPage),
            'routeName' => 'security_admin_show_moderate_comments',
            'routeParams' => []
        ];

        // Google map
        $googleApi = $this->container->getParameter('google_api');


        // Reportings
        $emReportings = $em->getRepository(Reporting::class);

        $reportings = $emReportings
            ->getReportingWhereResolvedLessOneMonth($city)
            ->getQuery()
            ->getResult()
        ;


        $reportedReportingsNb = $emReportings->getReportingByReportedNbWhereNotModerate($city);


        // Comments
        $emComments = $em->getRepository(Comment::class);


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
                'slug' => $city->getSlug(),
                'page' => $page
            ]);
        }


        return $this->render('@Citresp/Back/adminShowComments.html.twig',[
            'googleApi' => $googleApi,
            'city' => $city,
            'reportings' => $reportings,

            'reportingsList' => $reportings,

            'reportedReportingsNb' => $reportedReportingsNb,
            'reportedCommentsNb' => $reportedCommentsNb,
            'commentsList' => $commentsPerPage,
            'pagination' => $pagination
        ]);

    }



    /**
     * @Route("/admin/{slug}/comments/reported/{page}", requirements={"page"="\d+"}, name="security_admin_show_reported_comments")
     * @Security("has_role('ROLE_MODERATOR')")
     */
    public function adminShowReportedCommentsAction(City $city, $page, Request $request)
    {
        // Si les Villes sont différents on redirige vers la homepage
        $user = $this->getUser();
        if ($user->getCity() != $city)
        {
            $this->addFlash('errorCityAccess', 'Votre compte n\'est pas pour la ville de ' . $city->getName());

            return $this->redirectToRoute('homepage');
        }

        $em = $this->getDoctrine()->getManager();

        // Pagination
        $nbCommentsPerPage = $this->container->getParameter('back_nb_comments_per_page');

        $commentsPerPage = $em
          ->getRepository(Comment::class)
          ->getAllPageInWhereReportedAndNotModerate($city, $page, $nbCommentsPerPage)
        ;

        $pagination = [
            'page' => $page,
            'nbPages' => ceil(count($commentsPerPage) / $nbCommentsPerPage),
            'routeName' => 'security_admin_show_reported_comments',
            'routeParams' => []
        ];


        // Google map
        $googleApi = $this->container->getParameter('google_api');



        // Reportings
        $emReportings = $em->getRepository(Reporting::class);

        $reportings = $emReportings
            ->getReportingWhereResolvedLessOneMonth($city)
            ->getQuery()
            ->getResult()
        ;


        $reportedReportingsNb = $emReportings->getReportingByReportedNbWhereNotModerate($city);




        // Comments
        $emComments = $em->getRepository(Comment::class);

        // $reportedCommentsNotModerate = $emComments->getCommentByReportedWhereNotModerate($city);
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

            return $this->redirectToRoute('security_admin_show_reported_comments',[
                'slug' => $city->getSlug(),
                'page' => $page
            ]);
        }



        return $this->render('@Citresp/Back/adminShowComments.html.twig',[
            'googleApi' => $googleApi,
            'city' => $city,
            'reportings' => $reportings,

            'reportingsList' => $reportings,

            'reportedReportingsNb' => $reportedReportingsNb,
            'reportedCommentsNb' => $reportedCommentsNb,
            'commentsList' => $commentsPerPage,
            'pagination' => $pagination
        ]);

    }



    /**
     * @Route("/admin/{slug}/users/{page}", requirements={"page"="\d+"}, name="security_admin_show_users")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function adminShowUsersAction(City $city, $page, Request $request)
    {
        // Si les Villes sont différents on redirige vers la homepage
        $user = $this->getUser();
        if ($user->getCity() != $city)
        {
            $this->addFlash('errorCityAccess', 'Votre compte n\'est pas pour la ville de ' . $city->getName());

            return $this->redirectToRoute('homepage');
        }

        $em = $this->getDoctrine()->getManager();

        // Pagination
        $nbUserPerPage = $this->container->getParameter('back_nb_users_per_page');


        // Users
        // $userManager = $this->get('fos_user.user_manager');
        // $users = $userManager->findUsers($city);
        $usersPerPage = $em
          ->getRepository(User::class)
          ->getAllPageInWhereUsersNotAdminByCity($city, $page, $nbUserPerPage)
        ;


        $pagination = [
            'page' => $page,
            'nbPages' => ceil(count($usersPerPage) / $nbUserPerPage),
            'routeName' => 'security_admin_show_users',
            'routeParams' => []
        ];

        // Google map
        $googleApi = $this->container->getParameter('google_api');


        // Reportings
        $emReportings = $em->getRepository(Reporting::class);

        $reportings = $emReportings
            ->getReportingWhereResolvedLessOneMonth($city)
            ->getQuery()
            ->getResult()
        ;


        return $this->render('@Citresp/Back/adminSwowUsers.html.twig',[
            'googleApi' => $googleApi,
            'city' => $city,
            'reportings' => $reportings,
            'users' => $usersPerPage,

            'pagination' => $pagination
        ]);

    }



    /**
     * @Route("/admin/{slug}/users-admin/{page}", requirements={"page"="\d+"}, name="security_admin_show_admin_user")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function adminShowAdminUsersAction(City $city, $page, Request $request)
    {
        // Si les Villes sont différents on redirige vers la homepage
        $user = $this->getUser();
        if ($user->getCity() != $city)
        {
            $this->addFlash('errorCityAccess', 'Votre compte n\'est pas pour la ville de ' . $city->getName());

            return $this->redirectToRoute('homepage');
        }

        $em = $this->getDoctrine()->getManager();

        // Pagination
        $nbUserPerPage = $this->container->getParameter('back_nb_users_per_page');


        // Users
        $usersPerPage = $em
          ->getRepository(User::class)
          ->getAllPageInWhereUsersAreAdminByCity($city, $page, $nbUserPerPage)
        ;


        $pagination = [
            'page' => $page,
            'nbPages' => ceil(count($usersPerPage) / $nbUserPerPage),
            'routeName' => 'security_admin_show_users',
            'routeParams' => []
        ];

        // Google map
        $googleApi = $this->container->getParameter('google_api');


        // Reportings
        $emReportings = $em->getRepository(Reporting::class);

        $reportings = $emReportings
            ->getReportingWhereResolvedLessOneMonth($city)
            ->getQuery()
            ->getResult()
        ;


        return $this->render('@Citresp/Back/adminSwowUsers.html.twig',[
            'googleApi' => $googleApi,
            'city' => $city,
            'reportings' => $reportings,
            'users' => $usersPerPage,

            'pagination' => $pagination
        ]);

    }


}
