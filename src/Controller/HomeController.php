<?php

namespace App\Controller;

use App\Entity\Users;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
     * @Route("/signup",name="signup")
     */
    public function signup()
    {
        return $this->render('home/login.html.twig', [
            'signup' => true
        ]);
    }

    /**
     * @Route("/login",name="login")
     */
    public function login()
    {
        return $this->render('home/login.html.twig', [
            'signup' => false
        ]);

    }

    /**
     * @Route("/process-signup",name="process_signup" ,methods="POST")
     */
    public function process_signup(Request $request, EntityManagerInterface $entityManager)
    {
        $user = new Users();
        $data = $request->request;
        $user->setFullName($data->get('name'))
            ->setEmail($data->get('email'))
            ->setPassword($data->get('password'))
            ->setUsername($data->get('username'));

        $entityManager->persist($user);
        $entityManager->flush();
        return $this->render('home/index.html.twig');


    }

}
