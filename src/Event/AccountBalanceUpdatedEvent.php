<?php
declare(strict_types=1);

namespace App\Event;

use App\Entity\Account;
use Symfony\Component\EventDispatcher\Event;

class AccountBalanceUpdatedEvent extends Event
{
    public const NAME = 'account.balance.updated';

    /** @var Account  */
    protected $account;

    /**
     * @param Account $account
     */
    public function __construct(Account $account)
    {
        $this->account = $account;
    }

    /**
     * @return Account
     */
    public function getAccount(): Account
    {
        return $this->account;
    }
}
