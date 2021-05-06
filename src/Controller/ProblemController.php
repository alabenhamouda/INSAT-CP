<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProblemController extends AbstractController
{
    /**
     * @Route("/problem", name="problem")
     */
    public function index(): Response
    {
        return $this->render('problem/index.html.twig', [
            'controller_name' => 'ProblemController',
        ]);
    }
}
