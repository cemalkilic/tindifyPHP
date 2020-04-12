<?php

namespace App\Repository;

use App\Entity\UserSpotifyTokens;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserSpotifyTokensRepository|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserSpotifyTokensRepository|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserSpotifyTokensRepository[]    findAll()
 * @method UserSpotifyTokensRepository[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserSpotifyTokensRepository extends ServiceEntityRepository {

    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, UserSpotifyTokens::class);
    }

    // /**
    //  * @return UserTokens[] Returns an array of UserTokens objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?UserTokens
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
