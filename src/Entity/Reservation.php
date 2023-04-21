<?php

namespace App\Entity;

use App\Model\IdentifierTrait;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\ReservationRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use App\Validator as AppAssert;

 /**
 * @ORM\Entity(repositoryClass=ReservationRepository::class)
 * @AppAssert\ReservationDate()
 */
class Reservation
{
    use IdentifierTrait;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="reservations")
     */
    private $user;

    /**
     * @ORM\Column(type="string", length=2)
     */
    private $locale;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $reservationStatus;
    
    /**
     * @ORM\Column(type="integer")
     */
    private $sumPrice;

    /**
     * @ORM\OneToMany(targetEntity="ReservationItem", mappedBy="reservation", cascade={"persist", "remove"})
     */
    private $reservationItems;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="string", length=13)
     */
    private $reservationNumber;

    /**
     * @ORM\OneToMany(targetEntity=PaymentItem::class, mappedBy="reservation")
     */
    private $paymentItems;

    public function __construct()
    {
        $this->reservationItems = new ArrayCollection();
        $this->paymentItems = new ArrayCollection();
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

    public function getLocale(): ?string
    {
        return $this->locale;
    }

    public function setLocale(string $locale): self
    {
        $this->locale = $locale;

        return $this;
    }

    public function getReservationStatus(): ?string
    {
        return $this->reservationStatus;
    }

    public function setReservationStatus(string $reservationStatus): self
    {
        $this->reservationStatus = $reservationStatus;

        return $this;
    }

    public function getSumPrice(): ?int
    {
        return $this->sumPrice;
    }

    public function setSumPrice(int $sumPrice): self
    {
        $this->sumPrice = $sumPrice;

        return $this;
    }

    /**
     * @return Collection<int, ReservationItem>
     */
    public function getReservationItems(): Collection
    {
        return $this->reservationItems;
    }

    public function addReservationItem(ReservationItem $reservationItem): self
    {
        if (!$this->reservationItems->contains($reservationItem)) {
            $this->reservationItems->add($reservationItem);
            $reservationItem->setReservation($this);
        }

        return $this;
    }

    public function removeReservationItem(ReservationItem $reservationItem): self
    {
        if ($this->reservationItems->removeElement($reservationItem)) {
            // set the owning side to null (unless already changed)
            if ($reservationItem->getReservation() === $this) {
                $reservationItem->setReservation(null);
            }
        }

        return $this;
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getReservationNumber(): ?string
    {
        return $this->reservationNumber;
    }

    public function setReservationNumber(string $reservationNumber): self
    {
        $this->reservationNumber = $reservationNumber;

        return $this;
    }

    /**
     * @return Collection<int, PaymentItem>
     */
    public function getPaymentItems(): Collection
    {
        return $this->paymentItems;
    }

    public function addPaymentItem(PaymentItem $paymentItem): self
    {
        if (!$this->paymentItems->contains($paymentItem)) {
            $this->paymentItems->add($paymentItem);
            $paymentItem->setReservation($this);
        }

        return $this;
    }

    public function removePaymentItem(PaymentItem $paymentItem): self
    {
        if ($this->paymentItems->removeElement($paymentItem)) {
            // set the owning side to null (unless already changed)
            if ($paymentItem->getReservation() === $this) {
                $paymentItem->setReservation(null);
            }
        }

        return $this;
    }

}
