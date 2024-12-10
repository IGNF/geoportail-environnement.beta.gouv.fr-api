<?php

namespace App\Controller\Api;

use stdClass;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use OpenApi\Attributes as OA;

#[OA\Parameter(
    name: "id",
    in: "path",
    description: "Identifiant de la ressource",
    required: true,
    schema: new OA\Schema(type: "integer")
)]

#[OA\Response(
    response: "Deleted",
    description: "La ressource a été supprimée",
    content: new OA\JsonContent(
        properties: [
            new OA\Property(property: "code", type: "integer", example: "204"),
            new OA\Property(property: "message", type: "string", example: "La ressource a été supprimée")
        ]
    )
)]
#[OA\Response(
    response: "BadRequest",
    description: "La requête n'est pas correcte",
    content: new OA\JsonContent(
        properties: [
            new OA\Property(property: "code", type: "integer", example: "400"),
            new OA\Property(property: "message", type: "string", example: "La requête n'est pas correcte")
        ]
    )
)]
#[OA\Response(
    response: "NotConnected",
    description: "Vous devez être connecté",
    content: new OA\JsonContent(
        properties: [
            new OA\Property(property: "code", type: "integer", example: "401"),
            new OA\Property(property: "message", type: "string", example: "Vous devez être connecté")
        ]
    )
)]
#[OA\Response(
    response: "Forbidden",
    description: "Vous n'avez pas accès à cette ressource",
    content: new OA\JsonContent(
        properties: [
            new OA\Property(property: "code", type: "integer", example: "403"),
            new OA\Property(property: "message", type: "string", example: "Vous n'avez pas accès à cette ressource")
        ]
    )
)]
#[OA\Response(
    response: "NotFound",
    description: "La resource n'existe pas",
    content: new OA\JsonContent(
        properties: [
            new OA\Property(property: "code", type: "integer", example: "404"),
            new OA\Property(property: "message", type: "string", example: "La resource n'existe pas")
        ]
    )
)]

class ApiAbstractController extends AbstractController
{

    /**
     * Crée une reponse avec un json, à utiliser principalement pour les retours en 400
     *
     * @param String $message
     * @param Int $code
     * @return Response
     */
    protected function returnResponse(String $message, Int $code):Response
    {
        $responseObject = new stdClass();
        $responseObject->code = $code;
        $responseObject->message = $message;

        return new JsonResponse($responseObject, $code);
    }
}
