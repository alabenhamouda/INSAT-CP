<?php

namespace App\Controller;

use App\Entity\Status;
use App\Entity\User;
use App\Form\UserType;
use App\Security\LoginFormAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class HomeController extends AbstractController
{
    /**
     * @Route("/add_status",name="add_status")
     */
    public function added(EntityManagerInterface $manager)
    {

        $descriptions = [
            "In Queue", "Processing", "Accepted", "Wrong Answer",
            "Time Limit Exceeded", "Compilation Error", "Runtime Error (SIGSEGV)",
            "Runtime Error (SIGXFSZ)", "Runtime Error (SIGFPE)", "Runtime Error (SIGABRT)",
            "Runtime Error (NZEC)", "Runtime Error (Other)", "Internal Error",
            "Exec Format Error"
        ];
        for ($i = 0; $i < count($descriptions); $i++) {
            $status = new Status();
            $status->setDescription($descriptions[$i])
                ->setCode($i + 1);
            $manager->persist($status);
        }
        $manager->flush();
        return $this->render('home/index.html.twig');

    }
    /**
     * @Route("/", name="home")
     */
    public function index(): Response
    {
        return $this->render('home/index.html.twig');
    }

    /**
     * @Route("/{action<(login|signup)>}",name="auth")
     */
    public function signup($action, AuthenticationUtils $authenticationUtils)
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('home');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        $form = $this->createform(usertype::class);
        return $this->render('home/login.html.twig', [
            'signup' => ($action == 'signup'),
            'form' => $form->createView(),
            'last_username' => $lastUsername,
            'error' => $error
        ]);
    }

    /**
     * @Route("/register", name="register", methods={"POST"})
     */
    public function register(Request $request, UserPasswordEncoderInterface $encoder, EntityManagerInterface $entity,LoginFormAuthenticator $login,GuardAuthenticatorHandler $guard)
    {
        $user = new user();
        $form = $this->createform(usertype::class, $user);
        $form->handlerequest($request);
        if ($form->issubmitted() && $form->isvalid()) {
            $user->setpassword($encoder->encodepassword($user, $user->getpassword()));
            $entity->persist($user);
            $entity->flush();
            return $guard->authenticateUserAndHandleSuccess($user,$request,$login,'main');
        }
        return $this->render('home/login.html.twig', [
            'signup' => 'signup',
            'form' => $form->createView(),
            'last_username' => "",
            'error' => null
        ]);
    }


}
