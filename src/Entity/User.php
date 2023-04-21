<?php

namespace App\Entity;

use App\Model\IdentifierTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

 /**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\Table(name="`user`")
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    use IdentifierTrait;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="array")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updated_at;

    /**
     * @ORM\Column(type="boolean")
     */
    private $newsletter = false;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $lastName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $phone;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $invoiceAddressName;

    /**
     * @ORM\Column(type="string", length=12, nullable=true)
     */
    private $invoiceAddressZip;

    /**
     * @ORM\Column(type="string", length=3, nullable=true)
     */
    private $invoiceAddressCountry;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $invoiceAddressCity;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $invoiceAddressStreetAndOther;
    
    /**
     * @ORM\OneToMany(targetEntity=Reservation::class, mappedBy="user")
     */
    private $reservations;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $birthDay;

    /**
     * @ORM\Column(type="datetime")
     */
    private $passwordAvaibleUntil;

    /**
     * @ORM\Column(type="boolean")
     */
    private $usedPassword = false;

    /**
    * @ORM\OneToMany(targetEntity="Transaction", mappedBy="user", cascade={"persist"})
    */
   private $transactions;

    /**
     * @ORM\OneToMany(targetEntity=PaymentItem::class, mappedBy="user")
     */
    private $paymentItems;

    public function __construct()
    {
        $this->reservations = new ArrayCollection();
        $this->transactions = new ArrayCollection();
        $this->paymentItems = new ArrayCollection();
    }

    public function __toString() {
        return $this->email;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTime $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(?\DateTime $updated_at): self
    {
        $this->updated_at = $updated_at;

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
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
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
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function isNewsletter(): ?bool
    {
        return $this->newsletter;
    }

    public function setNewsletter(bool $newsletter): self
    {
        $this->newsletter = $newsletter;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getInvoiceAddressName(): ?string
    {
        return $this->invoiceAddressName;
    }

    public function setInvoiceAddressName(?string $invoiceAddressName): self
    {
        $this->invoiceAddressName = $invoiceAddressName;

        return $this;
    }

    public function getInvoiceAddressZip(): ?string
    {
        return $this->invoiceAddressZip;
    }

    public function setInvoiceAddressZip(?string $invoiceAddressZip): self
    {
        $this->invoiceAddressZip = $invoiceAddressZip;

        return $this;
    }

    public function getInvoiceAddressCountry(): ?string
    {
        return $this->invoiceAddressCountry;
    }

    public function setInvoiceAddressCountry(?string $invoiceAddressCountry): self
    {
        $this->invoiceAddressCountry = $invoiceAddressCountry;

        return $this;
    }

    public function getInvoiceAddressCity(): ?string
    {
        return $this->invoiceAddressCity;
    }

    public function setInvoiceAddressCity(?string $invoiceAddressCity): self
    {
        $this->invoiceAddressCity = $invoiceAddressCity;

        return $this;
    }

    public function getInvoiceAddressStreetAndOther(): ?string
    {
        return $this->invoiceAddressStreetAndOther;
    }

    public function setInvoiceAddressStreetAndOther(?string $invoiceAddressStreetAndOther): self
    {
        $this->invoiceAddressStreetAndOther = $invoiceAddressStreetAndOther;

        return $this;
    }

    public function getBirthDay(): ?\DateTime
    {
        return $this->birthDay;
    }

    public function setBirthDay(?\DateTime $birthDay): self
    {
        $this->birthDay = $birthDay;

        return $this;
    }

    /**
     * @return Collection<int, Reservation>
     */
    public function getReservations(): Collection
    {
        return $this->reservations;
    }

    public function addReservation(Reservation $reservation): self
    {
        if (!$this->reservations->contains($reservation)) {
            $this->reservations->add($reservation);
            $reservation->setUser($this);
        }

        return $this;
    }

    public function removeReservation(Reservation $reservation): self
    {
        if ($this->reservations->removeElement($reservation)) {
            // set the owning side to null (unless already changed)
            if ($reservation->getUser() === $this) {
                $reservation->setUser(null);
            }
        }

        return $this;
    }

    public function isUsedPassword(): ?bool
    {
        return $this->usedPassword;
    }

    public function setUsedPassword(bool $usedPassword): self
    {
        $this->usedPassword = $usedPassword;

        return $this;
    }

    public function getPasswordAvaibleUntil(): ?\DateTime
    {
        return $this->passwordAvaibleUntil;
    }

    public function setPasswordAvaibleUntil(\DateTime $passwordAvaibleUntil): self
    {
        $this->passwordAvaibleUntil = $passwordAvaibleUntil;

        return $this;
    }

    /**
     * @return Collection|Transaction[]
     */
    public function getTransactions(): Collection
    {
        return $this->transactions;
    }

    public function addTransaction(Transaction $transaction): self
    {
        if (!$this->transactions->contains($transaction)) {
            $this->transactions[] = $transaction;
            $transaction->setUser($this);

        }

        return $this;
    }

    public function removeTransaction(Transaction $transaction): self
    {
        if ($this->transactions->removeElement($transaction)) {
            // set the owning side to null (unless already changed)
            if ($transaction->getUser() === $this) {
                $transaction->setUser(null);
            }
        }

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
            $paymentItem->setUser($this);
        }

        return $this;
    }

    public function removePaymentItem(PaymentItem $paymentItem): self
    {
        if ($this->paymentItems->removeElement($paymentItem)) {
            // set the owning side to null (unless already changed)
            if ($paymentItem->getUser() === $this) {
                $paymentItem->setUser(null);
            }
        }

        return $this;
    }

}
