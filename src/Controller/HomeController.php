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
    public function signup(Request $request, UserPasswordEncoderInterface $encoder, EntityManagerInterface $entity, $action)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword($encoder->encodePassword($user, $user->getPassword()));
            $entity->persist($user);
            $entity->flush();
            return $this->redirectToRoute("home");
        }
        return $this->render('home/login.html.twig', [
            'signup' => ($action == 'signup'),
            'form' => $form->createView()
        ]);
    }
}
