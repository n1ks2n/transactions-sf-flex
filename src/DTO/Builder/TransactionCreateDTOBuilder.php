<?php
declare(strict_types=1);

namespace App\DTO\Builder;

use App\DTO\Constants\TransactionDTOTypes;
use App\DTO\Factory\TransactionDTOFactory;
use App\DTO\TransactionCreateDTO;
use App\Exception\WrongAMQPMessageFormatException;

class TransactionCreateDTOBuilder
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
     * @return TransactionCreateDTO
     *
     * @throws WrongAMQPMessageFormatException
     */
    public function build(array $message): TransactionCreateDTO
    {

        if (!isset(
            $message['requestId'],
            $message['accountId'],
            $message['amount'],
            $message['type']
        )) {
            throw new WrongAMQPMessageFormatException(
                'Message received is malformed!'
            );
        }

        $dto = $this->factory->make(TransactionDTOTypes::CREATE);

        return $dto
            ->setRequestId($message['requestId'])
            ->setAccountId($message['accountId'])
            ->setAmount($message['amount'])
            ->setType($message['type']);
    }
}
