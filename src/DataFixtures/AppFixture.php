<?php

namespace App\DataFixtures;

use App\Entity\Contest;
use App\Entity\Input;
use App\Entity\Problem;
use App\Entity\SampleInput;
use App\Entity\Status;
use App\Entity\Submission;
use App\Entity\Tag;
use App\Entity\User;
use Container9nkxRE3\getDoctrineMigrations_UpToDateCommandService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AppFixture extends Fixture
{


    public function load(ObjectManager $manager)
    {
        $fa = Factory::create();
        $fa->seed(101010);
        $tagArr = [];
        //create tags
        for ($i = 0; $i < 50; $i++) {
            $tag = new Tag();
            $tag->setName($fa->unique()->word);
            array_push($tagArr, $tag);
            $manager->persist($tag);
        }
        $fa->unique(true);
        $repo = $manager->getRepository(User::class);
        //create users
        $userArr = [];
        for ($i = 0; $i < 40; $i++) {
            $user = new User();
            $user->setUsername($fa->userName)
                ->setEmail($fa->email)
                ->setFullName($fa->name)
                ->setPassword($fa->password);
            array_push($userArr, $user);
            $manager->persist($user);
        }
//create contests
        $problemArr = [];
        for ($i = 0; $i < 40; $i++) {
            $contest = new Contest();
            $number = $fa->numberBetween(0, 39);
            $date = $fa->dateTimeBetween("-1 years", "+1 years", 'Africa/Tunis');
            $contest->setTitle($fa->sentence($fa->numberBetween(1, 4)))
                ->setStartDate($date)
                ->setDuration($fa->numberBetween(1, 300))
                ->setIsPublished(true)
                ->setCreator($userArr[$number]);
            $fa->unique(true);
            //create problems
            $letter = 'A';
            for ($j = 0; $j < $fa->numberBetween(1, 20); $j++) {
                $problem = new Problem();
                $problem->setTitle($fa->word)
                    ->setInputSpec($fa->text)
                    ->setOutputSpec($fa->text(200))
                    ->setLetter($letter)
                    ->setStatement($fa->realTextBetween(400, 1000))
                    ->setValidator($fa->text)
                    ->setContest($contest)
                    ->setPoints($fa->randomDigitNotZero());


                if ($fa->numberBetween(1, 10) < 9) {
                    $problem->setProof($fa->realText(400));
                } else {
                    $problem->setProof(null);
                }
                if ($fa->numberBetween(1, 10) < 9) {
                    $problem->setSolution($fa->realText(400));
                } else {
                    $problem->setSolution(null);
                }
                for ($k = 0; $k < $fa->numberBetween(1, 3); $k++) {
                    $problem->addTag($fa->unique()->randomElement($tagArr));
                }
                $fa->unique(true);
//                for ($k = 0; $k < $fa->numberBetween(1, 40); $k++) {
//                    $submission = new Submission();
//                    $submission->setUser($fa->unique()->randomElement($userArr))
//                        ->setProblem($problem)
//                        ->setCode($fa->realText())
//                        ->setLanguage($fa->word);
//                    $manager->persist($submission);
//                }
//                $fa->unique(true);


                $letter++;
                //create sample input
                for ($k = 0; $k < $fa->numberBetween(1, 5); $k++) {
                    $sample = new SampleInput();
                    $sample->setOutput($fa->text)
                        ->setInput($fa->text)
                        ->setProblem($problem);
                    $manager->persist($sample);
                }
                $input = new Input();
                $input->setInput("1 2");
                $input->setOutput("3");
                $problem->setInput($input);
                $manager->persist($problem);
                array_push($problemArr, $problem);
            }
            $manager->persist($contest);
        }

        // add statuses
        $descriptions = [
            "In Queue", "Processing", "Accepted", "Wrong Answer",
            "Time Limit Exceeded", "Compilation Error", "Runtime Error (SIGSEGV)",
            "Runtime Error (SIGXFSZ)", "Runtime Error (SIGFPE)", "Runtime Error (SIGABRT)",
            "Runtime Error (NZEC)", "Runtime Error (Other)", "Internal Error",
            "Exec Format Error"
        ];
        $statusArr = [];
        for ($i = 0; $i < count($descriptions); $i++) {
            $status = new Status();
            $status->setDescription($descriptions[$i])
                ->setCode($i + 1);
            $manager->persist($status);
            array_push($statusArr, $status);
        }
        //adding 200 mostly wrong submissions
        for ($i = 0; $i < 100; $i++) {
            $submission = new Submission();
            /** @var  $tprob Problem */
            $tprob = $fa->randomElement($problemArr);
            $tuser = $fa->randomElement($userArr);
            $submission->setLanguage("cpp")
                ->setUser($tuser)
                ->setCode($fa->text)
                ->setProblem($tprob)
                ->setStatus($fa->randomElement($statusArr))
                ->setToken("aaaaaa");
            $tcontest = $tprob->getContest();
            $submission->setInContest($tcontest->getStatus()['status'] == "running");
            $tcontest->addParticipant($tuser);

            $manager->persist($tprob);
            $manager->persist($tuser);
            $manager->persist($tcontest);
            $manager->persist($submission);
        }
        for ($i = 0; $i < 300; $i++) {
            $submission = new Submission();
            /** @var  $tprob Problem */
            $tprob = $fa->randomElement($problemArr);
            $tuser = $fa->randomElement($userArr);
            $submission->setLanguage("cpp")
                ->setUser($tuser)
                ->setCode($fa->text)
                ->setProblem($tprob)
                ->setStatus($statusArr[2])
                ->setToken("aaaaaa");
            $tcontest = $tprob->getContest();
            $submission->setInContest($tcontest->getStatus()['status'] == "running");
            $tcontest->addParticipant($tuser);

            $manager->persist($tprob);
            $manager->persist($tuser);
            $manager->persist($tcontest);
            $manager->persist($submission);
        }
        $manager->flush();
    }
}
