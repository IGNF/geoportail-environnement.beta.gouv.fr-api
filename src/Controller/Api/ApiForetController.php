<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ApiForetController extends AbstractController
{
    #[Route('/api/foret', name: 'app_api_foret')]
    public function index(): Response
    {
        return $this->render('api/foret/index.html.twig', [
            'controller_name' => 'ForetController',
        ]);
    }
}
