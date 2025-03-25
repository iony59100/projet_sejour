<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ApiController extends AbstractController
{
    #[Route('/api', name: 'api', methods: "GET")]
    public function getData()
    {
        return $this->json(['data' => 'Hello, World!']);
    }
}