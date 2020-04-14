<?php

namespace App\Security;

use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AuthenticatorInterface;
use Symfony\Component\Security\Guard\Token\PostAuthenticationGuardToken;

class Authenticator implements AuthenticatorInterface {

    // TODO: Will be used later
    const TOKEN_NAME = "X-AUTH-TOKEN";

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
        return $request->headers->has(self::TOKEN_NAME) || $request->cookies->has(self::TOKEN_NAME);
    }

    /**
     * Called on every request. Return whatever credentials you want to
     * be passed to getUser() as $credentials.
     */
    public function getCredentials(Request $request) {
        return $request->headers->get(self::TOKEN_NAME) ?? $request->cookies->get(self::TOKEN_NAME);
    }

    public function getUser($credentials, UserProviderInterface $userProvider) {
        if (null === $credentials) {
            // The token header was empty, authentication fails with HTTP Status
            // Code 401 "Unauthorized"
            return null;
        }

        return $userProvider->loadUserByUsername($credentials);
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
        $errorDetails = $exception->getMessage() ?? $exception->getMessageKey();

        return new JsonResponse($errorDetails, Response::HTTP_UNAUTHORIZED);
    }

    /**
     * Called when authentication is needed, but it's not sent
     */
    public function start(Request $request, AuthenticationException $authException = null) {
        $errorDetails = "Please visit the '/auth' link to login with your Spotify account!";
        return JsonResponse::create($errorDetails, JsonResponse::HTTP_UNAUTHORIZED);
    }

    public function supportsRememberMe() {
        return false;
    }
}
