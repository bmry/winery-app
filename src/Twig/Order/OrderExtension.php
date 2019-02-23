<?php
/**
 * Created by PhpStorm.
 * User: Morayo
 * Date: 2/23/2019
 * Time: 3:41 PM
 */

namespace App\Twig\Order;

use Doctrine\ORM\EntityManagerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class OrderExtension extends AbstractExtension
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('get_wine_name', [$this, 'getWineNameById']),
        ];
    }

    public function getWineNameById($wineId)
    {
        return $this->entityManager->getRepository('App\Entity\Wine')->findOneBy(['id' => $wineId])->getTitle();
    }
}