<?php
declare(strict_types=1);

namespace App\Listener;

use App\Event\TransactionProcessedThroughAccount;
use App\Service\TransactionOperations\TransactionMapper;
use OldSound\RabbitMqBundle\RabbitMq\ProducerInterface;
use Symfony\Component\EventDispatcher\Event;

class TransactionProcessedThroughAccountListener
{
    /**
     * @var TransactionMapper
     */
    private $transactionMapper;
    /**
     * @var ProducerInterface
     */
    private $producer;

    /**
     * @param TransactionMapper $transactionMapper
     * @param ProducerInterface $producer
     */
    public function __construct(TransactionMapper $transactionMapper, ProducerInterface $producer)
    {
        $this->transactionMapper = $transactionMapper;
        $this->producer = $producer;
    }

    /**
     * @param Event|TransactionProcessedThroughAccount $event
     */
    public function onTransactionProcessed(Event $event): void
    {
        $transaction = $event->getTransaction();
        $message = json_encode($this->transactionMapper->mapToArray($transaction));
        $this->producer->publish($message);
    }
}
