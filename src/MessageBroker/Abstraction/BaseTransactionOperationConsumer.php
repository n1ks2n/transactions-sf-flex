<?php
declare(strict_types=1);

namespace App\MessageBroker\Abstraction;

use App\DTO\Builder\TransactionDTOBuilder;
use App\Event\AccountBalanceUpdatedEvent;
use App\Event\TransactionProcessedThroughAccount;
use App\Exception\TransactionExistsException;
use App\Exception\WrongAMQPMessageFormat;
use App\Service\AccountService;
use App\Service\TransactionOperations\TransactionService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\PessimisticLockException;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

abstract class BaseTransactionOperationConsumer implements ConsumerInterface
{
    /**
     * @var TransactionDTOBuilder
     */
    private $builder;

    /**
     * @var TransactionService
     */
    private $transactionService;

    /**
     * @var AccountService
     */
    private $accountService;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @param TransactionDTOBuilder $builder
     * @param TransactionService $transactionService
     * @param AccountService $accountService
     * @param EntityManagerInterface $entityManager
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        TransactionDTOBuilder $builder,
        TransactionService $transactionService,
        AccountService $accountService,
        EntityManagerInterface $entityManager,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->builder = $builder;
        $this->transactionService = $transactionService;
        $this->accountService = $accountService;
        $this->entityManager = $entityManager;
        $this->eventDispatcher = $eventDispatcher;
    }

    protected function processTransaction(array $decodedMessage): bool
    {
        try {
            $creditDTO = $this->builder->build($decodedMessage);
            $transaction = $this->transactionService->create($creditDTO);
            $this->accountService->updateAccountBalance($transaction);
            $this->eventDispatcher->dispatch(AccountBalanceUpdatedEvent::NAME, new TransactionProcessedThroughAccount($transaction));
            echo 'Successfully dispatched job. Amount: ' . $transaction->getAmount();

            return true;
        } catch (WrongAMQPMessageFormat $exception) {
            return $this->releasableError($exception->getMessage());
        } catch (OptimisticLockException $exception) {
            $this->printError($exception->getMessage());

            if ($transaction !== null) {
                $this->entityManager->remove($transaction);
                $this->entityManager->flush();
            }

            return false;
        } catch (PessimisticLockException $exception) {
            $this->printError($exception->getMessage());

            if ($transaction !== null) {
                $this->entityManager->remove($transaction);
                $this->entityManager->flush();
            }

            return false;
        } catch (TransactionExistsException $exception) {
            return $this->releasableError($exception->getMessage());
        }
    }

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