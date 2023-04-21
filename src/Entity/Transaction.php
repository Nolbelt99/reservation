<?php

namespace App\Entity;

use App\Model\IdentifierTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\TransactionRepository;

/**
 * @ORM\Entity(repositoryClass=TransactionRepository::class)
 */
class Transaction
{
    use IdentifierTrait;

    /**
     * @ORM\Column(type="string", length=32, nullable=true)
     */
    private $transactionId;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $status;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="transactions", cascade={"persist"})
     */
    private $user;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $type;

    /**
     * @ORM\ManyToOne(targetEntity=PaymentItem::class, inversedBy="transactions")
     */
    private $paymentItem;

    /**
     * @ORM\Column(type="boolean")
     */
    private $hasReceipt = false;

    public function __toString() {
        return $this->transactionId;
    }

    public function getTransactionId(): ?string
    {
        return $this->transactionId;
    }

    public function setTransactionId(?string $transactionId): self
    {
        $this->transactionId = $transactionId;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

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
    
    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

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

    public function getPaymentItem(): ?PaymentItem
    {
        return $this->paymentItem;
    }

    public function setPaymentItem(?PaymentItem $paymentItem): self
    {
        $this->paymentItem = $paymentItem;

        return $this;
    }

    public function getHasReceipt(): ?bool
    {
        return $this->hasReceipt;
    }

    public function setHasReceipt(bool $hasReceipt): self
    {
        $this->hasReceipt = $hasReceipt;
        return $this;
    }

}
