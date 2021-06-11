<?php


namespace App\Controller;


use App\Entity\Contest;
use App\Entity\Problem;
use App\Entity\Status;
use App\Entity\Submission;
use App\Entity\User;
use App\Service\Judge;
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
    private $j;

    public function __construct(EntityManagerInterface $entityManager, Judge $j)
    {
        $this->em = $entityManager;
        $this->j = $j;
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

    private function getSubmissions($problem)
    {
        $submissions = null;
        if ($this->getUser()) {
            $repo = $this->em->getRepository(Submission::class);
            $submissions = $repo->findBy([
                'user' => $this->getUser()->getId(),
                'problem' => $problem->getId()
            ]);
            $changed = false;
            foreach ($submissions as $submission) {
                $status = $submission->getStatus();
                if ($status->getId() == 1 || $status->getId() == 2) {
                    $response = $this->j->getSubmission($submission->getToken());
                    $statusId = $response->status->id;
                    if ($status->getId() != $statusId) {
                        $submission->setStatus($this->em->find(Status::class, $statusId));
                        $this->em->persist($submission);
                        $changed = true;
                    }
                }
            }
            if ($changed) {
                $this->em->flush();
            }
        }
        return $submissions;
    }

    /**
     * @Route("/{id}/problem/{letter}",name="problem", methods={"GET"})
     */
    public function problem(Contest $contest, $letter)
    {
        $submissions = $this->getSubmissions($contest->getProblem($letter));
        //TODO check on letter
        $letter = strtoupper($letter);
        return $this->render('problem/index.html.twig', [
            "problem" => $contest->getProblems()[ord($letter) - ord('A')],
            'id' => $contest->getId(),
            'submissions' => $submissions
        ]);


    }

    /**
     * @Route("/{id}/problem/{letter}/submit",name="submit", methods={"GET"})
     */
    public function submit(Contest $contest, $letter)
    {
        $submissions = $this->getSubmissions($contest->getProblem($letter));
        //TODO check on letter
        $letter = strtoupper($letter);
        return $this->render('problem/submit.html.twig', [
            "problem" => $contest->getProblems()[ord($letter) - ord('A')],
            'id' => $contest->getId(),
            'submissions' => $submissions
        ]);

    }

    /**
     * @Route("/{id}/problem/{letter}/submit",name="process_submit", methods={"POST"})
     */
    public function processSubmit(Contest $contest, $letter, Request $request, Judge $j, EntityManagerInterface $entity)
    {
        $user = $this->getUser();
        if ($user) {
            $statusRepo = $entity->getRepository(Status::class);
            $data = $request->request->all();
            $submission = new Submission();
            $submission->setUser($user);
            $submission->setCode($data['source_code']);
            $submission->setLanguage($data['language_id']);
            $submission->setProblem($contest->getProblem($letter));
            $submission->setStatus($statusRepo->find(1));
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
        $submissions = $this->getSubmissions($contest->getProblem($letter));
        $letter = strtoupper($letter);
        return $this->render('problem/solution.html.twig', [
            "problem" => $contest->getProblems()[ord($letter) - ord('A')],
            'id' => $contest->getId(),
            'submissions' => $submissions
        ]);

    }

    /**
     * @Route("/create",name="create_contest",methods="GET")
     */
    public function create()
    {
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
            ->setStartTime(new \DateTime("00:00"))
            ->setCreator($user)
            ->setStartDate(new \DateTime($r->get('date')));
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
        $creator = $em->getRepository(User::class)->findOneBy(['username' => $this->getUser()->getUsername()]);
        if ($creator == null) {
            throw new HttpException(500, "contest has no creator");
        }
        if ($contest->getCreator()->getId() != $creator->getId()) {
            //TODO output message "you should be the owner of the contest"
            $this->redirectToRoute('myContests');
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
        $problem = new Problem();
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
            ->setInputSpec("");
        $contest->addProblem($problem);
        $em->persist($problem);
        $em->persist($contest);
        $em->flush();
        return $this->redirectToRoute('edit_problem', ['id' => $contest->getId(), 'letter' => $problem->getLetter()]);


    }

    /**
     * @Route ("/edit/{id<\d+>}/{letter}",name="edit_problem",methods={"GET"})
     */
    public function editProblem(Contest $contest, $letter)
    {
        //TODO check user
        //TODO check letter
        $problem = $contest->getProblems()[ord($letter) - ord('A')];
        return $this->render('contests/editProblem.html.twig', [
            'problem' => $problem
        ]);


    }

    /**
     * @Route ("/edit/{id<\d+>}/{letter}/process" ,name="process_edit_problem" ,methods={"POST"})
     */
    public function processEditProblem(Contest $contest, Request $request, EntityManagerInterface $em, $letter)
    {
        //TODO check user
        //TODO check letter


        $r = $request->request;
//        dd($letter);
        $problem = $contest->getProblems()[ord($letter) - ord('A')];
        //TODO check POST input or use symfony form
        $problem->setTitle($r->get('title'))
            ->setStatement($r->get('statement'));
        $em->persist($problem);
        $em->flush();
        return $this->redirectToRoute('myContests');


    }


}
