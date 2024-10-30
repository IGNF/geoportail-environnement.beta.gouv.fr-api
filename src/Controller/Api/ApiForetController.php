<?php

namespace App\Controller\Api;

use App\Repository\ForetRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ApiForetController extends ApiAbstractController
{
    private $foretRepository;

    public function __construct(ForetRepository $foretRepository)
    {
        $this->foretRepository = $foretRepository;
    }

    #[Route('/api/forets', name: 'api_foret_get', methods:["GET"])]
    public function get(): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        if(!$user){
            return $this->returnResponse('Not connected', Response::HTTP_UNAUTHORIZED);
        }

        return new JsonResponse();
    }

    #[Route('/api/forets/{id}', name: 'api_foret_getone', methods:["GET"])]
    public function getOne($id): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        if(!$user){
            return $this->returnResponse('Not connected', Response::HTTP_UNAUTHORIZED);
        }

        $foret = $this->foretRepository->find($id);
        
        if(!$foret){
            return $this->returnResponse('Not found', Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse();
    }

    #[Route('/api/forets/', name: 'api_foret_getone', methods:["POST"])]
    public function patch(): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        if(!$user){
            return $this->returnResponse('Not connected', Response::HTTP_UNAUTHORIZED);
        }

        return new JsonResponse();
    }

    #[Route('/api/forets/{id}', name: 'api_foret_patch', methods:["PATCH"])]
    public function post(): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        if(!$user){
            return $this->returnResponse('Not connected', Response::HTTP_UNAUTHORIZED);
        }

        return new JsonResponse();
    }

    #[Route('/api/forets/{id}', name: 'api_foret_delete', methods:["DELETE"])]
    public function delete(): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        if(!$user){
            return $this->returnResponse('Not connected', Response::HTTP_UNAUTHORIZED);
        }

        return new JsonResponse();
    }
}
