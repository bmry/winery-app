<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model\Timestampable\Timestampable;

/**
 * @ORM\Entity(repositoryClass="App\Repository\WineLogRepository")
 */
class WineLog
{
    use Timestampable;
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $log_action;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Wine", inversedBy="logs")
     * @ORM\JoinColumn(nullable=false)
     */
    private $wine;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $oldPublishDate;

    /**
     * @ORM\Column(type="json_array")
     */
    private $message;



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLogAction(): ?string
    {
        return $this->log_action;
    }

    public function setLogAction(string $log_action): self
    {
        $this->log_action = $log_action;

        return $this;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function setMessage($message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getWine(): ?Wine
    {
        return $this->wine;
    }

    public function setWine(?Wine $wine): self
    {
        $this->wine = $wine;

        return $this;
    }

    public function getOldPublishDate(): ?\DateTimeInterface
    {
        return $this->oldPublishDate;
    }

    public function setOldPublishDate(\DateTimeInterface $oldPublishDate): self
    {
        $this->oldPublishDate = $oldPublishDate;

        return $this;
    }
}
