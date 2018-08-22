<?php
declare(strict_types=1);

namespace App\DTO;

use App\DTO\Abstraction\TransactionDTO;

class TransactionCreateDTO implements TransactionDTO
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
     * @return TransactionCreateDTO
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
     * @return TransactionCreateDTO
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
     * @return TransactionCreateDTO
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
     * @return TransactionCreateDTO
     */
    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }
}
