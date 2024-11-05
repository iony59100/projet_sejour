<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PrincipalController extends AbstractController
{
    #[Route('/principal', name: 'app_principal')]
    public function index(): Response
    {
        return $this->render('principal/index.html.twig', [
            'controller_name' => 'PrincipalController',
        ]);
    }
    #[Route('/infirmier', name: 'infirmier')]
    public function index2(): Response
    {
        return $this->render('infirmier/infirmier.html.twig', [
            'controller_name' => 'PrincipalController',
        ]);
    }
    #[Route('/admin', name: 'admin')]
    public function index3(): Response
    {
        return $this->render('admin/admin.html.twig', [
            'controller_name' => 'PrincipalController',
        ]);
    }
}
