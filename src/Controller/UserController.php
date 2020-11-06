<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * @var UserRepository
     */
    private UserRepository $repository;

    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @Route ("/register", name="user_register", methods={"GET"})
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function registerPage()
    {
        return $this->render('pages/register.html.twig',[
            'title' => 'Registration'
        ]);
    }


    public function register(Request $request)
    {

    }
}
