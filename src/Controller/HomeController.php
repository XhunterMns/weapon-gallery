<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


final class HomeController extends AbstractController
{
    #[Route('/home', name: 'home')]
    public function index()
    {
        return $this->redirectToRoute('add_article');
    }
    #[Route('/admin/dashboard', name: 'admin_dashboard')]
    public function admin(): Response
    {
        return $this->redirectToRoute('add_article');
    }
}
