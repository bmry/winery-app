<?php

namespace App\Repository;

use App\Entity\OrderLog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method OrderLog|null find($id, $lockMode = null, $lockVersion = null)
 * @method OrderLog|null findOneBy(array $criteria, array $orderBy = null)
 * @method OrderLog[]    findAll()
 * @method OrderLog[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderLogRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, OrderLog::class);
    }

}
