<?php
declare(strict_types=1);

namespace App\MessageBroker;

use App\Enum\TransactionTypeEnum;
use App\MessageBroker\Abstraction\BaseCreateTransactionOperationConsumer;
use PhpAmqpLib\Message\AMQPMessage;

class DebitCreateConsumer extends BaseCreateTransactionOperationConsumer
{
    /**
     * @param AMQPMessage $msg The message
     * @return mixed false to reject and requeue, any other value to acknowledge
     */
    public function execute(AMQPMessage $msg): bool
    {
        $decodedMessage = json_decode($msg->getBody(), true);

        if (!\is_array($decodedMessage)) {
            return $this->releasableError('Wrong message format');
        }

        $decodedMessage['type'] = TransactionTypeEnum::DEBIT;

        return $this->processCreateTransaction($decodedMessage);
    }
}
