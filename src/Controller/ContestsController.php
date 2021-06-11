<?php


namespace App\Controller;


use App\Entity\Contest;
use App\Entity\Problem;
use App\Entity\SampleInput;
use App\Entity\Submission;
use App\Entity\User;
use App\Service\Judge;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * Class ContestsController
 * @package App\Controller
 * @Route("/contests")
 */
class ContestsController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }

    /**
     * @Route("/",name="contests",methods={"GET"})
     */
    public function contestList(Request $request, PaginatorInterface $paginator)
    {
        $repo = $this->em->getRepository(Contest::class);
        $title = $request->query->get('title');
        $up = $repo->findupcoming();
        $rec = $repo->findrecent();
        if ($title) {
            $contests = $repo->findByTitle($title);
        } else $contests = $repo->findAllOrderedbyDate();
        $visible_contests = $contests;
        if ($visible_contests) {
            $visible_contests = $paginator->paginate(
                $contests,
                $request->query->getInt('page', 1),
                $request->query->getInt('jumpBy', 10)
            );
        }
        //dd($visible_contests);
        return $this->render('contests/contests.html.twig', [
            'contests' => $visible_contests,
            'up' => $up,
            'rec' => $rec
        ]);
    }

    /**
     * @Route("/{id<\d+>}",name="contest",methods={"GET"})
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
     * @Route("/{id}/problem/{letter}",name="problem", methods={"GET"})
     */
    public function problem(Contest $contest, $letter)
    {
        //TODO check on letter
        $letter = strtoupper($letter);
        return $this->render('problem/index.html.twig', [
            "problem" => $contest->getProblems()[ord($letter) - ord('A')],
            'id' => $contest->getId()
        ]);


    }

    /**
     * @Route("/{id}/problem/{letter}/submit",name="submit", methods={"GET"})
     */
    public function submit(Contest $contest, $letter)
    {
        //TODO check on letter
        $letter = strtoupper($letter);
        return $this->render('problem/submit.html.twig', [
            "problem" => $contest->getProblems()[ord($letter) - ord('A')],
            'id' => $contest->getId()
        ]);

    }

    /**
     * @Route("/{id}/problem/{letter}/submit",name="process_submit", methods={"POST"})
     */
    public function processSubmit(Contest $contest, $letter, Request $request, Judge $j, EntityManagerInterface $entity)
    {
        $user = $this->getUser();
        if ($user) {
            $data = $request->request->all();
            $submission = new Submission();
            $submission->setUser($user);
            $submission->setCode($data['source_code']);
            $submission->setLanguage($data['language_id']);
            $submission->setProblem($contest->getProblem($letter));
            $token = $j->submit($submission);
            $submission->setToken($token);
            $entity->persist($submission);
            $entity->flush();
            $this->addFlash("success", "Code was submitted");
        } else {
            $this->addFlash('error', 'You must be logged in to submit a solution');
        }
        return $this->redirectToRoute("submit", ['id' => $contest->getId(), 'letter' => $letter]);
    }

    /**
     * @Route("/{id}/problem/{letter}/solution",name="solution", methods={"GET"})
     */
    public function solution(Contest $contest, $letter)
    {
        $letter = strtoupper($letter);
        return $this->render('problem/solution.html.twig', [
            "problem" => $contest->getProblems()[ord($letter) - ord('A')],
            'id' => $contest->getId()
        ]);

    }
    /**
     * @Route("/{id}/scoreboard",name="scoreboard", methods={"GET"})
     */
    public function scoreboard(Contest $contest )//,Request $request,PaginatorInterface $paginator ,Scoreboard_service $sc_s)
    {
        //mazelt ma7btch t5dem
        //$scoreboard=$sc_s->get_scoreboard($contest->getid());

        /*   =$paginator->paginate(
            $xxxxxxx,
            $request->query->getInt('page', 1),
            $request->query->getInt('jumpBy', 10)
        );
        */


        return $this->render('problem/scoreboard.html.twig', [
            "problems" => $contest->getProblems(),
            "problem" => $contest->getProblems()[0],
            'id' => $contest->getId(),
            "contest"=>$contest
        ]);

    }

    /**
     * @Route("/create",name="create_contest",methods="GET")
     */
    public function create()
    {
        if (!$this->getUser()) {
            throw $this->createAccessDeniedException("you need to sign in before creating a contest");

        }
        $contest = new Contest();

        return $this->render('contests/create.html.twig', [
            'contest' => $contest
        ]);

    }

    /**
     * @Route("/create",name="process_create_contest",methods={"POST"})
     */
    public function processCreate(Request $request, EntityManagerInterface $em, AuthenticationUtils $auth)
    {

        if (!$this->getUser()) {
            throw $this->createAccessDeniedException("you need to sign in before creating a contest");

        }
        $user = $em->getRepository(User::class)->findOneBy(['username' => $this->getUser()->getUsername()]);
        $contest = new Contest();
        $r = $request->request;
        $contest->setTitle($r->get('title'))
            ->setDuration($r->get('duration'))
            ->setStartTime(new DateTime("00:00"))
            ->setCreator($user)
            ->setStartDate(new DateTime($r->get('date')));
        $em->persist($contest);
        $em->flush();

        return $this->redirectToRoute('myContests');

    }

    /**
     * @Route("/my",name="myContests",methods={"GET"})
     */
    public function myContests(EntityManagerInterface $em)
    {
        $auth = $this->getUser();
        if (empty($auth)) {
            throw $this->createAccessDeniedException("alfred");

        }

        $user = $em->getRepository(User::class)->findOneBy(['username' => $auth->getUsername()]);
        $allContests = $user->getCreatedContests();
        $published = [];
        $unpublished = [];
        $participated = $user->getContests();
        foreach ($allContests as $contest) {
            if ($contest->getIsPublished()) {
                array_push($published, $contest);
            } else {
                array_push($unpublished, $contest);
            }
        }
        return $this->render('contests/mycontests.html.twig', [
            'published' => $published,
            'unpublished' => $unpublished,
            'participated' => $participated
        ]);


    }

    /**
     * @Route("/edit/{id<\d+>}",name="editContest",methods={"GET"})
     */
    public function edit(Contest $contest, EntityManagerInterface $em)
    {
        if (!$this->getUser()) {
            throw $this->createAccessDeniedException("alfred");
        }
        if ($contest->getCreator()->getId() != $this->getUser()->getId()) {
            //TODO output message "you should be the owner of the contest"
            return $this->redirectToRoute('myContests');
        }
        return $this->render('contests/edit.html.twig', [
            'contest' => $contest,
            'problems' => $contest->getProblems()
        ]);
    }

    /**
     * @Route ("/edit/{id<\d+>}/addProblem",name="addProblem",methods="POST")
     */
    public function addProblem(Contest $contest, Request $request, EntityManagerInterface $em)
    {
        //TODO check user
        if (!$this->getUser()) {
            throw $this->createAccessDeniedException("alfred");
        }
        if ($contest->getCreator()->getId() != $this->getUser()->getId()) {
            //TODO output message "you should be the owner of the contest"
            return $this->redirectToRoute('myContests');
        }
        $problem = new Problem();
        $sample = new SampleInput();
        $sample->setProblem($problem)
            ->setOutput("")
            ->setInput("");
        $problem->setTitle("")
            ->setContest($contest)
            ->setValidator("")
            ->setPoints(0)
            ->setTitle("")
            ->setSolution("")
            ->setProof("")
            ->setStatement("")
            ->setLetter(chr(sizeof($contest->getProblems()) + ord('A')))
            ->setOutputSpec("")
            ->setInputSpec("")
            ->addSampleIn($sample);
        $contest->addProblem($problem);
        $em->persist($sample);
        $em->persist($problem);
        $em->persist($contest);
        $em->flush();
        return $this->redirectToRoute('edit_problem', ['id' => $contest->getId(), 'letter' => $problem->getLetter()]);


    }

    private function checkLetter(string $letter)
    {
        if (empty($lettter) or strlen($letter) > 1) {
            return false;
        }
        if (ord($letter) < ord("A") or ord($letter) > ord("Z")) {
            return false;
        }
        return true;
    }

    /**
     * @Route ("/edit/{id<\d+>}/{letter}",name="edit_problem",methods={"GET"})
     */
    public function editProblem(Contest $contest, $letter)
    {

        if (!$this->getUser()) {
            throw $this->createAccessDeniedException("alfred");
        }
        if ($contest->getCreator()->getId() != $this->getUser()->getId()) {
            //TODO output message "you should be the owner of the contest"
            return $this->redirectToRoute('myContests');
        }
        $problems=$contest->getProblems();
        $size=sizeof($problems);
        if($this->checkLetter($letter) or ord($letter)-ord("A")>=$size )
        {
            throw $this->createNotFoundException('This problem does not exist');
        }
        $problem = $problems[ord($letter) - ord('A')];
        return $this->render('contests/editProblem.html.twig', [
            'id' => $contest->getId(),
            'problem' => $problem,
            'sample' => $problem->getSampleIn()[0]
        ]);


    }

    /**
     * @Route ("/edit/{id<\d+>}/{letter}/process" ,name="process_edit_problem" ,methods={"POST"})
     */
    public function processEditProblem(Contest $contest, Request $request, EntityManagerInterface $em, $letter)
    {
        //TODO check user
        //TODO check letter


        if (!$this->getUser()) {
            throw $this->createAccessDeniedException("alfred");
        }
        if ($contest->getCreator()->getId() != $this->getUser()->getId()) {
            //TODO output message "you should be the owner of the contest"
            return $this->redirectToRoute('myContests');
        }
        $problems=$contest->getProblems();
        $size=sizeof($problems);
        if($this->checkLetter($letter) or ord($letter)-ord("A")>=$size )
        {
            throw $this->createNotFoundException('This problem does not exist');
        }
        $problem = $problems[ord($letter) - ord('A')];
        $r = $request->request;
        //TODO check POST input or use symfony form
        $sample = $problem->getSampleIn()[0];
        $sample->setInput($r->get("insamp"))
            ->setOutput($r->get('outsamp'))
            ->setProblem($problem);
        $problem->setTitle($r->get('title'))
            ->setStatement($r->get('statement'))
            ->setInputSpec($r->get('input_spec'))
            ->setOutputSpec($r->get('output_spec'))
            ->setProof($r->get('proof'))
            ->setValidator($r->get('validator'))
            ->setSolution($r->get('solution'))
            ->setPoints($r->get('points'));
        $em->persist($sample);
        $em->persist($problem);
        $em->flush();
        return $this->redirectToRoute('editContest', ['id' => $contest->getId()]);


    }

    /**
     * @Route("/{id<\d+>}/publish", name="publish" ,methods={"POST"})
     */
    public function publish(Contest $contest, EntityManagerInterface $em)
    {
        if (!$this->getUser()) {
            throw $this->createAccessDeniedException("alfred");
        }
        if ($contest->getCreator()->getId() != $this->getUser()->getId()) {
            //TODO output message "you should be the owner of the contest"
            return $this->redirectToRoute('myContests');
        }
        $contest->setIsPublished(true);
        $em->persist($contest);
        $em->flush();
        return $this->redirectToRoute('myContests');

    }



}
