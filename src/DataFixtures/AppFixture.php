<?php

namespace App\DataFixtures;

use App\Entity\Contest;
use App\Entity\Problem;
use App\Entity\SampleInput;
use App\Entity\Tag;
use App\Entity\Users;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AppFixture extends Fixture
{


    public function load(ObjectManager $manager)
    {
        $fa=Factory::create();
        $fa->seed(101010);
        $tagArr=[];
        for($i=0;$i<50;$i++)
        {
            $tag=new Tag();
            $tag->setName($fa->word);
            array_push($tagArr,$tag);
            $manager->persist($tag);
        }
        $repo = $manager->getRepository(Users::class);
        //create users
        $userArr=[];
        for ($i = 0; $i < 40; $i++) {
            $user = new Users();
            $user->setUsername($fa->userName)
                ->setEmail($fa->email)
                ->setFullName($fa->name)
                ->setPassword($fa->password);
            array_push($userArr,$user);
            $manager->persist($user);
        }
//create contests
        $samir = new Contest();
        for ($i = 0; $i < 40; $i++) {
            $contest = new Contest();
            $number=$fa->numberBetween(0, 39);
            $contest->setTitle($fa->sentence($fa->numberBetween(1,4)))
                ->setStartTimme($fa->dateTime)
                ->setDuration($fa->numberBetween(1, 3000000))
                ->setCreator($userArr[$number]);
            $letter='A';
            for($j=0;$j<$fa->numberBetween(1,20);$j++)
            {
                $problem=new Problem();
                $problem->setTitle($fa->word)
                    ->setInputSpec($fa->text)
                    ->setOutputSpec($fa->text(200))
                    ->setLetter($letter)
                    ->setStatement($fa->text)
                    ->setValidator($fa->text)
                    ->setContestSource($contest)
                    ->addTag($tagArr[$fa->numberBetween(1,40)]);
                $letter++;
                for($k=0;$k<$fa->numberBetween(1,5);$k++)
                {
                    $sample=new SampleInput();
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
