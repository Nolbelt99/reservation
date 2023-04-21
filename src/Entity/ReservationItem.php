<?php

namespace App\Entity;

use App\Model\IdentifierTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\ReservationItemRepository;
use DateTime;
use App\Validator as AppAssert;

 /**
 * @ORM\Entity(repositoryClass=ReservationItemRepository::class)
 * @AppAssert\ReservationItemDate()
 */
class ReservationItem
{
    use IdentifierTrait;

    /**
     * @ORM\ManyToOne(targetEntity="Reservation", inversedBy="reservationItems")
     * @ORM\JoinColumn(name="reservation_id", referencedColumnName="id")
     */
    private $reservation;

    /**
     * @ORM\Column(type="datetime")
     */
    private $startDate;

    /**
     * @ORM\Column(type="datetime")
     */
    private $endDate;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $withCaptain;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $licenceNumber;

    /**
     * @ORM\OneToMany(targetEntity="ApartmentGuest", mappedBy="reservationItem", cascade={"persist", "merge"})
     */
    private $apartmentGuests;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $reservationPaidSuccesfully = false;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $reservationPrice;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $paidAssurance;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $assurancePaidSuccesfully;

    /**
     * @ORM\ManyToOne(targetEntity="Service")
     * @ORM\JoinColumn(name="service_id", referencedColumnName="id")
     */
    private $service;

    /**
     * @ORM\ManyToMany(targetEntity="PaymentItem", mappedBy="reservationItems")
     */
    private $paymentItems;

    public function __construct()
    {
        $this->apartmentGuests = new ArrayCollection();
        $this->startDate = new DateTime();
        $this->endDate = new DateTime();
        $this->paymentItems = new ArrayCollection();
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

    public function getStartDate(): ?\DateTime
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTime $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTime
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTime $endDate): self
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function isWithCaptain(): ?bool
    {
        return $this->withCaptain;
    }

    public function setWithCaptain(?bool $withCaptain): self
    {
        $this->withCaptain = $withCaptain;

        return $this;
    }

    public function getLicenceNumber(): ?string
    {
        return $this->licenceNumber;
    }

    public function setLicenceNumber(?string $licenceNumber): self
    {
        $this->licenceNumber = $licenceNumber;

        return $this;
    }

    /**
     * @return Collection<int, ApartmentGuest>
     */
    public function getApartmentGuests(): Collection
    {
        return $this->apartmentGuests;
    }

    public function addApartmentGuest(ApartmentGuest $apartmentGuest): self
    {
        if (!$this->apartmentGuests->contains($apartmentGuest)) {
            $this->apartmentGuests->add($apartmentGuest);
            $apartmentGuest->setReservationItem($this);
        }

        return $this;
    }

    public function removeApartmentGuest(ApartmentGuest $apartmentGuest): self
    {
        if ($this->apartmentGuests->removeElement($apartmentGuest)) {
            // set the owning side to null (unless already changed)
            if ($apartmentGuest->getReservationItem() === $this) {
                $apartmentGuest->setReservationItem(null);
            }
        }

        return $this;
    }

    public function getService(): ?Service
    {
        return $this->service;
    }

    public function setService(?Service $service): self
    {
        $this->service = $service;

        return $this;
    }

    public function isReservationPaidSuccesfully(): ?bool
    {
        return $this->reservationPaidSuccesfully;
    }

    public function setReservationPaidSuccesfully(bool $reservationPaidSuccesfully): self
    {
        $this->reservationPaidSuccesfully = $reservationPaidSuccesfully;

        return $this;
    }

    public function getReservationPrice(): ?int
    {
        return $this->reservationPrice;
    }

    public function setReservationPrice(?int $reservationPrice): self
    {
        $this->reservationPrice = $reservationPrice;

        return $this;
    }

    public function getPaidAssurance(): ?int
    {
        return $this->paidAssurance;
    }

    public function setPaidAssurance(?int $paidAssurance): self
    {
        $this->paidAssurance = $paidAssurance;

        return $this;
    }

    public function isAssurancePaidSuccesfully(): ?bool
    {
        return $this->assurancePaidSuccesfully;
    }

    public function setAssurancePaidSuccesfully(?bool $assurancePaidSuccesfully): self
    {
        $this->assurancePaidSuccesfully = $assurancePaidSuccesfully;

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
            $paymentItem->addReservationItem($this);
        }

        return $this;
    }

    public function removePaymentItem(PaymentItem $paymentItem): self
    {
        if ($this->paymentItems->removeElement($paymentItem)) {
            $paymentItem->removeReservationItem($this);
        }

        return $this;
    }

}
