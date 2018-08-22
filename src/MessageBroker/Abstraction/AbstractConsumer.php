<?php
declare(strict_types=1);

namespace App\MessageBroker\Abstraction;

use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;

abstract class AbstractConsumer implements ConsumerInterface
{
    /**
     * @param string $message
     *
     * @return bool
     */
    protected function releasableError(string $message): bool
    {
        $this->printError($message);
        echo "Releasing faulty message from queue!\n";

        return true;
    }

    protected function printError(string $message): void
    {
        echo $message . "\n";
    }
}
