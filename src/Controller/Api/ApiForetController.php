<?php

namespace App\Controller\Api;

use Dompdf\Dompdf;
use Knp\Snappy\Pdf;
use App\Entity\Foret;
use App\Repository\ForetRepository;
use App\Service\PdfGeneratorService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Nucleos\DompdfBundle\Wrapper\DompdfWrapperInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ApiForetController extends ApiAbstractController
{
    private $foretRepository;

    public function __construct(ForetRepository $foretRepository)
    {
        $this->foretRepository = $foretRepository;
    }

    #[Route('/api/forets', name: 'api_foret_get', methods:["GET"])]
    public function get(SerializerInterface $serializer): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        if(!$user){
            return $this->returnResponse('Not connected', Response::HTTP_UNAUTHORIZED);
        }

        $forets = $user->getForets();
        $json = $serializer->serialize($forets, 'json');

        return new Response($json, Response::HTTP_OK, array(
            'Content-Type' => 'application/json',
        ));
    }

    #[Route('/api/forets/{id}', name: 'api_foret_getone', methods:["GET"], requirements: ['id' => '\d+'])]
    public function getOne(int $id, SerializerInterface $serializer): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        if(!$user){
            return $this->returnResponse('Not connected', Response::HTTP_UNAUTHORIZED);
        }

        $foret = $this->foretRepository->find($id);
        if(!$foret){
            return $this->returnResponse('forest not found', Response::HTTP_NOT_FOUND);
        }
        if($foret->getOwner() != $this->getUser()){
            $this->returnResponse("not user's forest", Response::HTTP_FORBIDDEN);
        }

        $json = $serializer->serialize($foret, 'json');

        return new Response($json, Response::HTTP_OK, array(
            'Content-Type' => 'application/json',
        ));
    }

    #[Route('/api/forets/pdf', name: 'api_foret_pdf', methods:["GET"])]
    public function pdf(Request $request, PdfGeneratorService $pdfGeneratorService): Response
    {
        $data = json_decode($request->getContent());

        $html = $this->render('api/foret/enquete.html.twig', [
            'name' => 'domPdf' //$data->name
        ]);

        return $pdfGeneratorService->getStreamResponse($html, 'toto.pdf');
       
    }

    #[Route('/api/forets', name: 'api_foret_post', methods:["POST"])]
    public function post(Request $request, SerializerInterface $serializer): Response
    {
        $data = json_decode($request->getContent());

        $check = $this->checkForet($data);
        if($check instanceof Response){
            return $check;
        }

        $foret = $this->feedForet(new Foret(), $data);
        $this->foretRepository->persist($foret, true);

        $json = $serializer->serialize($foret, 'json');

        return new Response($json, Response::HTTP_OK, array(
            'Content-Type' => 'application/json',
        ));
    }

    #[Route('/api/forets/{id}', name: 'api_foret_puth', methods:["PUT"])]
    public function put(int $id, Request $request, SerializerInterface $serializer): Response
    {
        /** @var Foret $foret */
        $foret = $this->foretRepository->find($id);
        if(!$foret){
            return $this->returnResponse('Forest not found', Response::HTTP_NOT_FOUND);
        }
        if($foret->getOwner() != $this->getUser()){
            return $this->returnResponse("not user's forest", Response::HTTP_FORBIDDEN);
        }

        $data = json_decode($request->getContent());

        $check = $this->checkForet($data);
        if($check instanceof Response){
            return $check;
        }

        $foret = $this->feedForet($foret, $data);
        $this->foretRepository->persist($foret, true);

        $json = $serializer->serialize($foret, 'json');

        return new Response($json, Response::HTTP_OK, array(
            'Content-Type' => 'application/json',
        ));
    }

    #[Route('/api/forets/{id}', name: 'api_foret_delete', methods:["DELETE"])]
    public function delete(int $id): Response
    {
        $foret = $this->foretRepository->find($id);
        if(!$foret){
            return $this->returnResponse('Forest not found', Response::HTTP_NOT_FOUND);
        }
        if($foret->getOwner() != $this->getUser()){
            return $this->returnResponse("not user's forest", Response::HTTP_FORBIDDEN);
        }

        $this->foretRepository->remove($foret);

        return new Response('deleted', Response::HTTP_NO_CONTENT);
    }

    private function checkForet($data){
        if(!$data){
            return $this->returnResponse('Request body not valid', Response::HTTP_BAD_REQUEST);
        }

        if(!isset($data->name) || !$data->name ){
            return $this->returnResponse('Parameter "name" is required and cannot be empty', Response::HTTP_BAD_REQUEST);
        }

        if(!isset($data->image_url) || !filter_var($data->image_url, FILTER_VALIDATE_URL) 
        ){
            return $this->returnResponse('Parameter "image_url" is required, cannot be empty and must be a valid url', Response::HTTP_BAD_REQUEST);
        }

        if(!isset($data->area) || !intval($data->area)){
            return $this->returnResponse('Parameter "area" is required and be an integer > 0', Response::HTTP_BAD_REQUEST);
        }

        if(!isset($data->tags) || !is_array($data->tags)){
            return $this->returnResponse('Parameter "tags" is required and must be an array', Response::HTTP_BAD_REQUEST);
        }

        if(!isset($data->parcels) || !is_array($data->parcels)){
            return $this->returnResponse('Parameter "parcels" is required and must be an array', Response::HTTP_BAD_REQUEST);
        }

        /*regex wkt multipolygone https://stackoverflow.com/questions/75529623/regex-for-multipolygon-wkt */
        $regex = '/^MULTIPOLYGON\s*\(\(\((-?\d+(?:\.\d+)*\ -?\d+(?:\.\d+)*)(?:,\s*\s*(?1))*\)(?:,\s*\((?1)(?:,\s*\s*(?1))*\))*\)(?:,\s*\(\((?1)(?:,\s*\s*(?1))*\)(?:,\s*\((?1)(?:,\s*\s*(?1))*\))*\))*\)$/';
        if(!isset($data->geometry) || !preg_match($regex, $data->geometry)  ){
            return $this->returnResponse('Parameter "geometry" is required and must be WKT : MULTIPOLYGON (((1 5, 5 5, 5 1, 1 1, 1 5)), ((6 5, 9 1, 6 1, 6 5)))', Response::HTTP_BAD_REQUEST);
        }
    }

    private function feedForet(Foret $foret, $data){
        $foret
            ->setName($data->name)
            ->setImageUrl($data->image_url)
            ->setArea($data->area)
            ->setTags($data->tags ?: [])
            ->setParcels($data->parcels ?: [])
            ->setOwner($this->getUser())
            ->setGeometry($data->geometry)
        ;

        return $foret;
    }
}
