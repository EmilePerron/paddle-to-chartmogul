<?php

namespace App\Entity;

use App\Repository\SyncExceptionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SyncExceptionRepository::class)]
class SyncException
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'text')]
    private $message;

    #[ORM\Column(type: 'text', nullable: true)]
    private $trace;

    #[ORM\OneToOne(targetEntity: SyncLog::class, cascade: ['persist', 'remove'])]
    private $relatedLog;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getTrace(): ?string
    {
        return $this->trace;
    }

    public function setTrace(?string $trace): self
    {
        $this->trace = $trace;

        return $this;
    }

    public function getRelatedLog(): ?SyncLog
    {
        return $this->relatedLog;
    }

    public function setRelatedLog(?SyncLog $relatedLog): self
    {
        $this->relatedLog = $relatedLog;

        return $this;
    }
}
