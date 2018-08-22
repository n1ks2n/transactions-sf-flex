<?php
declare(strict_types=1);

namespace App\Service\TransactionOperations;

use App\DTO\TransactionCreateDTO;
use App\DTO\TransactionUpdateDTO;
use App\Entity\Account;
use App\Entity\Transaction;
use App\Enum\TransactionStatusEnum;
use App\Enum\TransactionTypeEnum;
use App\Event\TransactionCreatedEvent;
use App\Exception\AccountInsufficientFundsException;
use App\Exception\EntityNotFoundException;
use App\Exception\TransactionExistsException;
use App\Service\TransactionOperations\Abstraction\OperationServiceInterface;
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\PessimisticLockException;
use RuntimeException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class TransactionService implements OperationServiceInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var TransactionStatusChecker
     */
    private $transactionStatusChecker;

    /**
     * @param EntityManagerInterface $entityManager
     * @param EventDispatcherInterface $dispatcher
     * @param TransactionStatusChecker $transactionStatusChecker
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        EventDispatcherInterface $dispatcher,
        TransactionStatusChecker $transactionStatusChecker
    ) {
        $this->entityManager = $entityManager;
        $this->dispatcher = $dispatcher;
        $this->transactionStatusChecker = $transactionStatusChecker;
    }

    /**
     * @param TransactionCreateDTO $transactionDTO
     *
     * @return Transaction
     *
     * @throws TransactionExistsException
     * @throws AccountInsufficientFundsException
     */
    public function create(TransactionCreateDTO $transactionDTO): Transaction
    {
        $transactionRepository = $this->entityManager->getRepository(Transaction::class);
        $accountRepository = $this->entityManager->getRepository(Account::class);

        $transaction = $transactionRepository->findOneBy(
            ['requestId' => $transactionDTO->getRequestId(), 'type' => $transactionDTO->getType()]
        );

        if ($transaction) {
            throw new TransactionExistsException('Trying to create already existing transaction!');
        }

        /** @var Account $account */
        $account = $accountRepository->find($transactionDTO->getAccountId());

        if (!$account) {
            throw new RuntimeException('Trying to create transaction for not existing account!');
        }

        if ($transactionDTO->getType() === TransactionTypeEnum::DEBIT &&
            abs($transactionDTO->getAmount()) > $account->getActiveBalance()
        ) {
            throw new AccountInsufficientFundsException('Not enough funds for operation!');
        }

        $transaction = new Transaction();
        $transaction->setAmount(
            $transactionDTO->getType() === TransactionTypeEnum::DEBIT ?
                -abs($transactionDTO->getAmount()) :
                abs($transactionDTO->getAmount())
        );
        $transaction->setAccount($account);
        $transaction->setType($transactionDTO->getType());
        $transaction->setRequestId($transactionDTO->getRequestId());
        $transaction->setStatus(TransactionStatusEnum::CREATED);

        $this->entityManager->persist($transaction);
        $this->entityManager->flush();
        $this->dispatcher->dispatch(TransactionCreatedEvent::NAME, new TransactionCreatedEvent($transaction));

        return $transaction;
    }

    /**
     * @param TransactionUpdateDTO $transactionUpdateDTO
     *
     * @return Transaction
     *
     * @throws EntityNotFoundException
     * @throws OptimisticLockException
     * @throws PessimisticLockException
     */
    public function update(TransactionUpdateDTO $transactionUpdateDTO): Transaction
    {
        $transactionRepository = $this->entityManager->getRepository(Transaction::class);
        /** @var Transaction $transaction */
        $transaction = $transactionRepository->find($transactionUpdateDTO->getId());

        if (!$transaction) {
            throw new EntityNotFoundException('Transaction not found in the system');
        }

        if ($this->transactionStatusChecker->canBeChanged($transaction, $transactionUpdateDTO->getStatus())) {
            $this->entityManager->lock($transaction, LockMode::PESSIMISTIC_READ);
            $transaction->setStatus($transactionUpdateDTO->getStatus());
            $this->entityManager->persist($transaction);
            $this->entityManager->flush();
        }

        return $transaction;
    }
}
