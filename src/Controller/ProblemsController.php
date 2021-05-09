<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProblemsController extends AbstractController
{
    /**
     * @Route("/problems", name="problems")
     */
    public function index(): Response
    {
        return $this->render('problems/index.html.twig', []);
    }
}
