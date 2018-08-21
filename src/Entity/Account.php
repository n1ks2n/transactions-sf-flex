<?php
declare(strict_types=1);

namespace App\Entity;

use App\Entity\Abstraction\SoftDeletable;
use App\Entity\Abstraction\Timestampable;
use App\Entity\Traits\SoftDeletes as SoftDeletesTrait;
use App\Entity\Traits\Timestamps as TimestampsTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AccountRepository")
 * @ORM\Table(name="accounts")
 */
class Account implements Timestampable, SoftDeletable
{
    use TimestampsTrait,
        SoftDeletesTrait;

    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(name="id", type="integer")
     */
    private $id;

    /**
     * @var float
     *
     * @ORM\Column(name="active_balance", type="decimal", precision=19, scale=4)
     */
    private $activeBalance;

    /**
     * @var float
     *
     * @ORM\Column(name="blocked_balance", type="decimal", precision=19, scale=4)
     */
    private $blockedBalance;

    /**
     * @var float
     *
     * @ORM\Column(name="total_balance", type="decimal", precision=19, scale=4)
     */
    private $totalBalance;

    /**
     * @var string
     *
     * @ORM\Column(name="holder_name", type="string")
     */
    private $holderName;

    /**
     * @var string
     *
     * @ORM\Column(name="holder_last_name", type="string")
     */
    private $holderLastName;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="Transaction", mappedBy="account")
     */
    private $transactions;

    public function __construct()
    {
        $this->setTransactions(new ArrayCollection());
    }

    /**
     * @return float
     */
    public function getActiveBalance(): float
    {
        return $this->activeBalance;
    }

    /**
     * @param float $activeBalance
     *
     * @return Account
     */
    public function setActiveBalance(float $activeBalance): self
    {
        $this->activeBalance = $activeBalance;

        return $this;
    }

    /**
     * @return float
     */
    public function getBlockedBalance(): float
    {
        return $this->blockedBalance;
    }

    /**
     * @param float $blockedBalance
     *
     * @return Account
     */
    public function setBlockedBalance(float $blockedBalance): self
    {
        $this->blockedBalance = $blockedBalance;

        return $this;
    }

    /**
     * @return float
     */
    public function getTotalBalance(): float
    {
        return $this->totalBalance;
    }

    /**
     * @param float $totalBalance
     *
     * @return Account
     */
    public function setTotalBalance(float $totalBalance): self
    {
        $this->totalBalance = $totalBalance;

        return $this;
    }

    /**
     * @return string
     */
    public function getHolderName(): string
    {
        return $this->holderName;
    }

    /**
     * @param string $holderName
     *
     * @return Account
     */
    public function setHolderName(string $holderName): self
    {
        $this->holderName = $holderName;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getTransactions(): Collection
    {
        return $this->transactions;
    }

    /**
     * @param Collection $transactions
     *
     * @return Account
     */
    public function setTransactions(Collection $transactions): self
    {
        $this->transactions = $transactions;

        return $this;
    }

    /**
     * @param Transaction $transaction
     *
     * @return Account
     */
    public function addTransaction(Transaction $transaction): self
    {
        if (!$this->transactions->contains($transaction)) {
            $this->transactions->add($transaction);
            $transaction->setAccount($this);
        }

        return $this;
    }

    /**
     * @param Transaction $transaction
     *
     * @return Account
     */
    public function removeTransaction(Transaction $transaction): self
    {
        if ($this->transactions->contains($transaction)) {
            $this->transactions->removeElement($transaction);
            $transaction->setAccount(null);
        }

        return $this;
    }
}