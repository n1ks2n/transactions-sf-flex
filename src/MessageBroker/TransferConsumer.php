<?php
declare(strict_types=1);

namespace App\MessageBroker;

use App\Enum\TransactionTypeEnum;
use App\MessageBroker\Abstraction\BaseTransactionOperationConsumer;
use PhpAmqpLib\Message\AMQPMessage;

class TransferConsumer extends BaseTransactionOperationConsumer
{
    /**
     * @param AMQPMessage $msg The message
     * @return mixed false to reject and requeue, any other value to acknowledge
     */
    public function execute(AMQPMessage $msg): bool
    {
        $decodedMessage = json_decode($msg->getBody(), true);
        $debitMessage = [
            'accountId' => $decodedMessage['from'],
            'amount' => $decodedMessage['amount'],
            'type' => TransactionTypeEnum::DEBIT,
            'requestId' => $decodedMessage['requestId']
        ];
        $creditMessage = [
            'accountId' => $decodedMessage['to'],
            'amount' => $decodedMessage['amount'],
            'type' => TransactionTypeEnum::CREDIT,
            'requestId' => $decodedMessage['requestId']
        ];
        $debitSuccess = $this->processTransaction($debitMessage);
        $creditSuccess = $this->processTransaction($creditMessage);

        return $debitSuccess && $creditSuccess;
    }
}
