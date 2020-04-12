<?php

namespace App\Security;

use App\Entity\UserSpotifyTokens;
use App\Model\User;
use App\Service\SpotifyTokenPersister;
use SpotifyWebAPI\SpotifyWebAPI;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class SpotifyUserProvider implements UserProviderInterface {

    private $params;
    private $spotifyTokenPersister;

    /**
     * UserProvider constructor.
     */
    public function __construct(ContainerBagInterface $params, SpotifyTokenPersister $spotifyTokenPersister) {
        $this->params = $params;
        $this->spotifyTokenPersister = $spotifyTokenPersister;
    }

    /**
     * Symfony calls this method if you use features like switch_user
     * or remember_me.
     *
     * If you're not using these features, you do not need to implement
     * this method.
     *
     * @return UserInterface
     *
     * @throws UsernameNotFoundException if the user is not found
     */
    public function loadUserByUsername($authorizationCode) {
        if (empty($authorizationCode)) {
            return null;
        }

        $spotifySession = new \SpotifyWebAPI\Session(
            $this->params->get('spotifyApi.clientID'),
            $this->params->get('spotifyApi.clientSecret'),
            $this->params->get('spotifyApi.redirectUri')
        );

        $spotifySession->requestAccessToken($authorizationCode);
        $accessToken = $spotifySession->getAccessToken();
        $refreshToken = $spotifySession->getRefreshToken();
        $tokenExpire = $spotifySession->getTokenExpiration();

        $spotifyApi = new SpotifyWebAPI();
        $spotifyApi->setAccessToken($accessToken);

        $userDetails = $spotifyApi->me();

        $spotifyTokens = new UserSpotifyTokens();
        $spotifyTokens->setAccessToken($accessToken);
        $spotifyTokens->setRefreshToken($refreshToken);
        $spotifyTokens->setTokenExpire(new \DateTime("@" . $tokenExpire));
        $spotifyTokens->setCreatedAt(new \DateTime());
        $spotifyTokens->setSpotifyUserID($userDetails->id);

        $this->spotifyTokenPersister->persistSpotifyTokens($spotifyTokens);

        $authedUser = new User($userDetails->id, $accessToken);
        $authedUser->setDisplayName($userDetails->display_name);
        return $authedUser;
    }

    /**
     * Refreshes the user after being reloaded from the session.
     *
     * When a user is logged in, at the beginning of each request, the
     * User object is loaded from the session and then this method is
     * called. Your job is to make sure the user's data is still fresh by,
     * for example, re-querying for fresh User data.
     *
     * If your firewall is "stateless: true" (for a pure API), this
     * method is not called.
     *
     * @return UserInterface
     */
    public function refreshUser(UserInterface $user) {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Invalid user class "%s".', get_class($user)));
        }
        // This function is never called, at least should be!
        return $user;
    }

    /**
     * Tells Symfony to use this provider for this User class.
     */
    public function supportsClass($class) {
        return User::class === $class;
    }
}
