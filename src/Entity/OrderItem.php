<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\OrderItemRepository")
 */
class OrderItem
{
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
     * @ORM\ManyToOne(targetEntity="App\Entity\Wine", inversedBy="orderItems")
     * @ORM\JoinColumn(nullable=false)
     */
    private $wine;

    /*
    * @ORM\Column(name="available", type nullable=true)
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

    public function getWine(): ?Wine
    {
        return $this->wine;
    }

    public function setWine(Wine $wine): self
    {
        $this->wine = $wine;

        return $this;
    }
    public function getAvailable(): ?Wine
    {
        return $this->wine;
    }

    public function setAvailable($available): self
    {
        $this->available = $available;

        return $this;
    }
}