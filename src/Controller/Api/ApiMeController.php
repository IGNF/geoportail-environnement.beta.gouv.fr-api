<?php

namespace App\Controller\Api;

use App\Repository\UserRepository;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ApiMeController extends ApiAbstractController
{
    private $userRepository;
    
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }
    
    #[Route('/api/me', name: 'app_api_me', methods:["GET"])]
    public function index(SerializerInterface $serializer): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        if(!$user){
            return $this->returnResponse('Not connected', Response::HTTP_UNAUTHORIZED);
        }

        $json = $serializer->serialize($user, 'json', [
            DateTimeNormalizer::FORMAT_KEY => 'Y-m-d\TH:i:sP',
        ]);

        return new Response($json, Response::HTTP_OK, array(
            'Content-Type' => 'application/json',
        ));
    }
}
