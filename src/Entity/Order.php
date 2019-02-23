<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use phpDocumentor\Reflection\Types\Boolean;

/**
 * @ORM\Entity(repositoryClass="App\Repository\OrderRepository")
 * @ORM\Table(name="wine_order")
 */
class Order
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     *
     */
    private $id;

    /**
     * @ORM\Column(name="customer_contact_email", type="string", nullable=false)
     */
    private $customerContactEmail;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\OrderItem", mappedBy="orderId",fetch="EAGER",orphanRemoval=true, cascade={"persist", "remove"})
     */
    private $orderItems;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\OrderLog", mappedBy="orderId",fetch="EAGER",orphanRemoval=true, cascade={"persist", "remove"})
     */
    private $orderLogs;

    /**
     * @ORM\Column(type="string", length=255)
     */

    private $status = 'PENDING';

    public function __construct()
    {
        $this->orderItems = new ArrayCollection();
        $this->orderLogs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCustomerContactEmail(): ?string
    {
        return $this->customerContactEmail;
    }

    public function setCustomerContactEmail(? string $customerContactEmail): self
    {
        $this->customerContactEmail = $customerContactEmail;

        return $this;
    }

    /**
     * @return Collection|OrderItem[]
     */
    public function getOrderItems(): Collection
    {
        return $this->orderItems;
    }

    /**
     * @return Collection|OrderItem[]
     */
    public function getOrderLogs(): Collection
    {
        return $this->orderLogs;
    }


    public function addOrderItem(OrderItem $orderItem): self
    {
        if (!$this->orderItems->contains($orderItem)) {
            $this->orderItems[] = $orderItem;
            $orderItem->setOrderId($this);
        }

        return $this;
    }

    public function removeOrderItem(OrderItem $orderItem): self
    {
        if ($this->orderItems->contains($orderItem)) {
            $this->orderItems->removeElement($orderItem);
            // set the owning side to null (unless already changed)
            if ($orderItem->getOrderId() === $this) {
                $orderItem->setOrderId(null);
            }
        }

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus($status): self
    {
        $this->status = $status;

        return $this;
    }


}
