<?php


namespace App\Controller;


use App\Classes\Result;
use App\Entity\Contest;
use App\Entity\Problem;
use App\Entity\SampleInput;
use App\Entity\Status;
use App\Entity\Submission;
use App\Entity\User;
use App\Service\Judge;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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

//get all submissions of the current user in the current contest
    function get_all_submissions(Contest $contest)
    {
        $problems = $contest->getProblems();
        $ans = array();
        foreach ($problems as $problem) {
//            dd($this->getSubmissions($problem));
            $ans = array_merge($ans, $this->getSubmissions($problem));
        }
        return $ans;
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
            $submission->setStatus($statusRepo->findOneBy(['code'=>1]));
            $token = $j->submit($submission);
            $submission->setToken($token);
            $entity->persist($submission);
            $contest->addParticipant($user);
            $entity->persist($contest);
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
     * @Route("/{id}/scoreboard",name="scoreboard", methods={"GET"})
     */
    public function scoreboard(Contest $contest, EntityManagerInterface $em)
    {
        $participants = $contest->getParticipants();
        $result = [];
        foreach ($participants as $participant) {
            $userResult = new Result();
            $userResult->username = $participant->getUsername();
            foreach ($contest->getProblems() as $problem) {
                //0 for not submitted yet
                //1 in queue
                //2 for CE or RE or WA or TLE
                //3 for AC
                array_push($userResult->result, 0);
            }
            $subRepo = $em->getRepository(Submission::class);
            foreach ($contest->getProblems() as $problem) {
                $subs = $subRepo->findBy([
                    'user' => $participant->getId(),
                    'problem' => $problem->getId()]);


                foreach ($subs as $submission) {
                    $tmp = $submission->getStatus()->getCode();
                    $letter = $submission->getProblem()->getLetter();
                    if ($tmp == 3) {
                        //AC
                        $userResult->result[ord($letter) - ord("A")] = 3;
                    } elseif ($tmp == 1 or $tmp == 2) {
                        // in queue
                        $userResult->result[ord($letter) - ord("A")] = max($userResult->result[ord($letter) - ord("A")], 1);
                    } else {
                        // Wrong
                        $userResult->result[ord($letter) - ord("A")] = max($userResult->result[ord($letter) - ord("A")], 2);
                    }

                }
            }
            $solved = 0;
            for ($i = 0; $i < sizeof($userResult->result); $i++) {
                if ($userResult->result[$i] == "3") {
                    $solved++;
                }
            }
            $userResult->solved = $solved;
            array_push($result, $userResult);
        }
        usort($result, function (Result $a, Result $b) {
            return $b->solved - $a->solved;
        });
        $i=0;
        foreach($result as $tmp)
        {$i++;

            $tmp->rank=$i;
        }


        return $this->render('contests/scoreboard.html.twig', [
            "problems" => $contest->getProblems(),
            'results' => $result,
            'id' => $contest->getId(),
            "contest" => $contest,
            'submissions' => null
        ]);

    }


    /**
     * @Route("/{id}/my_submissions",name="my_submissions", methods={"GET"})
     */
    public function my_submissions(Contest $contest)
    {
        $submissions = $this->get_all_submissions($contest);
//        dd($submissions);
        return $this->render('contests/my_submissions.html.twig', [
            'id' => $contest->getId(),
            "submissions" => $submissions,
            "contest" => $contest,
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
        $problems = $contest->getProblems();
        $size = sizeof($problems);
        if ($this->checkLetter($letter) or ord($letter) - ord("A") >= $size) {
            throw $this->createNotFoundException('This problem does not exist');
        }
        $problem = $problems[ord($letter) - ord('A')];
        return $this->render('contests/editProblem.html.twig', [
            'id' => $contest->getId(),
            'problem' => $problem,
            'sample' => $problem->getSampleIn()[0],
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
        $problems = $contest->getProblems();
        $size = sizeof($problems);
        if ($this->checkLetter($letter) or ord($letter) - ord("A") >= $size) {
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
