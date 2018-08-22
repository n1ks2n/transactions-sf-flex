<?php
declare(strict_types=1);

namespace App\DTO\Builder;

use App\DTO\Constants\TransactionDTOTypes;
use App\DTO\Factory\TransactionDTOFactory;
use App\DTO\TransactionUpdateDTO;
use App\Exception\WrongAMQPMessageFormatException;

class TransactionUpdateDTOBuilder
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
     * @return TransactionUpdateDTO
     *
     * @throws WrongAMQPMessageFormatException
     */
    public function build(array $message): TransactionUpdateDTO
    {

        if (!isset(
            $message['id'],
            $message['status']
        )) {
            throw new WrongAMQPMessageFormatException(
                'Message received is malformed!'
            );
        }

        $dto = $this->factory->make(TransactionDTOTypes::UPDATE);

        return $dto->setId($message['id'])->setStatus($message['status']);
    }
}