<?php
declare(strict_types=1);

namespace App\MessageBroker;

use OldSound\RabbitMqBundle\RabbitMq\ProducerInterface;

class TransactionProducer implements ProducerInterface
{

    /**
     * Publish a message
     *
     * @param string $msgBody
     * @param string $routingKey
     * @param array $additionalProperties
     */
    public function publish($msgBody, $routingKey = '', $additionalProperties = array())
    {

    }
}