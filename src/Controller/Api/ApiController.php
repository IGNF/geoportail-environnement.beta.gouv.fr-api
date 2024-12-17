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

    #[OA\Post(
        tags: ["Login"],
        path: '/api/login',
        description: "Login via l'API - n'utiliser que pour les développements",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "username", type: "string", example: "email de l'utilisateur (foo@bar.fr)"),
                    new OA\Property(property: "password", type: "string", example: "password de l'utilisateur")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200, 
                description: "tokens de l'utilisateur",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "token", type: "string", example: "JWT token lié à l'utilisateur"),
                        new OA\Property(property: "refresh_token", type: "string", example: "password de l'utilisateur")
                    ]
                )
            ),
            new OA\Response(
                response: 401, 
                description: "bad credentials",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "code", type: "integer", example: "401"),
                        new OA\Property(property: "message", type: "string", example: "bad credentials")
                    ]
                )
            )
        ]
    )]

    #[OA\Post(
        tags: ["Login"],
        path: '/api/token/refresh',
        description: "Redonne un JWT token",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "refresh_token", type: "string", example: "refresh_token de l'utilisateur")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200, 
                description: "tokens de l'utilisateur",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "token", type: "string", example: "JWT token lié à l'utilisateur"),
                        new OA\Property(property: "refresh_token", type: "string", example: "password de l'utilisateur")
                    ]
                )
            ),
            new OA\Response(
                response: 401, 
                description: "bad credentials",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "code", type: "integer", example: "401"),
                        new OA\Property(property: "message", type: "string", example: "An authentication exception occurred")
                    ]
                )
            )
        ]
    )]
    #[Route(path: '/api/login', name: 'api_login')]
    public function apiLogin(): Response
    {
        // intercepté par JWTAuthenticator, utilisé uniquement en localhost, après avoir généré des users
        return new Response('ok');
    }
}
