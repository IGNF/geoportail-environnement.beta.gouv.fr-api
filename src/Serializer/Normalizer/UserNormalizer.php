<?php

namespace App\Serializer\Normalizer;

use App\Entity\User;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use OpenApi\Attributes as OA;

class UserNormalizer implements NormalizerInterface
{
    public function __construct(
        #[Autowire(service: 'serializer.normalizer.object')]
        private NormalizerInterface $normalizer
    ) {
    }

    #[OA\Schema(
        schema: "user",
        properties: [
            new OA\Property(
                type: "string", 
                property: "email", 
                description: "email de l'utilisateur, ne sera jamais modifié",
                format: "email",
            ),
            new OA\Property(
                type: "string", 
                property: "created_at", 
                description: "date de création du compte de l'utilisateur",
                format: "date-time",
            ),
            new OA\Property(
                type: "string", 
                property: "last_login", 
                description: "date de dernière connexion de l'utilisateur",
                format: "date-time",
            ),
        ]
    )]
    public function normalize($user, ?string $format = null, array $context = []): array
    {
        /** @var User $user */
        $data = [];
        $data['email'] = $user->getEmail();
        $data['created_at'] = $user->getCreatedAt()->format(\DateTimeInterface::W3C);
        $data['last_login'] = $user->getLastLogin()->format(\DateTimeInterface::W3C);
        
        return $data;
    }

    public function supportsNormalization($data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof User;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [User::class => true];
    }
}
