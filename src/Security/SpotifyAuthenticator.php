<?php

namespace App\Security;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AuthenticatorInterface;
use Symfony\Component\Security\Guard\Token\PostAuthenticationGuardToken;

class SpotifyAuthenticator implements AuthenticatorInterface {

    const TOKEN_NAME = "X-AUTH-TOKEN";
    const TOKEN_VALUE_SECRET = "veryBasicSecurity";

    private $params;
    private $em;
    private $session;

    public function __construct(ContainerBagInterface $params, EntityManagerInterface $em, SessionInterface $session) {
        $this->params = $params;
        $this->em = $em;
        $this->session = $session;
    }

    public function createAuthenticatedToken(UserInterface $user, string $providerKey) {
        return new PostAuthenticationGuardToken(
            $user,
            $providerKey,
            $user->getRoles()
        );
    }

    /**
     * Called on every request to decide if this authenticator should be
     * used for the request. Returning `false` will cause this authenticator
     * to be skipped.
     */
    public function supports(Request $request) {
        return $request->getPathInfo() === "/authCallback" && $request->query->has("code");
    }

    /**
     * Called on every request. Return whatever credentials you want to
     * be passed to getUser() as $credentials.
     */
    public function getCredentials(Request $request) {
        return $request->query->get("code", null);
    }

    public function getUser($authorizationCode, UserProviderInterface $userProvider) {
        if (null === $authorizationCode) {
            // The token header was empty, authentication fails with HTTP Status
            // Code 401 "Unauthorized"
            return null;
        }

        $user = $userProvider->loadUserByUsername($authorizationCode);
        $this->session->set('spotify_user', $user);

        // if a User is returned, checkCredentials() is called
        return $user;
    }

    public function checkCredentials($credentials, UserInterface $user) {
        // Check credentials - e.g. make sure the password is valid.
        // In case of an API token, no credential check is needed.

        // Return `true` to cause authentication success
        return true;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey) {
        // user is authenticated, return it to homepage
        return new RedirectResponse("/");
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception) {
        $errorDetails = $exception->getMessage() ?? $exception->getMessageKey();

        return new JsonResponse($errorDetails, Response::HTTP_UNAUTHORIZED);
    }

    /**
     * Called when authentication is needed, but it's not sent
     */
    public function start(Request $request, AuthenticationException $authException = null) {

        $spotifySession = new \SpotifyWebAPI\Session(
            $this->params->get('spotifyApi.clientID'),
            $this->params->get('spotifyApi.clientSecret'),
            $this->params->get('spotifyApi.redirectUri')
        );

        // user need to be authenticated
        $options = [
            'scope' => [
                'user-read-email',
                'playlist-read-private',
                'playlist-modify-private'
            ],
        ];

        return new RedirectResponse($spotifySession->getAuthorizeUrl($options));
    }

    public function supportsRememberMe() {
        return false;
    }
}
