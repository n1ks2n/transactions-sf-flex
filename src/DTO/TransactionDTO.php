<?php
declare(strict_types=1);

namespace App\DTO;

class TransactionDTO
{
    /**
     * @var string
     */
    private $requestId;

    /**
     * @var float
     */
    private $amount;

    /**
     * @var int
     */
    private $accountId;

    /**
     * @var string
     */
    private $type;

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
     * @return TransactionDTO
     */
    public function setRequestId(string $requestId): self
    {
        $this->requestId = $requestId;

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
     * @return TransactionDTO
     */
    public function setAmount(float $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * @return int
     */
    public function getAccountId(): int
    {
        return $this->accountId;
    }

    /**
     * @param int $accountId
     *
     * @return TransactionDTO
     */
    public function setAccountId(int $accountId): self
    {
        $this->accountId = $accountId;

        return $this;
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
     * @return TransactionDTO
     */
    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }
}