<?php


namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class CoursesController extends AbstractController
{
    /**
     * @Route("/courses",name="courses")
     */
    public function courses()
    {
        return $this->render('courses/courses.html.twig');

    }
    /**
     * @Route("/courses/sorting-and-search", name="sorting")
     */
    public function sorting()
    {
        return $this->render('courses/sorting_and_searching.html.twig');

    }
    /**
     * @Route("/courses/{id}",name="course_id")
     */
    public function course()
    {
        return $this->render();

    }

}