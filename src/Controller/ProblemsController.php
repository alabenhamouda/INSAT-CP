<?php

namespace App\Controller;

use App\Entity\Problem;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProblemsController extends AbstractController
{
    private $em;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em=$entityManager;
    }

    /**
     * @Route("/problems", name="problems")
     */
    public function index(): Response
    {
        $repo=$this->em->getRepository(Problem::class);
        /**@var \App\Entity\Problem $problems*/
        $problems=$repo->findAll();



        return $this->render('problems/index.html.twig', [
            'problems'=>$problems
        ]);
    }
}
