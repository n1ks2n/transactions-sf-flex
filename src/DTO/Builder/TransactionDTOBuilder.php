<?php
declare(strict_types=1);

namespace App\DTO\Builder;

use App\DTO\Factory\TransactionDTOFactory;
use App\DTO\TransactionDTO;
use App\Exception\WrongAMQPMessageFormat;

class TransactionDTOBuilder
{
    /**
     * @var TransactionDTOFactory
     */
    private $factory;

    /**
     * @param TransactionDTOFactory $factory
     */
    public function __construct(TransactionDTOFactory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * @param array $message
     *
     * @return TransactionDTO
     *
     * @throws WrongAMQPMessageFormat
     */
    public function build(array $message): TransactionDTO
    {

        if (!isset(
            $message['requestId'],
            $message['accountId'],
            $message['amount'],
            $message['type']
        )
        ) {
            throw new WrongAMQPMessageFormat(
                'Message received is malformed!'
            );
        }

        $dto = $this->factory->make();

        return $dto
            ->setRequestId($message['requestId'])
            ->setAccountId($message['accountId'])
            ->setAmount($message['amount'])
            ->setType($message['type']);
    }
}
