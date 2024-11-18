<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ApiController extends AbstractController
{
    #[Route('/api', name: 'api_index')]
    public function documentation(): Response
    {
        return $this->render('api/documentation.html.twig', [
            'controller_name' => 'ApiController',
        ]);
    }

    #[Route('/api/test', name: 'api_test')]
    public function test(): Response
    {
        return new Response('ça marche', Response::HTTP_OK);
    }
}
