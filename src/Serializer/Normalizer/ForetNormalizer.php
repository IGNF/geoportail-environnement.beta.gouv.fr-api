<?php

namespace App\Serializer\Normalizer;

use App\Entity\Foret;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use OpenApi\Attributes as OA;

class ForetNormalizer implements NormalizerInterface
{
    public function __construct(
        #[Autowire(service: 'serializer.normalizer.object')]
        private NormalizerInterface $normalizer
    ) {
    }

    #[OA\Schema(
        schema: "foret_input",
        properties: [
            new OA\Property(
                type: "string", 
                property: "name", 
                description: "Nom de la foret",
            ),
            new OA\Property(
                type: "number", 
                property: "area", 
                description: "aire de la foret, exprimée en xx",
            ),
            new OA\Property(
                type: "string", 
                property: "image_url", 
                description: "image d'illustration de la foret",
                format: "uri",
            ),
            new OA\Property(
                type: "array", 
                property: "parcels", 
                description: "parcelles cadastrales intersectant la foret",
                items: new OA\Items(type: "string"),
            ),
            new OA\Property(
                type: "array", 
                property: "tags", 
                description: "tags concernant la foret",
                items: new OA\Items(type: "string"),
            ),
            new OA\Property(
                type: "string", 
                property: "geometry", 
                description: "géométrie de la foret",
            ),
        ]
    )]

    #[OA\Schema(
        schema: "foret_output",
        allOf: [ 
            new OA\Schema( ref: "#/components/schemas/foret_input"),
        ],
        properties: [
            new OA\Property(
                type: "string", 
                property: "owner_email", 
                description: "email de l'utilisateur",
                format: "email",
            ),
            new OA\Property(
                type: "integer", 
                property: "id", 
                description: "Identifiant de la foret",
            ),
            new OA\Property(
                type: "string", 
                property: "created_at", 
                description: "date de création du compte de l'utilisateur",
                format: "date-time",
            ),
            new OA\Property(
                type: "string", 
                property: "updated_at", 
                description: "date de création du compte de l'utilisateur",
                format: "date-time",
            ),
        ]
    )]
    public function normalize($foret, ?string $format = null, array $context = []): array
    {
        /** @var Foret $foret */
        $data = [];
        $data['name'] = $foret->getName();
        $data['area'] = $foret->getArea();
        $data['id'] = $foret->getId();
        $data['img_url'] = $foret->getImageUrl();
        $data['owner_email'] = $foret->getOwner()->getEmail();
        $data['parcels'] = $foret->getParcels();
        $data['tags'] = $foret->getTags();
        $data['geometry'] = $foret->getGeometry();
        $data['created_at'] = $foret->getCreatedAt()->format(\DateTimeInterface::W3C);
        $data['updated_at'] = $foret->getUpdatedAt()->format(\DateTimeInterface::W3C);
        
        return $data;
    }

    public function supportsNormalization($data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof Foret;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [Foret::class => true];
    }
}
