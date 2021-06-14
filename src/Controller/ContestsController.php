<?php


namespace App\Controller;


use App\Classes\Result;
use App\Entity\Contest;
use App\Entity\Input;
use App\Entity\Problem;
use App\Entity\SampleInput;
use App\Entity\Status;
use App\Entity\Submission;
use App\Entity\Tag;
use App\Entity\User;
use App\Service\Judge;
use DateTime;
use DateTimeZone;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
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
//        $up = $repo->findupcoming();
//        $rec = $repo->findrecent();
        if ($title) {
            $contests = $repo->findByTitle($title);
        } else $contests = $repo->findAllOrderedbyDate();
        $up = [];
        $finished = [];
        foreach ($contests as $contest) {
            $tmp = $contest->getStatus()['status'];

            if ($tmp === "not_started" or $tmp === "running") {
                array_push($up, $contest);
            } else {
                array_push($finished, $contest);
            }
        }


        $finished = array_reverse($finished);
        if ($finished) {
            $finished = $paginator->paginate(
                $finished,
                $request->query->getInt('page', 1),
                $request->query->getInt('jumpBy', 10)
            );
        }
        //dd($visible_contests);
        return $this->render('contests/contests.html.twig', [
            'degla' => "Finished Contests",
            'finished' => $finished
        ]);
    }

    /**
     * @Route("/upcoming",name="upcoming",methods={"GET"})
     */
    public function upcoming(Request $request, PaginatorInterface $paginator)
    {
        $repo = $this->em->getRepository(Contest::class);
        $title = $request->query->get('title');
        $contests = $repo->findAllOrderedbyDate();
        $up = [];
        $finished = [];
        foreach ($contests as $contest) {
            $tmp = $contest->getStatus()['status'];
//            dump($tmp);

            if ($tmp === "not_started" or $tmp === "running") {
                array_push($up, $contest);
            } else {
                array_push($finished, $contest);
            }
        }

        if ($up) {
            $up = $paginator->paginate(
                $up,
                $request->query->getInt('page', 1),
                $request->query->getInt('jumpBy', 10)
            );
        }
        //dd($visible_contests);
        return $this->render('contests/contests.html.twig', [
            'degla' => 'Upcoming Contests',
            'finished' => $up
        ]);
    }


    /**
     * @Route("/{id<\d+>}",name="contest",methods={"GET"})
     */
    public function contest(Contest $contest)
    {
        $status = $contest->getStatus();
        if ($contest->allowed_inside_contest($this->getUser())) {
            $problems = $contest->getProblems();
            return $this->render('contests/contest.html.twig', [
                'status' => $status,
                'problems' => $problems,
                'id' => $contest->getId()
            ]);
        }

        if ($status['is_published']) {
            return $this->render('contests/registration_page.html.twig', [
                'registred' => ($this->getUser()) && ($this->getUser()->getContests()->contains($contest) | null),
                'status' => $status,
                'contest' => $contest,
                'id' => $contest->getId()
            ]);
        } else {
            throw $this->createAccessDeniedException('You cannot access this page!');
        }

    }

    /**
     * @Route("/{id<\d+>}/register",name="contest_registration",methods={"GET"})
     */
    public function contest_registration(Contest $contest)
    {
        $this->denyAccessUnlessGranted("ROLE_USER");
        $status = $contest->getStatus();
        if ($status['status'] = 'not_started' && $status['is_published']
            && $this->getUser()->getId() != $contest->getCreator()->getId()) {
            $user = $this->getUser();
            $contest->addParticipant($user);
            $user->addContest($contest);
            $this->em->persist($contest);
            $this->em->flush();
        }
        return $this->redirectToRoute('contest', ['id' => $contest->getId()]);
    }


    private function updateSubmissions($submissions)
    {
        $changed = false;
        foreach ($submissions as $submission) {
            /** @var $submission Submission */
            if ($submission->getToken() != "aaaaaa") {
                $status = $submission->getStatus();
                if ($status->getCode() == 1 || $status->getCode() == 2) {
                    $response = $this->j->getSubmission($submission->getToken());
                    $statusId = $response->status->id;
                    if ($status->getCode() != $statusId) {
                        $repo = $this->em->getRepository(Status::class);
                        $submission->setStatus($repo->findOneBy(['code' => $statusId]));
                        $this->em->persist($submission);
                        $changed = true;
                    }
                }
            }
        }
        if ($changed) {
            $this->em->flush();
        }
        return $submissions;
    }

    private function getSubmissions($problem)
    {
        $submissions = array();
        if ($this->getUser()) {
            $repo = $this->em->getRepository(Submission::class);
            $submissions = $repo->findBy([
                'user' => $this->getUser()->getId(),
                'problem' => $problem->getId()
            ]);
            $submissions = $this->updateSubmissions($submissions);
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
    public
    function problem(Contest $contest, $letter)
    {
        if (!$contest->allowed_inside_contest($this->getUser())) {
            return $this->redirectToRoute('contest', ['id' => $contest->getId()]);
        }
        $status = $contest->getStatus();

        $submissions = $this->getSubmissions($contest->getProblem($letter));
        //TODO check on letter
        $letter = strtoupper($letter);
        return $this->render('problem/index.html.twig', [
            'status' => $status,
            "problem" => $contest->getProblems()[ord($letter) - ord('A')],
            'id' => $contest->getId(),
            'submissions' => $submissions
        ]);


    }

    /**
     * @Route("/{id}/problem/{letter}/submit",name="submit", methods={"GET"})
     */
    public
    function submit(Contest $contest, $letter)
    {
        if (!$contest->allowed_inside_contest($this->getUser())) {
            return $this->redirectToRoute('contest', ['id' => $contest->getId()]);
        }
        $status = $contest->getStatus();
        $submissions = $this->getSubmissions($contest->getProblem($letter));
        //TODO check on letter
        $letter = strtoupper($letter);
        return $this->render('problem/submit.html.twig', [
            'status' => $status,
            "problem" => $contest->getProblems()[ord($letter) - ord('A')],
            'id' => $contest->getId(),
            'submissions' => $submissions
        ]);

    }

    /**
     * @Route("/{id}/problem/{letter}/submit",name="process_submit", methods={"POST"})
     */
    public
    function processSubmit(Contest $contest, $letter, Request $request, Judge $j, EntityManagerInterface $entity)
    {
        $user = $this->getUser();
        if (!$contest->allowed_inside_contest($user)) {
            return $this->redirectToRoute('contest', ['id' => $contest->getId()]);
        }

        if ($user) {
            $statusRepo = $entity->getRepository(Status::class);
            $data = $request->request->all();
            $submission = new Submission();
            $submission->setInContest($contest->getStatus()['status'] == "running"
                && $user->getContests()->contains($contest));
            $submission->setUser($user);
            $submission->setCode($data['source_code']);
            $submission->setLanguage($data['language_id']);
            $submission->setProblem($contest->getProblem($letter));
            $submission->setStatus($statusRepo->findOneBy(['code' => 1]));
            $token = $j->submit($submission);
            $submission->setToken($token);
            $entity->persist($submission);
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
    public
    function solution(Contest $contest, $letter)
    {

        $user = $this->getUser();
        $status = $contest->getStatus();
        if ($user == null || !$contest->allowed_inside_contest($user) ||
            ($status['status'] != "finished" && $user->getId() != $contest->getCreator()->getId())) {
            return $this->redirectToRoute('problem', ['id' => $contest->getId(), 'letter' => $letter]);
        }

        $submissions = $this->getSubmissions($contest->getProblem($letter));
        $letter = strtoupper($letter);
        return $this->render('problem/solution.html.twig', [
            'status' => $status,
            "problem" => $contest->getProblems()[ord($letter) - ord('A')],
            'id' => $contest->getId(),
            'submissions' => $submissions
        ]);

    }

    /**
     * @Route("/{id}/scoreboard",name="scoreboard", methods={"GET"})
     */
    public
    function scoreboard(Contest $contest, EntityManagerInterface $em)
    {
        $user = $this->getUser();
        $status = $contest->getStatus();
        if (!$contest->allowed_inside_contest($user)) {
            return $this->redirectToRoute('contest', ['id' => $contest->getId()]);
        }
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
                $subs = $this->updateSubmissions($subs);


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
        $i = 0;
        foreach ($result as $tmp) {
            $i++;

            $tmp->rank = $i;
        }


        return $this->render('contests/scoreboard.html.twig', [
            'status' => $status,
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
    public
    function my_submissions(Contest $contest)
    {
        $this->denyAccessUnlessGranted("ROLE_USER");
        $user = $this->getUser();
        $status = $contest->getStatus();
        if (!$contest->allowed_inside_contest($user)) {
            return $this->redirectToRoute('contest', ['id' => $contest->getId()]);
        }

        $submissions = $this->get_all_submissions($contest);
//        dd($submissions);
        return $this->render('contests/my_submissions.html.twig', [
            'status' => $status,
            'id' => $contest->getId(),
            "submissions" => $submissions,
            "contest" => $contest,
        ]);
    }

    /**
     * @Route("/create",name="create_contest",methods="GET")
     */
    public
    function create()
    {
        if (!$this->getUser()) {
            throw $this->createAccessDeniedException("you need to sign in before creating a contest");
        }
        $contest = new Contest();

        return $this->render('contests/create.html.twig', [
            'contest' => $contest
        ]);

    }

    private function validateDate($value, $format = "Y-m-d H:i:s")
    {
        if (!$value) {
            return false;
        }

        try {
            new DateTime($value);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * @Route("/create",name="process_create_contest",methods={"POST"})
     */
    public
    function processCreate(Request $request, EntityManagerInterface $em, AuthenticationUtils $auth)
    {
        if (!$this->getUser()) {
            throw $this->createAccessDeniedException("you need to sign in before creating a contest");

        }
        $user = $em->getRepository(User::class)->findOneBy(['username' => $this->getUser()->getUsername()]);
        $contest = new Contest();
        $r = $request->request;
        $contest->setTitle($r->get('title'))
            ->setDuration($r->get('duration'))
            ->setCreator($user)
            ->setStartDate(new DateTime($r->get('date') . " " . $r->get('time') . ":00", new DateTimeZone('Africa/Tunis')));
        $em->persist($contest);
        $em->flush();

        return $this->redirectToRoute('myContests');

    }

    /**
     * @Route("/my",name="myContests",methods={"GET"})
     */
    public
    function myContests(EntityManagerInterface $em)
    {
        $this->denyAccessUnlessGranted("ROLE_USER");

        $user = $em->getRepository(User::class)->findOneBy(['username' => $this->getUser()->getUsername()]);
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
    public
    function edit(Contest $contest, EntityManagerInterface $em)
    {
        $this->denyAccessUnlessGranted("ROLE_USER");

        if ($contest->getCreator()->getId() != $this->getUser()->getId()) {
            //TODO output message "you should be the owner of the contest"
            return $this->redirectToRoute('myContests');
        }
        $repo = $em->getRepository(Problem::class);
        $list = $repo->findBy(['contest' => $contest->getId()], ['Letter' => 'ASC']);
        return $this->render('contests/edit.html.twig', [
            'contest' => $contest,
            'problems' => $list
        ]);
    }

    /**
     * @Route ("/edit/{id<\d+>}/addProblem",name="addProblem",methods="POST")
     */
    public
    function addProblem(Contest $contest, Request $request, EntityManagerInterface $em)
    {

        $this->denyAccessUnlessGranted("ROLE_USER");

        if ($contest->getCreator()->getId() != $this->getUser()->getId()) {
            //TODO output message "you should be the owner of the contest"
            return $this->redirectToRoute('myContests');
        }
        $tags = $em->getRepository(Tag::class)->findAll();
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
        $input = new Input();
        $input->setInput("")->setOutput("");
        $problem->setInput($input);
        $contest->addProblem($problem);
        $em->persist($input);
        $em->persist($sample);
        $em->persist($problem);
        $em->persist($contest);
        $em->flush();
        return $this->redirectToRoute('edit_problem', [
            'id' => $contest->getId(),
            'letter' => $problem->getLetter()
        ]);


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
    public
    function editProblem(Contest $contest, $letter, EntityManagerInterface $em)
    {

        $this->denyAccessUnlessGranted("ROLE_USER");

        if ($contest->getCreator()->getId() != $this->getUser()->getId()) {
            //TODO output message "you should be the owner of the contest"
            return $this->redirectToRoute('myContests');
        }
        $problems = $contest->getProblems();
        $size = sizeof($problems);
        if ($this->checkLetter($letter) or ord($letter) - ord("A") >= $size) {
            throw $this->createNotFoundException('This problem does not exist');
        }
        $tags = $em->getRepository(Tag::class)->findAll();
        $problem = $problems[ord($letter) - ord('A')];
        return $this->render('contests/editProblem.html.twig', [
            'id' => $contest->getId(),
            'problem' => $problem,
            'sample' => $problem->getSampleIn()[0],
            'input' => $problem->getInput(),
            'tags' => $tags
        ]);


    }

    /**
     * @Route ("/edit/{id<\d+>}/{letter}/process" ,name="process_edit_problem" ,methods={"POST"})
     */
    public
    function processEditProblem(Contest $contest, Request $request, EntityManagerInterface $em, $letter)
    {
        $this->denyAccessUnlessGranted("ROLE_USER");

        //TODO check user
        //TODO check letter

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
        if (!empty($tags)) {
            foreach ($r->get('tags') as $tag) {
                $problem->AddTag($em->getRepository(Tag::class)->findOneBy(['name' => $tag]));
            }
        }
        //TODO check POST input or use symfony form
        $sample = $problem->getSampleIn()[0];
        $input = new Input();
        $sample->setInput($r->get("insamp"))
            ->setOutput($r->get('outsamp'))
            ->setProblem($problem);
        $problem->setTitle($r->get('title'))
            ->setStatement($r->get('statement'))
            ->setInputSpec($r->get('input_spec'))
            ->setOutputSpec($r->get('output_spec'))
            ->setProof($r->get('proof'))
            ->setSolution($r->get('solution'))
            ->setTimeLimit($r->get('timelimit'))
            ->setPoints($r->get('points'));
        $input->setInput($r->get('intest'))
            ->setOutput($r->get('outtest'));
        $problem->setInput($input);
        $em->persist($input);
        $em->persist($sample);
        $em->persist($problem);
        $em->flush();
        return $this->redirectToRoute('editContest', ['id' => $contest->getId()]);


    }

    /**
     * @Route("/{id<\d+>}/publish", name="publish" ,methods={"POST"})
     */
    public
    function publish(Contest $contest, EntityManagerInterface $em)
    {
        $this->denyAccessUnlessGranted("ROLE_USER");

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
