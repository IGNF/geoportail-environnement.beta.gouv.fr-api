<?php

namespace App\Serializer\Normalizer;

use App\Entity\User;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class UserNormalizer implements NormalizerInterface
{
    public function __construct(
        #[Autowire(service: 'serializer.normalizer.object')]
        private NormalizerInterface $normalizer
    ) {
    }

    public function normalize($user, ?string $format = null, array $context = []): array
    {
        /** @var User $user */
        $data = [];
        // if($object instanceof User){
            $data['email'] = $user->getEmail();
            $data['created_at'] = $user->getCreatedAt()->format(\DateTimeInterface::W3C);
        // }
        // $data = $this->normalizer->normalize($object, $format, $context);

        // TODO: add, edit, or delete some data

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
