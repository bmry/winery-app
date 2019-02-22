<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\OrderLogRepository")
 */
class OrderLog
{
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
}
