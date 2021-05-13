<?php

namespace App\Controller;

use App\Entity\Problem;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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
    public function index(Request $request,PaginatorInterface $paginator): Response
    {
        $repo=$this->em->getRepository(Problem::class);
        /**@var \App\Entity\Problem $problems*/
        $problems=$paginator->paginate(
            $repo->findAll(),
            $request->query->getInt('page',1),
            $request->query->getInt('jumpBy',24)
        );;



        return $this->render('problems/index.html.twig', [
            'problems'=>$problems
        ]);
    }
}
