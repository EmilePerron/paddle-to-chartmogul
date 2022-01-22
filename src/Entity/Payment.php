<?php

namespace App\Entity;

use Ambta\DoctrineEncryptBundle\Configuration\Encrypted;
use App\Repository\PaymentRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PaymentRepository::class)]
#[ORM\Index(fields:["paddleId"])]
class Payment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $paddleId;

    #[ORM\ManyToOne(targetEntity: Subscription::class)]
    #[ORM\JoinColumn(nullable: false)]
    private $subscription;

    #[ORM\Column(type: 'integer')]
    private $amount;

    #[ORM\Column(type: 'string', length: 10)]
    private $currency;

    #[ORM\Column(type: 'datetime')]
    private $payoutDate;

    #[ORM\Column(type: 'boolean')]
    private $paid;

    #[ORM\Column(type: 'boolean')]
    private $isOneOff;

    #[ORM\Column(type: 'boolean')]
    private $synced;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private $lastSyncDate;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
	#[Encrypted]
    private $chartMogulId;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'payments')]
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

    public function getSubscription(): ?Subscription
    {
        return $this->subscription;
    }

    public function setSubscription(?Subscription $subscription): self
    {
        $this->subscription = $subscription;

        return $this;
    }

    public function getAmount(): ?int
    {
        return $this->amount;
    }

    public function setAmount(int $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): self
    {
        $this->currency = $currency;

        return $this;
    }

    public function getPayoutDate(): ?\DateTimeInterface
    {
        return $this->payoutDate;
    }

    public function setPayoutDate(\DateTimeInterface $payoutDate): self
    {
        $this->payoutDate = $payoutDate;

        return $this;
    }

    public function getPaid(): ?bool
    {
        return $this->paid;
    }

    public function setPaid(bool $paid): self
    {
        $this->paid = $paid;

        return $this;
    }

    public function getIsOneOff(): ?bool
    {
        return $this->isOneOff;
    }

    public function setIsOneOff(bool $isOneOff): self
    {
        $this->isOneOff = $isOneOff;

        return $this;
    }

    public function getSynced(): ?bool
    {
        return $this->synced;
    }

    public function setSynced(bool $synced): self
    {
        $this->synced = $synced;

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

    public function getChartMogulId(): ?string
    {
        return $this->chartMogulId;
    }

    public function setChartMogulId(?string $chartMogulId): self
    {
        $this->chartMogulId = $chartMogulId;

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
