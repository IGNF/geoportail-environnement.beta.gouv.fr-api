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
use Symfony\Component\HttpFoundation\RedirectResponse;

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
        
        // Supprimer les refresh_tokens de la base
        $user = $event->getToken()->getUser();
        /** @var RefreshTokenRepository $repository */
        $repository = $this->doctrine->getRepository(RefreshToken::class);
        $repository->removeAllForUser($user);

        // supprimer la session
        $this->requestStack->getSession()->clear();

        // supprimer la connexion SSO GPF
        $keycloakUrl = $this->params->get('iam_url').'/logout';
        $params = [
            'client_id' => $this->params->get('iam_client_id'),
            'post_logout_redirect_uri' => $this->params->get('iam_post_logout_redirect_uri'),
        ];
        $url = $keycloakUrl.'?'.http_build_query($params);
        
        $event->setResponse(new RedirectResponse($url));

    }

    public static function getSubscribedEvents(): array
    {
        return [
            LogoutEvent::class => 'onLogoutEvent',
        ];
    }
}
