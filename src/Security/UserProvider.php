<?php

namespace App\Security;

use App\Entity\UserAccessKeys;
use App\Entity\UserSpotifyTokens;
use App\Model\User;
use App\Service\UserTokenPersister;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class UserProvider implements UserProviderInterface {

    const TOKEN_REFRESH_THRESHOLD = 10; // minutes

    private $em;
    private $params;

    /**
     * UserProvider constructor.
     */
    public function __construct(EntityManagerInterface $em, ContainerBagInterface $params) {
        $this->em = $em;
        $this->params = $params;
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
    public function loadUserByUsername($accessKey) {
        if (empty($accessKey)) {
            return null;
        }

        // check if user exist with the given token
        $existingUserAccessKeys = $this->em->getRepository(UserAccessKeys::class)->find($accessKey);
        if (!isset($existingUserAccessKeys)) {
            throw new UsernameNotFoundException("No user found with the given token!");
        }

        // need to get the spotify access token for the user
        $userID = $existingUserAccessKeys->getSpotifyUserID();
        $userSpotifyTokens = $this->em->getRepository(UserSpotifyTokens::class)->find($userID);
        if (!isset($userSpotifyTokens)) {
            throw new UsernameNotFoundException("No spotify token found for the user!");
        }

        return new User($userID, $userSpotifyTokens->getAccessToken());
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

        // check if current token is about to expire
        $spotifyUserID = $user->getUsername();

        $spotifyTokens = $this->em->getRepository(UserSpotifyTokens::class)->find($spotifyUserID);

        if (!isset($spotifyTokens)) {
            throw new UnsupportedUserException("No spotify keys found!");
        }

        $currentTime = new \DateTime();
        $diff = $currentTime->diff($spotifyTokens->getTokenExpire());

        // if there is less than 10 minutes or token has already expired,
        // let's refresh the token
        if ($diff->invert || $diff->i < self::TOKEN_REFRESH_THRESHOLD) {
            // refresh the token and store the new ones

            $spotifySession = new \SpotifyWebAPI\Session(
                $this->params->get('spotifyApi.clientID'),
                $this->params->get('spotifyApi.clientSecret'),
                $this->params->get('spotifyApi.redirectUri')
            );

            $isRefreshed = $spotifySession->refreshAccessToken($spotifyTokens->getRefreshToken());
            if (!$isRefreshed) {
                // TODO no need to throw exception but fine for now
                throw new UsernameNotFoundException("Can not refresh the tokens!");
            }

            $spotifyTokens = new UserSpotifyTokens();
            $spotifyTokens->setSpotifyUserID($spotifyUserID);
            $spotifyTokens->setAccessToken($spotifySession->getAccessToken());
            $spotifyTokens->setTokenExpire(new \DateTime('@' . $spotifySession->getTokenExpiration()));
            $spotifyTokens->setRefreshToken($spotifySession->getRefreshToken());
            $spotifyTokens->setCreatedAt(new \DateTime());

            $userTokenPersister = new UserTokenPersister($this->em);
            $userTokenPersister->persistSpotifyTokens($spotifyTokens);

            $user->setAccessToken($spotifySession->getAccessToken());
        }

        return $user;
    }

    /**
     * Tells Symfony to use this provider for this User class.
     */
    public function supportsClass($class) {
        return User::class === $class;
    }
}
