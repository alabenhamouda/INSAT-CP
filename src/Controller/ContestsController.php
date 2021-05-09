<?php


namespace App\Controller;


use App\Entity\Contest;
use App\Entity\Problem;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ContestsController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }

    /**
     * @Route("/contest",name="contests",methods={"GET"})
     */
    public function contestList()
    {
        $repo = $this->em->getRepository(Contest::class);
        $contests = $repo->findAll();
        return $this->render('contests/contests.html.twig', [
            'contests' => $contests
        ]);


    }

    /**
     * @Route("/contest/{id}",name="contest",methods={"GET"})
     */
    public function contest(Contest $contest)
    {
        //TODO CHECK FOR ID
        $problems = $contest->getProblems();
        return $this->render('contests/contest.html.twig', [
            'problems' => $problems,
            'id' => $contest->getId()
        ]);


    }

    /**
     * @Route("/contest/{id}/{letter}",name="problem", methods={"GET"})
     */
    public function problem(Contest $contest, $letter)
    {
        $letter = strtoupper($letter);
        return $this->render('problem/index.html.twig', [
            "problem" => $contest->getProblems()[ord($letter) - ord('A')],
            'id' =>$contest->getId()
        ]);


    }

    /**
     * @Route("/contest/{id}/{letter}/submit",name="submit", methods={"GET"})
     */
    public function submit(Contest $contest, $letter)
    {
        $letter = strtoupper($letter);
        return $this->render('problem/submit.html.twig', [
            "problem" => $contest->getProblems()[ord($letter) - ord('A')],
            'id' =>$contest->getId()
        ]);

    }
    /**
     * @Route("/contest/{id}/{letter}/solution",name="solution", methods={"GET"})
     */
    public function solution(Contest $contest, $letter)
    {
        $letter = strtoupper($letter);
        return $this->render('problem/solution.html.twig', [
            "problem" => $contest->getProblems()[ord($letter) - ord('A')],
            'id' =>$contest->getId()
        ]);

    }
}