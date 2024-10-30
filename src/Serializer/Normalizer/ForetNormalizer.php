<?php

namespace App\Serializer\Normalizer;

use App\Entity\Foret;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ForetNormalizer implements NormalizerInterface
{
    public function __construct(
        #[Autowire(service: 'serializer.normalizer.object')]
        private NormalizerInterface $normalizer
    ) {
    }

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
