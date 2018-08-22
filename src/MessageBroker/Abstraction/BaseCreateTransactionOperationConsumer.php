<?php
declare(strict_types=1);

namespace App\MessageBroker\Abstraction;

use App\DTO\Builder\TransactionCreateDTOBuilder;
use App\Entity\Transaction;
use App\Event\TransactionProcessedThroughAccount;
use App\Exception\AccountInsufficientFundsException;
use App\Exception\TransactionExistsException;
use App\Exception\WrongAMQPMessageFormatException;
use App\Service\AccountService;
use App\Service\TransactionOperations\TransactionService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\PessimisticLockException;
use Exception;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

abstract class BaseCreateTransactionOperationConsumer extends AbstractConsumer
{
    /**
     * @var TransactionCreateDTOBuilder
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
     * @param TransactionCreateDTOBuilder $builder
     * @param TransactionService $transactionService
     * @param AccountService $accountService
     * @param EntityManagerInterface $entityManager
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        TransactionCreateDTOBuilder $builder,
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

    /**
     * @param array $decodedMessage
     *
     * @return bool
     */
    protected function processCreateTransaction(array $decodedMessage): bool
    {
        try {
            $creditDTO = $this->builder->build($decodedMessage);
            $transaction = $this->transactionService->create($creditDTO);
            $this->entityManager->beginTransaction();
            $this->accountService->updateAccountBalance($transaction);
            $this->entityManager->commit();
            $this->eventDispatcher->dispatch(
                TransactionProcessedThroughAccount::NAME,
                new TransactionProcessedThroughAccount($transaction)
            );
            echo 'Successfully dispatched job. Amount: ' . $transaction->getAmount();

            return true;
        } catch (WrongAMQPMessageFormatException $exception) {
            return $this->releasableError($exception->getMessage());
        } catch (OptimisticLockException $exception) {
            return $this->processDBTransactionException($exception, $transaction ?? null);
        } catch (PessimisticLockException $exception) {
            return $this->processDBTransactionException($exception, $transaction ?? null);
        } catch (TransactionExistsException $exception) {
            return $this->releasableError($exception->getMessage());
        } catch (AccountInsufficientFundsException $exception) {
            return $this->releasableError($exception->getMessage());
        }
    }

    /**
     * While processing DB lock we must not release job from queue, just print exception and return false
     * returning false will leave the message in queue for next worker to try again.
     *
     * @param Exception $exception
     *
     * @param Transaction|null $transaction
     *
     * @return bool
     */
    protected function processDBTransactionException(Exception $exception, ?Transaction $transaction): bool
    {
        $this->printError($exception->getMessage());

        if ($transaction !== null) {
            $this->entityManager->remove($transaction);
            $this->entityManager->flush();
            $this->entityManager->rollback();
        }

        return false;
    }
}
