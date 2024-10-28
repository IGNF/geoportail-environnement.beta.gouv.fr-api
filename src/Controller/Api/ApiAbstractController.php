<?php

namespace App\Controller\Api;

use stdClass;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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
