<?php

namespace App\Controller;

use App\Entity\Contest;
use App\Entity\Problem;
use App\Entity\Tag;
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
        $this->em = $entityManager;
    }

    /**
     * @Route("/problems", name="problems")
     */
    public function index(Request $request, PaginatorInterface $paginator): Response
    {
        $repo = $this->em->getRepository(Problem::class);
        $tags = $this->em->getRepository(Tag::class)->findBy(array(), ['name' => 'ASC']);
        $title = $request->query->get('title');
        $up = $this->em->getRepository(Contest::class)->findupcoming();
        $prob_tags = $request->query->get('tag');
        $prob_tags = $this->em->getRepository(Tag::class)->findBy([
            'name' => $prob_tags
        ]);
        if ($title and $prob_tags) {
            $problems = $repo->findByTitleAndTags($title, $prob_tags);
            dump($title);
            dump($prob_tags);
        } else if ($title) {
            $problems = $repo->findByTitle($title);
        } else if ($prob_tags) {
            $problems = $repo->findByTags($prob_tags);
            dump($problems);
        } else $problems = $repo->findAll();
        $visible=[];

        foreach($problems as $problem){
            $contest = $problem->getContest();
            $status = $contest->getStatus();
            if($contest->getIsPublished() === true&&($status['status']=="running"||$status['status']=="finished") ){
                array_push($visible, $problem);
            }
        }
        /**@var \App\Entity\Problem $problems */
        if ($visible) {
            $visible = $paginator->paginate(
                $visible,
                $request->query->getInt('page', 1),
                $request->query->getInt('jumpBy', 10)
            );;
        }
        return $this->render('problems/index.html.twig', [
            'problems' => $visible,
            'tags' => $tags,
            'up' => $up
        ]);
    }
}
