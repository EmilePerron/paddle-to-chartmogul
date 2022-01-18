<?php

namespace App\Entity;

use Ambta\DoctrineEncryptBundle\Configuration\Encrypted;
use App\Repository\PlanRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PlanRepository::class)]
#[ORM\Index(fields:["paddleId"])]
class Plan
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
	#[Encrypted]
    private $paddleId;

    #[ORM\Column(type: 'string', length: 512)]
    private $name;

    #[ORM\Column(type: 'string', length: 255)]
    private $billingType;

    #[ORM\Column(type: 'integer')]
    private $billingPeriod;

    #[ORM\Column(type: 'float')]
    private $initialPrice;

    #[ORM\Column(type: 'float')]
    private $recurringPrice;

    #[ORM\Column(type: 'integer')]
    private $trialDays;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
	#[Encrypted]
    private $chartMogulId;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private $lastSyncDate;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'plans')]
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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getBillingType(): ?string
    {
        return $this->billingType;
    }

    public function setBillingType(string $billingType): self
    {
        $this->billingType = $billingType;

        return $this;
    }

    public function getBillingPeriod(): ?int
    {
        return $this->billingPeriod;
    }

    public function setBillingPeriod(int $billingPeriod): self
    {
        $this->billingPeriod = $billingPeriod;

        return $this;
    }

    public function getInitialPrice(): ?float
    {
        return $this->initialPrice;
    }

    public function setInitialPrice(float $initialPrice): self
    {
        $this->initialPrice = $initialPrice;

        return $this;
    }

    public function getRecurringPrice(): ?float
    {
        return $this->recurringPrice;
    }

    public function setRecurringPrice(float $recurringPrice): self
    {
        $this->recurringPrice = $recurringPrice;

        return $this;
    }

    public function getTrialDays(): ?int
    {
        return $this->trialDays;
    }

    public function setTrialDays(int $trialDays): self
    {
        $this->trialDays = $trialDays;

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
