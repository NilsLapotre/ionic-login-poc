<?php

namespace App\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="FailedLoginAttemptRepository")
 */
class FailedLoginAttempt
{
    /**
     * var int|null.
     *
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * var strint|null.
     *
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $ipAddress;

    /**
     * @var DateTimeImmutable
     * @ORM\Column(type="datetime_immutable")
     */
    private $occuredAt;

    /**
     * @var string|null
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $username;

    /**
     * @var string|null
     * @ORM\Column(type="text", nullable=true)
     */
    private $messsage;

    public function __construct(?string $ipAddress, ?string $username, ?string $message)
    {
        $this->ipAddress = $ipAddress;
        $this->username = $username;
        $this->messsage = $message;
        $this->occuredAt = new DateTimeImmutable('now');
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIpAddress(): ?string
    {
        return $this->ipAddress;
    }

    public function getOccuredAt(): DateTimeImmutable
    {
        return $this->occuredAt;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function getMesssage(): ?string
    {
        return $this->messsage;
    }
}
