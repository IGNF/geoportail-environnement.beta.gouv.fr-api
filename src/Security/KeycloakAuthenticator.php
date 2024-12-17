<?php

namespace App\Security\Authenticator;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class KeycloakAuthenticator extends AbstractAuthenticator
{
    use TargetPathTrait;

    // public const LOGIN_ROUTE = 'security_login';
    public const LOGIN_CHECK_ROUTE = 'security_login_check';
    public const SUCCESS_ROUTE = 'security_connected';
    // public const HOME_ROUTE = 'app_home';

    private $urlGenerator;
    private $userRepository;
    private $params;

    public function __construct(
        UrlGeneratorInterface $urlGenerator, 
        UserRepository $userRepository,
        ParameterBagInterface $params
    )
    {
        $this->urlGenerator = $urlGenerator;
        $this->userRepository = $userRepository;
        $this->params = $params;
    }

    /**
     * Called on every request to decide if this authenticator should be
     * used for the request. Returning `false` will cause this authenticator
     * to be skipped.
     */
    public function supports(Request $request): ?bool
    {
        return self::LOGIN_CHECK_ROUTE === $request->attributes->get('_route');
    }

    public function authenticate(Request $request): Passport
    {
        $url = $this->params->get('iam_url').'/token';
        $redirectUri = $this->urlGenerator->generate('security_login_check', [], UrlGeneratorInterface::ABSOLUTE_URL);
        $redirectUri = preg_replace('/^http:/', 'https:', $redirectUri);
        
        $data = [
            'client_id' => $this->params->get('iam_client_id'), 
            'client_secret' => $this->params->get('iam_client_secret'), 
            'code' => $request->query->get('code'),
            'redirect_uri' => $redirectUri,
            'grant_type' => 'authorization_code',
        ];

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER , true);
        curl_setopt($curl, CURLOPT_POSTFIELDS , http_build_query($data));
        curl_setopt($curl, CURLOPT_HTTPHEADER , ['Content-type: application/x-www-form-urlencoded', 'Accept: application/json']);
        $response = json_decode(curl_exec($curl));
        curl_close($curl);

        $jwtToken = $response->access_token;
        $infos = json_decode(base64_decode(str_replace('_', '/', str_replace('-','+',explode('.', $jwtToken)[1]))));
        $email = $infos->email;

        return new SelfValidatingPassport(
            new UserBadge($email, function(string $userIdentifier){
                $user = $this->userRepository->findOneBy(['email' => $userIdentifier]);
                if(!$user){
                    $user = new User();
                    $user->setEmail($userIdentifier);
                    $user->setLastLogin(new \DateTimeImmutable());
                    $user->setPassword(base64_encode(random_bytes(32)));
                    $this->userRepository->persist($user);
                }

                return $user;
            }) 
        );
    }

    
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey): ?Response
    {
        $targetPath = $this->getTargetPath($request->getSession(), $providerKey);
        if ($targetPath) {
            return new RedirectResponse($targetPath);
        }
        return new RedirectResponse($this->urlGenerator->generate(self::SUCCESS_ROUTE));

    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $message = $exception->getMessage() ?? "An error occured during authentication";

        return new RedirectResponse($this->urlGenerator->generate(self::SUCCESS_ROUTE, ['error_flash' => $message]));
    }

    public function supportsRememberMe(): bool
    {
        return false;
    }
}
