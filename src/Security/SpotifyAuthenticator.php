<?php

namespace App\Security;

use App\Entity\UserAccessKeys;
use App\Service\UserTokenPersister;
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

class SpotifyAuthenticator implements AuthenticatorInterface {

    const TOKEN_NAME = "X-AUTH-TOKEN";

    private $params;
    private $userTokenPersister;

    public function __construct(ContainerBagInterface $params, UserTokenPersister $persister) {
        $this->params = $params;
        $this->userTokenPersister = $persister;
    }

    public function createAuthenticatedToken(UserInterface $user, string $providerKey) {

        // create the access key for user and persist it
        // TODO does this need to be done here?
        $accessKey = hash('sha256', $user->getUsername() . time());

        $userAccessKey = new UserAccessKeys();
        $userAccessKey->setSpotifyUserID($user->getUsername());
        $userAccessKey->setAccessKey($accessKey);
        $userAccessKey->setExpireAt(new \DateTime("+7 days"));
        $userAccessKey->setCreatedAt(new \DateTime());
        $this->userTokenPersister->persistUserAccessKey($userAccessKey);

        $token = new PostAuthenticationGuardToken(
            $user,
            $providerKey,
            $user->getRoles()
        );

        // setting access key to use at onAuthenticationSuccess
        $token->setAttribute('accessKey', $accessKey);
        return $token;
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

        // if a User is returned, checkCredentials() is called
        return $userProvider->loadUserByUsername($authorizationCode);
    }

    public function checkCredentials($credentials, UserInterface $user) {
        // Check credentials - e.g. make sure the password is valid.
        // In case of an API token, no credential check is needed.

        // Return `true` to cause authentication success
        return true;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey) {
        // user is authenticated, return it to homepage
        $authKey = $token->getAttribute("accessKey");

        $response = new RedirectResponse("/", 302);
        $response->headers->setCookie(Cookie::create(self::TOKEN_NAME, $authKey));

        return $response;
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
