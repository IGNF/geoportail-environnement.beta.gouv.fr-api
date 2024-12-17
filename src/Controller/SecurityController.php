<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\RefreshToken;
use App\Repository\RefreshTokenRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
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

    #[Route(path: '/login', name: 'security_login')]
    public function login(UrlGeneratorInterface $urlGenerator): Response
    {
        $keycloakUrl = $this->getParameter('iam_url').'/auth';
        $redirectUri = $urlGenerator->generate('security_login_check', [], UrlGeneratorInterface::ABSOLUTE_URL);
        $redirectUri = preg_replace('/^http:/', 'https:', $redirectUri);

        $params = [
            'client_id' => $this->getParameter('iam_client_id'),
            'response_type' => 'code',
            'scope' => 'openid profile email',
            'redirect_uri' => $redirectUri,
            'nonce' => $this->generateUid(),
        ];

        $url = $keycloakUrl.'?'.http_build_query($params);

        return new RedirectResponse($url);
    }

    #[Route(path: '/login/check', name: 'security_login_check')]
    public function loginCheck()
    {
        // intercepté par KeycloakAuthenticator
        return new Response('ok');
    }

    /*
     * stocke les jetons JWT dans le localstorage
     */
    #[Route(path: '/vous-etes-connecte', name: 'security_connected')]
    public function connected():Response
    {
        return $this->render('security/connected.html.twig');
    }

    /**
     * permet de recevoir les tokens lorsqu'on est connectés sur le site via une session
     */
    #[Route(path: '/session-token', name: 'security_session_token')]
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

    #[Route(path: '/logout', name: 'security_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    /**
     * Générer un UID.
     *
     * @param int $length
     *
     * @return string
     */
    public static function generateUid($length = 16)
    {
        $randomUid = '';

        for ($i = 0; $i < $length; ++$i) {
            if (1 == random_int(1, 2)) {
                // un chiffre entre 0 et 9
                $randomUid .= chr(random_int(48, 57));
            } else {
                // une lettre minuscule entre a et z
                $randomUid .= chr(random_int(97, 122));
            }
        }

        return $randomUid;
    }

    private function generateRefreshToken(User $user){
        $refreshToken = new RefreshToken();
        $refreshToken->setUsername($user->getUserIdentifier());
        
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
