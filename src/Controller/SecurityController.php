<?php

namespace App\Controller;

use App\Entity\RefreshToken;
use App\Repository\RefreshTokenRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;

class SecurityController extends AbstractController
{
    private $refreshTokenRepository;

    public function __construct(RefreshTokenRepository $refreshTokenRepository)
    {
        $this->refreshTokenRepository = $refreshTokenRepository;
    }

    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route(path: '/api/login', name: 'api_login')]
    public function apiLogin(): Response
    {
        return new Response('ok');
    }

    #[Route(path: '/vous-etes-connecte', name: 'app_connected')]
    public function connected():Response
    {
        return $this->render('security/connected.html.twig');
    }

    /**
     * cette route permet de recevoir les tokens lorsqu'on est connectés sur le site via une session
     */
    #[Route(path: '/session-token', name: 'app_session_token')]
    public function sessionToken(JWTTokenManagerInterface $jwtManager ): Response
    {
        $user = $this->getUser();
        if(!$user){
            return new Response('no user', Response::HTTP_UNAUTHORIZED);
        }

        $refreshToken = $this->generateRefreshToken($user);
        return new JsonResponse(array(
            'token' => $jwtManager->create($user),
            'refresh_token' => $refreshToken->getRefreshToken(),
        ));
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    private function generateRefreshToken($user){
        $refreshToken = new RefreshToken();
        $refreshToken->setUsername($user->getId());
        
        $datetime = new \DateTime();
        $ttl = $this->getParameter('gesdinet_jwt_refresh_token.ttl');
        $datetime->modify('+'.$ttl.' seconds');
        $refreshToken->setValid($datetime);

        $token = '';
        do{
            $token = bin2hex(openssl_random_pseudo_bytes(64));
            $existingToken = $this->refreshTokenRepository->findOneBy(array('refreshToken' => $token));
        }while($existingToken);
        $refreshToken->setRefreshToken($token);

        $this->refreshTokenRepository->persist($refreshToken);

        return $refreshToken;
    }
}
