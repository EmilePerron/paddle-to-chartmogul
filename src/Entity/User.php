<?php

namespace App\Entity;

use Ambta\DoctrineEncryptBundle\Configuration\Encrypted;
use App\Repository\UserRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
class User implements UserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    private $email;

    #[ORM\Column(type: 'json')]
    private $roles = [];

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
	#[Encrypted]
    private $paddleVendorId;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
	#[Encrypted]
    private $paddleApiKey;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
	#[Encrypted]
    private $chartMogulApiKey;

    #[ORM\Column(type: 'datetime')]
    private $signUpDate;

    #[ORM\Column(type: 'datetime')]
    private $lastLoginDate;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private $lastSyncDate;

    #[ORM\OneToOne(mappedBy: 'owner', targetEntity: DataSource::class, cascade: ['persist', 'remove'])]
    private $dataSource;

    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: Plan::class, orphanRemoval: true)]
    private $plans;

    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: Subscription::class, orphanRemoval: true)]
    private $subscriptions;

    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: Customer::class, orphanRemoval: true)]
    private $customers;

    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: Payment::class, orphanRemoval: true)]
    private $payments;

    #[ORM\Column(type: 'string', length: 15, nullable: true)]
    private $frequency;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: SyncLog::class, orphanRemoval: true)]
    #[ORM\OrderBy(["startDate" => "DESC"])]
    private $syncLogs;

    public function __construct()
    {
        $this->plans = new ArrayCollection();
        $this->subscriptions = new ArrayCollection();
        $this->signUpDate = new DateTime();
        $this->lastLoginDate = new DateTime();
        $this->customers = new ArrayCollection();
        $this->payments = new ArrayCollection();
        $this->frequency = "1 day";
        $this->syncLogs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getPaddleVendorId(): ?string
    {
        return $this->paddleVendorId;
    }

    public function setPaddleVendorId(?string $paddleVendorId): self
    {
        $this->paddleVendorId = $paddleVendorId;

        return $this;
    }

    public function getPaddleApiKey(): ?string
    {
        return $this->paddleApiKey;
    }

    public function setPaddleApiKey(?string $paddleApiKey): self
    {
        $this->paddleApiKey = $paddleApiKey;

        return $this;
    }

    public function getChartMogulApiKey(): ?string
    {
        return $this->chartMogulApiKey;
    }

    public function setChartMogulApiKey(?string $chartMogulApiKey): self
    {
        $this->chartMogulApiKey = $chartMogulApiKey;

        return $this;
    }

    public function getSignUpDate(): ?\DateTimeInterface
    {
        return $this->signUpDate;
    }

    public function setSignUpDate(\DateTimeInterface $signUpDate): self
    {
        $this->signUpDate = $signUpDate;

        return $this;
    }

    public function getLastLoginDate(): ?\DateTimeInterface
    {
        return $this->lastLoginDate;
    }

    public function setLastLoginDate(\DateTimeInterface $lastLoginDate): self
    {
        $this->lastLoginDate = $lastLoginDate;

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

    public function getDataSource(): ?DataSource
    {
        return $this->dataSource;
    }

    public function setDataSource(DataSource $dataSource): self
    {
        // set the owning side of the relation if necessary
        if ($dataSource->getOwner() !== $this) {
            $dataSource->setOwner($this);
        }

        $this->dataSource = $dataSource;

        return $this;
    }

    public function unsetDataSource(): self
    {
        $this->dataSource = null;

        return $this;
    }

    /**
     * @return Collection|Plan[]
     */
    public function getPlans(): Collection
    {
        return $this->plans;
    }

    public function addPlan(Plan $plan): self
    {
        if (!$this->plans->contains($plan)) {
            $this->plans[] = $plan;
            $plan->setOwner($this);
        }

        return $this;
    }

    public function removePlan(Plan $plan): self
    {
        if ($this->plans->removeElement($plan)) {
            // set the owning side to null (unless already changed)
            if ($plan->getOwner() === $this) {
                $plan->setOwner(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Subscription[]
     */
    public function getSubscriptions(): Collection
    {
        return $this->subscriptions;
    }

    public function addSubscription(Subscription $subscription): self
    {
        if (!$this->subscriptions->contains($subscription)) {
            $this->subscriptions[] = $subscription;
            $subscription->setOwner($this);
        }

        return $this;
    }

    public function removeSubscription(Subscription $subscription): self
    {
        if ($this->subscriptions->removeElement($subscription)) {
            // set the owning side to null (unless already changed)
            if ($subscription->getOwner() === $this) {
                $subscription->setOwner(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Customer[]
     */
    public function getCustomers(): Collection
    {
        return $this->customers;
    }

    public function addCustomer(Customer $customer): self
    {
        if (!$this->customers->contains($customer)) {
            $this->customers[] = $customer;
            $customer->setOwner($this);
        }

        return $this;
    }

    public function removeCustomer(Customer $customer): self
    {
        if ($this->customers->removeElement($customer)) {
            // set the owning side to null (unless already changed)
            if ($customer->getOwner() === $this) {
                $customer->setOwner(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Payment[]
     */
    public function getPayments(): Collection
    {
        return $this->payments;
    }

    public function addPayment(Payment $payment): self
    {
        if (!$this->payments->contains($payment)) {
            $this->payments[] = $payment;
            $payment->setOwner($this);
        }

        return $this;
    }

    public function removePayment(Payment $payment): self
    {
        if ($this->payments->removeElement($payment)) {
            // set the owning side to null (unless already changed)
            if ($payment->getOwner() === $this) {
                $payment->setOwner(null);
            }
        }

        return $this;
    }

    public function hasConfiguredPaddle(): bool
    {
        return $this->getPaddleApiKey() && $this->getPaddleVendorId();
    }

    public function hasConfiguredChartMogul(): bool
    {
        return !!$this->getChartMogulApiKey();
    }

    /**
     * @return string|null A valid PHP DateTime modifier without prefix (ex.: 1 day)
     */
    public function getFrequency(): ?string
    {
        return $this->frequency;
    }

    /**
     * @param string|null $frequency A valid PHP DateTime modifier without prefix (ex.: 1 day)
     */
    public function setFrequency(?string $frequency): self
    {
        $this->frequency = $frequency ?: "1 day";

        return $this;
    }

    public function getFrequencyHumanLabel(): string
    {
        return match ($this->frequency) {
            "1 day" => "every day",
			"1 hour" => "every hour",
			"15 minutes" => "every 15 minutes",
        };
    }

    /**
     * @return Collection|SyncLog[]
     */
    public function getSyncLogs(): Collection
    {
        return $this->syncLogs;
    }

    public function addSyncLog(SyncLog $syncLog): self
    {
        if (!$this->syncLogs->contains($syncLog)) {
            $this->syncLogs[] = $syncLog;
            $syncLog->setUser($this);
        }

        return $this;
    }

    public function removeSyncLog(SyncLog $syncLog): self
    {
        if ($this->syncLogs->removeElement($syncLog)) {
            // set the owning side to null (unless already changed)
            if ($syncLog->getUser() === $this) {
                $syncLog->setUser(null);
            }
        }

        return $this;
    }
}
