<?php
declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Account;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    private const ACCOUNTS = [
        [
            'active_balance' => 10000.000,
            'blocked_balance' => 150.000,
            'total_balance' => 10150.000,
            'holder_name' => 'Nikita',
            'holder_last_name' => 'Pimoshenko',
        ],
        [
            'active_balance' => 20000.000,
            'blocked_balance' => 250.000,
            'total_balance' => 20250.000,
            'holder_name' => 'Nikita',
            'holder_last_name' => 'Pimoshenko',
        ],
    ];

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        foreach (self::ACCOUNTS as $accountData) {
            $account = new Account();
            $account->setActiveBalance($accountData['active_balance']);
            $account->setBlockedBalance($accountData['blocked_balance']);
            $account->setTotalBalance($accountData['total_balance']);
            $account->setHolderName($accountData['holder_name']);
            $account->setHolderLastName($accountData['holder_last_name']);

            $manager->persist($account);
            $manager->flush();
        }
    }
}