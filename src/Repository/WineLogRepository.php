<?php

namespace App\Repository;

use App\Entity\OrderLog;
use App\Entity\Wine;
use App\Entity\WineLog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method OrderLog|null find($id, $lockMode = null, $lockVersion = null)
 * @method OrderLog|null findOneBy(array $criteria, array $orderBy = null)
 * @method OrderLog[]    findAll()
 * @method OrderLog[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WineLogRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, WineLog::class);
    }

    public function getFromWineLogLastTimeWineWasAvailableBeforeNewUpdate(Wine $wine)
    {

        $newPublishDate = $wine->getPublishDate();
        $result = $this->createQueryBuilder('w')
            ->where('w.log_action = :action')
            ->andWhere('w.wine = :wine')
            ->andWhere('w.oldPublishDate IS NOT NULL')
            ->andWhere('w.oldPublishDate < :newPublishDate')
            ->setParameter('action', 'DATE_UPDATE')
            ->setParameter('wine', $wine)
            ->setParameter('newPublishDate', $newPublishDate)
            ->orderBy('w.id', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getResult();

        return $result[0];
    }
}
