<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;

class participants {
    private $problem; //list fiha el problem id=>letter
    private $user_id;

    public array $submissions=['121','513','6516'];

    private $scoreof_solved_problem;//associative arrays koll problem wel score mt3ha
    private $attemps;
    private $penality;
    private $nbr_solved_problem;

    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function clear_submissions(){
        $this->submissions=null;
        $this->attemps=null;
        $this->scoreof_solved_problem=null;
        $this->attemps=null;
        $this->penality=0;
        $this->nbr_solved_problem=0;
    }
    public function add_submission( $submission_id){
        $submission=$this->em->getRepository()->find($submission_id);
        $verdict=$submission->getToken();
        $time=$submission->getTime();
        array_push($this->submissions,['id'=>$submission_id , 'verdict'=>$verdict,'time'=>time]);
    }
    public function calc_problem_score(){
        foreach( $this->submissions as &$sub){
            $this->attemps[sub['id']]++;
            if(sub['verdict']=="Accepted"){

                $scoreof_solved_problem[$sub['id']]=min($sub['time'],$scoreof_solved_problem[$sub['id']]);
            }
        }
    }

    public function cal_score_penality(){
        foreach($this->scoreof_solved_problem as &$score){
            if($score != null ){
                $this->penality+=score;
                $this->nbr_solved_problem++;
            }
        }
    }

}
/*

class Scoreboard{
    private submissions;
    private participants; // final list une fois t7atet matetbadlch
    public function __construct($id){
        // ta5ou el id mt3 el contest w tloadi el users eli mcherkin fiha
        //el scoreboard yet5l9 douma yetskr el ajel w9ti m3ch 7ad yajem ycherk fel contest

    }
    //lazem reload tsir automatiquement koll 30s
    function reload (){
        //n3wdou el submission nejbdouhom mel DB mel lowl w jdid
        //clear el submission mt3 koll participant
    }
}

class Scoreboard_service
{
    private $scoreboard;

    public function __construct($id)
    {
        //logique hetha
        //$this->scoreboard = $submission->get_submissions(id);
    }

    public function get_scoreboard($id )
    {
        $ans=null;
        $ans[0]=['A','B','C'];
        $
        return $ans;
    }
}
*/