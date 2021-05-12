<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Users;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(): Response
    {
        return $this->render('home/index.html.twig');
    }

    /**
     * @Route("/{action<(login|signup)>}",name="auth")
     */
    public function signup($action, AuthenticationUtils $authenticationUtils)
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('home');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        $form = $this->createform(usertype::class);
        return $this->render('home/login.html.twig', [
            'signup' => ($action == 'signup'),
            'form' => $form->createView(),
            'last_username' => $lastUsername,
            'error' => $error
        ]);
    }

    /**
     * @Route("/register", name="register", methods={"POST"})
     */
    public function register(Request $request, UserPasswordEncoderInterface $encoder, EntityManagerInterface $entity)
    {
        $user = new user();
        $form = $this->createform(usertype::class, $user);
        $form->handlerequest($request);
        if ($form->issubmitted() && $form->isvalid()) {
            $user->setpassword($encoder->encodepassword($user, $user->getpassword()));
            $entity->persist($user);
            $entity->flush();
            return $this->redirecttoroute("home");
        }
        return $this->render('home/login.html.twig', [
            'signup' => 'signup',
            'form' => $form->createView(),
            'last_username' => "",
            'error' => null
        ]);
    }
}
