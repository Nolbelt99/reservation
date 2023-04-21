<?php

namespace App\Entity;

use App\Model\IdentifierTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\PaymentItemRepository;

 /**
 * @ORM\Entity(repositoryClass=PaymentItemRepository::class)
 */
class PaymentItem
{
    use IdentifierTrait;

    /**
     * @ORM\ManyToMany(targetEntity="ReservationItem", inversedBy="paymentItems", cascade={"persist"})
     * @ORM\JoinTable(name="reservation_item_payment_item",
     *      joinColumns={@ORM\JoinColumn(name="payment_item_id", referencedColumnName="id", onDelete="cascade")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="reservation_item_id", referencedColumnName="id", onDelete="cascade")}
     * )
     */
    private $reservationItems;

    /**
     * @ORM\ManyToOne(targetEntity=Reservation::class, inversedBy="paymentItems")
     */
    private $reservation;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $companyName;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $companyPriority;

    /**
     * @ORM\Column(type="boolean")
     */
    private $paid = false;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="paymentItems")
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity=Transaction::class, mappedBy="paymentItem")
     */
    private $transactions;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $type;
        
    /**
     * @ORM\Column(type="integer")
     */
    private $sumPrice;


    public function __construct()
    {
        $this->reservationItems = new ArrayCollection();
        $this->transactions = new ArrayCollection();
    }

    public function getCompanyName(): ?string
    {
        return $this->companyName;
    }

    public function setCompanyName(string $companyName): self
    {
        $this->companyName = $companyName;

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
        }

        return $this;
    }

    public function removeReservationItem(ReservationItem $reservationItem): self
    {
        $this->reservationItems->removeElement($reservationItem);

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

    /**
     * @return Collection<int, Transaction>
     */
    public function getTransactions(): Collection
    {
        return $this->transactions;
    }

    public function addTransaction(Transaction $transaction): self
    {
        if (!$this->transactions->contains($transaction)) {
            $this->transactions->add($transaction);
            $transaction->setPaymentItem($this);
        }

        return $this;
    }

    public function removeTransaction(Transaction $transaction): self
    {
        if ($this->transactions->removeElement($transaction)) {
            // set the owning side to null (unless already changed)
            if ($transaction->getPaymentItem() === $this) {
                $transaction->setPaymentItem(null);
            }
        }

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function isPaid(): ?bool
    {
        return $this->paid;
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

    public function getCompanyPriority(): ?int
    {
        return $this->companyPriority;
    }

    public function setCompanyPriority(?int $companyPriority): self
    {
        $this->companyPriority = $companyPriority;

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

}
