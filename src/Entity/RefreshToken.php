<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\RefreshTokenRepository;
use Gesdinet\JWTRefreshTokenBundle\Entity\RefreshToken as BaseRefreshToken;

#[ORM\Table(name: 'refresh_tokens')]
#[ORM\Entity(repositoryClass: RefreshTokenRepository::class)]
class RefreshToken extends BaseRefreshToken
{
}
