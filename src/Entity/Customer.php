<?php

namespace App\Entity;

use App\Repository\CustomerRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CustomerRepository::class)]
#[ORM\Index(fields:["paddleId"])]
class Customer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $paddleId;

    #[ORM\Column(type: 'string', length: 512)]
    private $email;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $chartMogulId;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private $lastSyncDate;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'customers')]
    #[ORM\JoinColumn(nullable: false)]
    private $owner;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPaddleId(): ?string
    {
        return $this->paddleId;
    }

    public function setPaddleId(string $paddleId): self
    {
        $this->paddleId = $paddleId;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getChartMogulId(): ?string
    {
        return $this->chartMogulId;
    }

    public function setChartMogulId(?string $chartMogulId): self
    {
        $this->chartMogulId = $chartMogulId;

        return $this;
    }

    public function getLastSyncDate(): ?\DateTimeInterface
    {
        return $this->lastSyncDate;
    }

    public function setLastSyncDate(?\DateTimeInterface $lastSyncDate): self
    {
        $this->lastSyncDate = $lastSyncDate;

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): self
    {
        $this->owner = $owner;

        return $this;
    }
}
