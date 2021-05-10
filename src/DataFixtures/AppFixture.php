<?php

namespace App\DataFixtures;

use App\Entity\Contest;
use App\Entity\Problem;
use App\Entity\SampleInput;
use App\Entity\Submission;
use App\Entity\Tag;
use App\Entity\Users;
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
            $tag->setName($fa->word);
            array_push($tagArr, $tag);
            $manager->persist($tag);
        }
        $repo = $manager->getRepository(Users::class);
        //create users
        $userArr = [];
        for ($i = 0; $i < 40; $i++) {
            $user = new Users();
            $user->setUsername($fa->userName)
                ->setEmail($fa->email)
                ->setFullName($fa->name)
                ->setPassword($fa->password);
            array_push($userArr, $user);
            $manager->persist($user);
        }
//create contests
        $samir = new Contest();
        for ($i = 0; $i < 40; $i++) {
            $contest = new Contest();
            $number = $fa->numberBetween(0, 39);
            $contest->setTitle($fa->sentence($fa->numberBetween(1, 4)))
                ->setStartDate($fa->dateTimeBetween('-1 years','+1 years'))
                ->setStartTime($fa->dateTime())
                ->setDuration($fa->numberBetween(1, 300))
                ->setCreator($userArr[$number]);
            for ($j = 0; $j < $fa->numberBetween(1, 40); $j++) {
                $contest->addParticipant($fa->unique()->randomElement($userArr));
            }
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
                    ->setContestSource($contest)
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
                for ($k = 0; $k < $fa->numberBetween(1, 40); $k++) {
                    $submission = new Submission();
                    $submission->setUser($fa->unique()->randomElement($userArr))
                        ->setProblem($problem)
                        ->setCode($fa->realText())
                        ->setLanguage($fa->word);
                    $manager->persist($submission);
                }
                $fa->unique(true);


                $letter++;
                //create sample input
                for ($k = 0; $k < $fa->numberBetween(1, 5); $k++) {
                    $sample = new SampleInput();
                    $sample->setOutput($fa->text)
                        ->setInput($fa->text)
                        ->setProblem($problem);
                    $manager->persist($sample);
                }
                $manager->persist($problem);

            }
            $manager->persist($contest);
            $manager->flush();
        }

        $manager->flush();
    }
}
