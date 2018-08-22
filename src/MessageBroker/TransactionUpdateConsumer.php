<?php
declare(strict_types=1);

namespace App\MessageBroker;

use App\DTO\Builder\TransactionUpdateDTOBuilder;
use App\Event\TransactionStatusChangedEvent;
use App\Exception\EntityNotFoundException;
use App\Exception\WrongAMQPMessageFormatException;
use App\MessageBroker\Abstraction\AbstractConsumer;
use App\Service\AccountService;
use App\Service\TransactionOperations\TransactionService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\PessimisticLockException;
use Exception;
use PhpAmqpLib\Message\AMQPMessage;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class TransactionUpdateConsumer extends AbstractConsumer
{
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
     * @var TransactionService
     */
    private $transactionService;

    /**
     * @var TransactionUpdateDTOBuilder
     */
    private $builder;

    /**
     * @param AccountService $accountService
     * @param EntityManagerInterface $entityManager
     * @param EventDispatcherInterface $eventDispatcher
     * @param TransactionService $transactionService
     * @param TransactionUpdateDTOBuilder $builder
     */
    public function __construct(
        AccountService $accountService,
        EntityManagerInterface $entityManager,
        EventDispatcherInterface $eventDispatcher,
        TransactionService $transactionService,
        TransactionUpdateDTOBuilder $builder
    ) {
        $this->accountService = $accountService;
        $this->entityManager = $entityManager;
        $this->eventDispatcher = $eventDispatcher;
        $this->transactionService = $transactionService;
        $this->builder = $builder;
    }

    /**
     * @param AMQPMessage $msg The message
     * @return mixed false to reject and requeue, any other value to acknowledge
     */
    public function execute(AMQPMessage $msg)
    {
        $decodedMessage = json_decode($msg->getBody(), true);

        if (!\is_array($decodedMessage)) {
            return $this->releasableError('Wrong message format');
        }

        return $this->processUpdateTransaction($decodedMessage);
    }

    /**
     * @param array $decodedMessage
     *
     * @return bool
     */
    private function processUpdateTransaction(array $decodedMessage): bool
    {
        try {
            $dto = $this->builder->build($decodedMessage);
            $this->entityManager->beginTransaction();
            $transaction = $this->transactionService->update($dto);
            $this->accountService->updateAccountBalance($transaction);
            $this->entityManager->commit();
            $this->eventDispatcher->dispatch(
                TransactionStatusChangedEvent::NAME,
                new TransactionStatusChangedEvent($transaction)
            );

            return true;
        } catch (WrongAMQPMessageFormatException $exception) {
            return $this->releasableError($exception->getMessage());
        } catch (OptimisticLockException $exception) {
            return $this->processDBLockException($exception);
        } catch (PessimisticLockException $exception) {
            return $this->processDBLockException($exception);
        } catch (EntityNotFoundException $exception) {
            return $this->releasableError($exception->getMessage());
        }
    }

    /**
     * While processing DB lock we must not release job from queue, just print exception and return false
     * returning false will leave the message in queue for next worker to try again.
     *
     * @param Exception $exception
     * @return bool
     */
    protected function processDBLockException(Exception $exception): bool
    {
        $this->printError($exception->getMessage());

        return false;
    }
}
