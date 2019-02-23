<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model\Timestampable\Timestampable;

/**
 * @ORM\Entity(repositoryClass="App\Repository\OrderItemRepository")
 */
class OrderItem
{
    use Timestampable;
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Order", inversedBy="orderItems")
     * @ORM\JoinColumn(nullable=false)
     */
    private $orderId;

    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    public $wine;

    /**
    * @ORM\Column(name="available", type="boolean", nullable=true)
    */
    private $available;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOrderId(): ?Order
    {
        return $this->orderId;
    }

    public function setOrderId(?Order $orderId): self
    {
        $this->orderId = $orderId;

        return $this;
    }

    public function getWine(): ?int
    {
        return $this->wine;
    }

    public function setWine(Wine $wine): self
    {
        $this->wine = $wine->getId();

        return $this;
    }
    public function getAvailable(): ?bool
    {
        return $this->available;
    }

    public function setAvailable($available): self
    {
        $this->available = $available;

        return $this;
    }


    public function __toString()
    {
       return "'".$this->getId()."'";
    }
}
