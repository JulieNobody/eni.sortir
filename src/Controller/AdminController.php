<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class AdminController
 * @package App\Controller
 * @Route("/admin_")
 */
class AdminController extends AbstractController
{
    /**
     * @Route("menu", name="admin_menu")
     */
    public function index()
    {

        return $this->render('admin/menu-admin.html.twig', []);
    }
}
