<?php
declare(strict_types=1);

namespace App\DTO;

use App\DTO\Abstraction\TransactionDTO;

class TransactionUpdateDTO implements TransactionDTO
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $status;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     *
     * @return TransactionUpdateDTO
     */
    public function setId(int $id): self
    {
        $this->id = $id;

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
     * @return TransactionUpdateDTO
     */
    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }
}
