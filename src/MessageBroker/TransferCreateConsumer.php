<?php
declare(strict_types=1);

namespace App\MessageBroker;

use App\Enum\TransactionTypeEnum;
use App\MessageBroker\Abstraction\BaseCreateTransactionOperationConsumer;
use PhpAmqpLib\Message\AMQPMessage;

class TransferCreateConsumer extends BaseCreateTransactionOperationConsumer
{
    /**
     * @param AMQPMessage $msg The message
     *
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
        $debitSuccess = $this->processCreateTransaction($debitMessage);
        $creditSuccess = $this->processCreateTransaction($creditMessage);

        return $debitSuccess && $creditSuccess;
    }
}
