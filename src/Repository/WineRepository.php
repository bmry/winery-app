<?php

namespace App\Repository;

use App\Entity\Wine;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Wine|null find($id, $lockMode = null, $lockVersion = null)
 * @method Wine|null findOneBy(array $criteria, array $orderBy = null)
 * @method Wine[]    findAll()
 * @method Wine[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WineRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Wine::class);
    }

    public function getWineByNameAndDate($wineName, \DateTime $dateTime){

        return $this->createQueryBuilder('w')
            ->where('w.title = :name')
            ->andWhere('w.publishDate LIKE  :date')
            ->setParameter('name', $wineName)
            ->setParameter('date', $dateTime->format('Y-m-d'))
            ->getQuery()
            ->getSingleResult();
    }
}
