<?php
declare(strict_types=1);

namespace App\Service\TransactionOperations;

use App\DTO\TransactionDTO;
use App\Entity\Account;
use App\Entity\Transaction;
use App\Enum\TransactionStatusEnum;
use App\Enum\TransactionTypeEnum;
use App\Event\TransactionCreatedEvent;
use App\Exception\TransactionExistsException;
use App\Service\TransactionOperations\Abstraction\OperationServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
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
     * CreditService constructor.
     * @param EntityManagerInterface $entityManager
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(EntityManagerInterface $entityManager, EventDispatcherInterface $dispatcher)
    {
        $this->entityManager = $entityManager;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param TransactionDTO $transactionDTO
     *
     * @return Transaction
     *
     * @throws TransactionExistsException
     */
    public function create(TransactionDTO $transactionDTO): Transaction
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
}
