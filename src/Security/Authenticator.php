<?php

namespace App\Security;

use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AuthenticatorInterface;
use Symfony\Component\Security\Guard\Token\PostAuthenticationGuardToken;

class Authenticator implements AuthenticatorInterface {
    private $params;

    public function __construct(ContainerBagInterface $params) {
        $this->params = $params;
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
        return $request->cookies->has('SPTFY-TOKEN');
    }

    /**
     * Called on every request. Return whatever credentials you want to
     * be passed to getUser() as $credentials.
     */
    public function getCredentials(Request $request) {
        return $request->cookies->get('SPTFY-TOKEN');
    }

    public function getUser($accessToken, UserProviderInterface $userProvider) {
        if (null === $accessToken) {
            // The token header was empty, authentication fails with HTTP Status
            // Code 401 "Unauthorized"
            return null;
        }

        // if a User is returned, checkCredentials() is called
        return $userProvider->loadUserByUsername($accessToken);;
    }

    public function checkCredentials($credentials, UserInterface $user) {
        // Check credentials - e.g. make sure the password is valid.
        // In case of an API token, no credential check is needed.

        // Return `true` to cause authentication success
        return true;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey) {
        // on success, let the request continue
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception) {
        $data = [
            // you may want to customize or obfuscate the message first
            'message' => strtr($exception->getMessageKey(), $exception->getMessageData())

            // or to translate this message
            // $this->translator->trans($exception->getMessageKey(), $exception->getMessageData())
        ];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
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

        // if user has code in the param
        // hopefully for now!
        $code = $request->query->get("code");
        if ($request->getPathInfo() === "/authCallback" && $code !== null) {
            $spotifySession->requestAccessToken($code);
            $accessToken = $spotifySession->getAccessToken();

            // TODO: redirect to welcome screen
            $response = new RedirectResponse("/playlists");
            $response->headers->setCookie(Cookie::create("SPTFY-TOKEN", $accessToken, strtotime("+1 days")));

            return $response;
        }

        // user need to be authorized

        $options = [
            'scope' => [
                'user-read-email',
                'playlist-read-private',
            ],
        ];

        return new RedirectResponse($spotifySession->getAuthorizeUrl($options));
    }

    public function supportsRememberMe() {
        return false;
    }
}
