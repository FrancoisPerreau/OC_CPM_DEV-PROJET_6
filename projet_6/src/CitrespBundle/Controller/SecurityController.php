<?php

namespace CitrespBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use CitrespBundle\Entity\BaseCities;
use CitrespBundle\Entity\City;
use CitrespBundle\Entity\Comment;
use CitrespBundle\Entity\Reporting;
use CitrespBundle\Entity\User;


use CitrespBundle\Form\BaseCitiesChoiceType;
use CitrespBundle\Form\Security\RegistrationByCityType;
use CitrespBundle\Form\Security\UserEditRoleType;


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



    /**
     * @Route("/admin/{slug}/user/edit", name="security_admin_edit_user_role")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function adminEditUserRoleAction(City $city, User $userSelected, Request $request)
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

        $sectedUserId = $request->query->get('userId');
        $selectedUser = $em
            ->getRepository(User::class)
            ->find($sectedUserId)
            ;


        $form = $this->createForm(UserEditRoleType::class, $selectedUser);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $user = $form->getData();

            // dump($user);
            // die;

            $em->flush();

            return $this->redirectToRoute('security_admin_show_admin_user', [
                'slug' => $city->getSlug(),
                'page' => 1
            ]);
        }



        return $this->render('@Citresp/Back/adminEditUserRole.html.twig',[
            'user' => $user,
            'form' => $form->createView()
        ]);
    }



      /**
       * @Route("/city/{slug}/remove-user", name="remove_user")
       * @Security("has_role('ROLE_USER')")
       */
      public function removeUserAction(City $city, Request $request)
      {
          $em = $this->getDoctrine()->getManager();

          $userGivenId = $request->query->get('userId');
          $userGiven = $em
              ->getRepository(User::class)
              ->find($userGivenId);


          // Si les Villes sont différents on redirige vers la homepage
          $user = $this->getUser();
          
          $checkUserCity = $this->container->get('citresp.checkUserCity')->checkIsCity($user, $city);

          if ($checkUserCity['isCity'] === false)
          {
              $message = $checkUserCity['message'];
              $this->addFlash('errorCityAccess', $message);

              return $this->redirectToRoute('homepage');
          }

          $isAdmin = $this->get('security.authorization_checker')->isGranted('ROLE_ADMIN');


          // Si les utilisateurs sont différents et
          // que l'utilisateur courant n'a pas le rôle admin
          // on redirige vers la page city
          if ($userGiven->getId() != $user->getId() && $isAdmin === false)
          {
              $this->addFlash('errorSelectedUser', 'Vous ne pouvez pas suprimer ce compte');

              return $this->redirectToRoute('city', [
                  'slug' => $city->getSlug(),
                  'page' => 1
              ]);
          }


          $action = $request->query->get('action');

          if ($action === 'remove' && !is_null($userGiven))
          {
              // Avant de suprimer l'utilisateur
              // verifier que s'il à le rôle ADMIN
              // il existe au moins un autre Admin pour cette ville
             if($userGiven->hasRole('ROLE_ADMIN'))
             {
                 $nbAdmin = $em
                    ->getRepository(User::class)
                    ->countUserOnlyAdminByCity($city)
                 ;

                 if ($nbAdmin < 2)
                 {
                     $this->addFlash('ErrorRemoveUser', 'Au moins un autre utilisateur doit avoir le rôle ADMINISTRATEUR pour pouvoir supprimer ce compte');

                     return $this->redirectToRoute('security_admin', [
                         'slug' => $city->getSlug(),
                         'page' => 1
                     ]);
                 }

             }

             $comments = $em
                ->getRepository(Comment::class)
                ->findBy(['user' => $userGiven])
             ;

             foreach ($comments as $comment)
             {
                 $comment->setUser(null);
             }

             $reportings = $em
                ->getRepository(Reporting::class)
                ->findBy(['user' => $userGiven])
             ;

             foreach ($reportings as $reporting)
             {
                 $reporting->setUser(null);
             }



             $userManager = $this->get('fos_user.user_manager');
             $userManager->deleteUser($userGiven);


             $this->addFlash('SuccessRemoveUser', 'Le compte à bien été supprimé.');

             if ($isAdmin === true)
             {
                 return $this->redirectToRoute('security_admin', [
                     'slug' => $city->getSlug(),
                     'page' => 1
                 ]);
             }
             else
             {
                 return $this->redirectToRoute('homepage');
             }

          }


          return $this->render('@Citresp/Security/removeUser.html.twig', [
            'user' => $userGiven
          ]);
      }

}
