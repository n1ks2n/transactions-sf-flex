<?php
declare(strict_types=1);

namespace App\Tests\Service;

use App\Entity\Account;
use App\Entity\Transaction;
use App\Service\AccountBalanceCalculator;
use App\Service\AccountService;
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\PessimisticLockException;
use Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class AccountServiceTest extends TestCase
{
    /**
     * @var MockObject|EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var MockObject|AccountBalanceCalculator
     */
    private $balanceCalculator;

    /**
     * @var MockObject|EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var AccountService
     */
    private $testedClass;

    public function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->balanceCalculator = $this->createMock(AccountBalanceCalculator::class);
        $this->eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $this->testedClass = new AccountService(
            $this->entityManager,
            $this->balanceCalculator,
            $this->eventDispatcher
        );
    }

    /**
     * @throws OptimisticLockException
     * @throws PessimisticLockException
     */
    public function testUpdateAccountBalanceSuccessful(): void
    {
        /** @var MockObject|Transaction $transaction */
        $transaction = $this->createMock(Transaction::class);
        $accountMock = $this->createMock(Account::class);
        $accountMock->expects($this->once())->method('addTransaction')->with($transaction)->willReturnSelf();
        $transaction->expects($this->once())->method('getAccount')->willReturn($accountMock);
        $this->entityManager->expects($this->once())->method('lock')->with($accountMock, LockMode::PESSIMISTIC_READ);
        $this
            ->balanceCalculator
            ->expects($this->once())
            ->method('calculate')
            ->with($accountMock, $transaction)
            ->willReturn($accountMock);
        $this->entityManager->expects($this->once())->method('persist')->with($accountMock);
        $this->entityManager->expects($this->once())->method('flush');
        $this->eventDispatcher->expects($this->once())->method('dispatch');
        $account = $this->testedClass->updateAccountBalance($transaction);
        $this->assertSame($accountMock, $account, 'update account algorithm went as expected');
    }

    /**
     * @dataProvider availableExceptions
     *
     * @param string $exceptionClass
     *
     * @throws OptimisticLockException
     * @throws PessimisticLockException
     */
    public function testUpdateAccountBalanceException(string $exceptionClass): void
    {
        $this->expectException($exceptionClass);
        $this->assertTrue(true, 'update account algorithm thrown transaction exception');
        /** @var MockObject|Transaction $transaction */
        $transaction = $this->createMock(Transaction::class);
        $accountMock = $this->createMock(Account::class);
        $transaction->expects($this->once())->method('getAccount')->willReturn($accountMock);
        /** @var MockObject|Exception $exceptionMock */
        $exceptionMock = $this->createMock($exceptionClass);
        $this
            ->entityManager
            ->expects($this->once())
            ->method('lock')
            ->with($accountMock, LockMode::PESSIMISTIC_READ)
            ->willThrowException($exceptionMock);
        $this->testedClass->updateAccountBalance($transaction);
    }

    /**
     * @return array
     */
    public function availableExceptions(): array
    {
        return [
            ['exceptionClass' => PessimisticLockException::class],
            ['exceptionClass' => OptimisticLockException::class],
        ];
    }
}