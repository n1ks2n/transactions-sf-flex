<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Migrations\AbortMigrationException;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180821084922 extends AbstractMigration
{
    /**
     * @param Schema $schema
     * @throws DBALException
     * @throws AbortMigrationException
     */
    public function up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql',
            'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE transactions_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE accounts_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE transactions (id INT NOT NULL, account_id INT DEFAULT NULL, type SMALLINT NOT NULL, amount NUMERIC(19, 4) NOT NULL, status SMALLINT NOT NULL, request_id UUID NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_EAA81A4C9B6B5FBA ON transactions (account_id)');
        $this->addSql('CREATE INDEX search_idx ON transactions (request_id, status, type)');
        $this->addSql('COMMENT ON COLUMN transactions.type IS \'(DC2Type:transaction_type_enum)\'');
        $this->addSql('COMMENT ON COLUMN transactions.status IS \'(DC2Type:transaction_status_enum)\'');
        $this->addSql('CREATE TABLE accounts (id INT NOT NULL, active_balance NUMERIC(19, 4) NOT NULL, blocked_balance NUMERIC(19, 4) NOT NULL, total_balance NUMERIC(19, 4) NOT NULL, holder_name VARCHAR(255) NOT NULL, holder_last_name VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE transactions ADD CONSTRAINT FK_EAA81A4C9B6B5FBA FOREIGN KEY (account_id) REFERENCES accounts (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    /**
     * @param Schema $schema
     * @throws DBALException
     * @throws AbortMigrationException
     */
    public function down(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql',
            'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE transactions DROP CONSTRAINT FK_EAA81A4C9B6B5FBA');
        $this->addSql('DROP SEQUENCE transactions_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE accounts_id_seq CASCADE');
        $this->addSql('DROP TABLE transactions');
        $this->addSql('DROP TABLE accounts');
    }
}
