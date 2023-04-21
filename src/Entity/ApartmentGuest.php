<?php

namespace App\Entity;

use App\Model\IdentifierTrait;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\ApartmentGuestRepository;

 /**
 * @ORM\Entity(repositoryClass=ApartmentGuestRepository::class)
 */
class ApartmentGuest
{
    use IdentifierTrait;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $lastName;

    /**
     * @ORM\Column(type="datetime")
     */
    private $birthDay;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $cardNumber;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $cardType;

    /**
     * @ORM\ManyToOne(targetEntity="ReservationItem", inversedBy="apartmentGuests")
     * @ORM\JoinColumn(name="reservationItem_id", referencedColumnName="id")
     */
    private $reservationItem;

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getBirthDay(): ?\DateTime
    {
        return $this->birthDay;
    }

    public function setBirthDay(\DateTime $birthDay): self
    {
        $this->birthDay = $birthDay;

        return $this;
    }

    public function getCardNumber(): ?string
    {
        return $this->cardNumber;
    }

    public function setCardNumber(string $cardNumber): self
    {
        $this->cardNumber = $cardNumber;

        return $this;
    }

    public function getCardType(): ?string
    {
        return $this->cardType;
    }

    public function setCardType(string $cardType): self
    {
        $this->cardType = $cardType;

        return $this;
    }

    public function getReservationItem(): ?ReservationItem
    {
        return $this->reservationItem;
    }

    public function setReservationItem(?ReservationItem $reservationItem): self
    {
        $this->reservationItem = $reservationItem;

        return $this;
    }
}
