<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;


#[OA\Info(
    title:"API géoportail de l'environnement", 
    version:"1.0"
)]
#[OA\Server(
    url: "https://TODO_PROD.ign.fr/api",
    description: "Permet l'accès aux données du GPE"
)]
#[OA\Server(
    url: "https://qlf-gpe.ign.fr/api",
    description: "Permet l'accès aux données du GPE sur serveur qlf"
)]
#[OA\Server(
    url: "https://gpe.mut-dev.ign.fr/api",
    description: "Permet l'accès aux données du GPE sur serveur mut-dev"
)]

#[OA\SecurityScheme(
    type: "apiKey",
    securityScheme: "bearer",
    bearerFormat: "JWT",
    in: "header",
    name: "Authorization",
    scheme: "Bearer",
)]

class ApiController extends AbstractController
{
    #[Route('/api', name: 'api_index')]
    public function documentation(): Response
    {
        return $this->render('api/documentation.html.twig', 
            []
        );
    }
}
