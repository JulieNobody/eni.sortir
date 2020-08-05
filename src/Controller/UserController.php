<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{

    /**
     * @Route("/login", name="user_login")
     */
    public function login() {

        return $this->render('user/login');
    }

    /**
     * Symfony gère entièrement cette route
     * @Route("/logout", name="user_logout")
     */
    public function logout() {}







}
