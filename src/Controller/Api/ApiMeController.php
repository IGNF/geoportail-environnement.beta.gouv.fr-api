<?php

namespace App\Controller\Api;

use App\Repository\UserRepository;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

class ApiMeController extends ApiAbstractController
{
    private $userRepository;
    
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }
    
    #[Route('/api/me', name: 'app_api_me', methods:["GET"])]
    #[OA\Get(
        path: '/api/me',
        tags: ["User"],
        security: ["bearer"],
        responses: [
            new OA\Response(
                response: 200, 
                description: "données de l'utilisateur connecté",
                content: new OA\JsonContent(ref: "#/components/schemas/user")
            ),
            new OA\Response(response: 401, ref: "#/components/responses/NotConnected"),
        ]
    )]
    public function index(SerializerInterface $serializer): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        if(!$user){
            return $this->returnResponse('Not connected', Response::HTTP_UNAUTHORIZED);
        }

        $json = $serializer->serialize($user, 'json');

        return new Response($json, Response::HTTP_OK, array(
            'Content-Type' => 'application/json',
        ));
    }
}
