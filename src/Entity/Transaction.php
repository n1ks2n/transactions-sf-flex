<?php
declare(strict_types=1);

namespace App\Entity;

use App\Entity\Abstraction\SoftDeletable;
use App\Entity\Abstraction\Timestampable;
use App\Entity\Traits\SoftDeletes as SoftDeletesTrait;
use App\Entity\Traits\Timestamps as TimestampsTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TransactionRepository")
 * @ORM\Table(name="transactions", indexes={
 *     @ORM\Index(name="search_idx", columns={"request_id", "status", "type"})
 * })
 */
class Transaction implements Timestampable, SoftDeletable
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
     * @var string
     *
     * @ORM\Column(name="type", type="transaction_type_enum")
     */
    private $type;

    /**
     * @var float
     *
     * @ORM\Column(name="amount", type="decimal", precision=19, scale=4)
     */
    private $amount;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="transaction_status_enum")
     */
    private $status;

    /**
     * @var Account
     *
     * @ORM\ManyToOne(targetEntity="Account", inversedBy="transactions")
     */
    private $account;

    /**
     * @var string
     *
     * @ORM\Column(name="request_id", type="guid")
     */
    private $requestId;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     *
     * @return Transaction
     */
    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return float
     */
    public function getAmount(): float
    {
        return $this->amount;
    }

    /**
     * @param float $amount
     *
     * @return Transaction
     */
    public function setAmount(float $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @param string $status
     *
     * @return Transaction
     */
    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return Account
     */
    public function getAccount(): Account
    {
        return $this->account;
    }

    /**
     * @param Account $account
     *
     * @return Transaction
     */
    public function setAccount(Account $account): self
    {
        $this->account = $account;

        return $this;
    }

    /**
     * @return string
     */
    public function getRequestId(): string
    {
        return $this->requestId;
    }

    /**
     * @param string $requestId
     *
     * @return Transaction
     */
    public function setRequestId(string $requestId): self
    {
        $this->requestId = $requestId;

        return $this;
    }
}
