<?php

namespace App\EventSubscriber;

use App\Entity\RefreshToken;
use Doctrine\Persistence\ManagerRegistry;
use App\Repository\RefreshTokenRepository;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Http\Event\LogoutEvent;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class LogoutSubscriber implements EventSubscriberInterface
{
    private $params;
    private $urlGenerator;
    private $requestStack;
    private $doctrine;

    public function __construct(
        ParameterBagInterface $params, 
        UrlGeneratorInterface $urlGenerator, 
        ManagerRegistry $doctrine,
        RequestStack $requestStack,  )
    {
        $this->params = $params;
        $this->urlGenerator = $urlGenerator;
        $this->requestStack = $requestStack;
        $this->doctrine = $doctrine;
    }
    
    public function onLogoutEvent(LogoutEvent $event): void
    {
        if(!$event->getToken()){
            return;
        }
        $user = $event->getToken()->getUser();

        // Supprimer les refresh_tokens de la base
        /** @var RefreshTokenRepository $repository */
        $repository = $this->doctrine->getRepository(RefreshToken::class);
        $repository->removeAllForUser($user);

        // supprimer la session
        $this->requestStack->getSession()->clear();

        // TODO supprimer la connexion SSO GPF

        // la direction par défaut est "/" (sans le préfixe des routes)

    }

    public static function getSubscribedEvents(): array
    {
        return [
            LogoutEvent::class => 'onLogoutEvent',
        ];
    }
}
