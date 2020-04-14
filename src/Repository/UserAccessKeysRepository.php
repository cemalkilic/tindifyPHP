<?php

namespace App\Repository;

use App\Entity\UserAccessKeys;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserAccessKeys|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserAccessKeys|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserAccessKeys[]    findAll()
 * @method UserAccessKeys[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserAccessKeysRepository extends ServiceEntityRepository {
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, UserAccessKeys::class);
    }
}
