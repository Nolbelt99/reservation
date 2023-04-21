<?php

namespace App\Entity;

use DateTime;
use App\Model\IdentifierTrait;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\ReceiptRepository;

/**
 * @ORM\Entity(repositoryClass=ReceiptRepository::class)
 */
class Receipt
{
    use IdentifierTrait;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="string", length=64, unique=true)
     */
    private $identifier;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="invoices")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="Reservation")
     */
    private $reservation;

    /**
     * @ORM\OneToOne(targetEntity=Transaction::class)
     */
    private $transaction;

    public function getIdentifier(): ?string
    {
        return $this->identifier;
    }

    public function setIdentifier(string $identifier): self
    {
        $this->identifier = $identifier;
        return $this;
    }

    public function getCreatedAt(): ?DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getReservation(): ?Reservation
    {
        return $this->reservation;
    }

    public function setReservation(?Reservation $reservation): self
    {
        $this->reservation = $reservation;

        return $this;
    }
    
    public function getTransaction(): ?Transaction
    {
        return $this->transaction;
    }

    public function setTransaction(?Transaction $transaction): self
    {
        $this->transaction = $transaction;

        return $this;
    }
}
