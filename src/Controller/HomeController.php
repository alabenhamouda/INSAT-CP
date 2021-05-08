<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
        return $this->render('home/signup.html.twig');


    }
    /**
     * @Route("/login",name="login")
     */
    public function login()
    {
        return $this->render('home/login.html.twig');

    }
    ///**
    // * @Route("/process-signup",name="process_signup")
    // */
    //public function process_signup($Request $request)
    //{

    //}

}
