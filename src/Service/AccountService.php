<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\Account;
use App\Entity\Transaction;
use App\Event\AccountBalanceUpdatedEvent;
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\PessimisticLockException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class AccountService
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
     * @var AccountBalanceCalculator
     */
    private $balanceCalculator;

    /**
     * @param EntityManagerInterface $entityManager
     * @param AccountBalanceCalculator $balanceCalculator
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        AccountBalanceCalculator $balanceCalculator,
        EventDispatcherInterface $dispatcher
    ) {
        $this->entityManager = $entityManager;
        $this->dispatcher = $dispatcher;
        $this->balanceCalculator = $balanceCalculator;
    }

    /**
     * @param Transaction $transaction
     *
     * @return Account
     *
     * @throws OptimisticLockException
     * @throws PessimisticLockException
     */
    public function updateAccountBalance(Transaction $transaction): Account
    {
        $this->entityManager->beginTransaction();
        $account = $transaction->getAccount();
        $this->entityManager->lock($account, LockMode::PESSIMISTIC_READ);
        $account = $this->balanceCalculator->calculate($account, $transaction);
        $account->addTransaction($transaction);
        $this->entityManager->persist($account);
        $this->entityManager->flush();
        $this->entityManager->commit();
        $this->dispatcher->dispatch(AccountBalanceUpdatedEvent::NAME, new AccountBalanceUpdatedEvent($account));

        return $account;
    }
}
