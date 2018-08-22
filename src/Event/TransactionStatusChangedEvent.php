<?php
declare(strict_types=1);

namespace App\Event;

use App\Entity\Transaction;
use Symfony\Component\EventDispatcher\Event;

class TransactionStatusChangedEvent extends Event
{
    public const NAME = 'transaction.status.changed';

    /** @var Transaction  */
    protected $transaction;

    /**
     * @param Transaction $transaction
     */
    public function __construct(Transaction $transaction)
    {
        $this->transaction = $transaction;
    }

    /**
     * @return Transaction
     */
    public function getTransaction(): Transaction
    {
        return $this->transaction;
    }
}