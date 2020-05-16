<?php

namespace App\Repository;

use App\Entity\TindifyPlaylist;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TindifyPlaylist|null find($id, $lockMode = null, $lockVersion = null)
 * @method TindifyPlaylist|null findOneBy(array $criteria, array $orderBy = null)
 * @method TindifyPlaylist[]    findAll()
 * @method TindifyPlaylist[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TindifyPlaylistRepository extends ServiceEntityRepository {
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, TindifyPlaylist::class);
    }

    public function findOneByUsername($username): ?TindifyPlaylist {
        return $this->createQueryBuilder('t')
            ->andWhere('t.username = :username')
            ->setParameter('username', $username)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findAllByUsername($username) {
        return $this->createQueryBuilder('t')
            ->andWhere('t.username = :username')
            ->setParameter('username', $username)
            ->getQuery()
            ->getResult();
    }

    public function findOneByPlaylistName($playlistName): ?TindifyPlaylist {
        return $this->createQueryBuilder('t')
            ->andWhere('t.name = :playlistName')
            ->setParameter('playlistName', $playlistName)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findOneByPlaylistID($playlistID): ?TindifyPlaylist {
        return $this->createQueryBuilder('t')
            ->andWhere('t.playlistID = :$playlistID')
            ->setParameter('$playlistID', $playlistID)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
