<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220115173323 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE customer (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, paddle_id VARCHAR(255) NOT NULL, email VARCHAR(512) NOT NULL, chart_mogul_id VARCHAR(255) DEFAULT NULL, last_sync_date DATETIME DEFAULT NULL)');
        $this->addSql('CREATE INDEX IDX_81398E092F4DD3BB ON customer (paddle_id)');
        $this->addSql('CREATE TABLE data_source (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, owner_id INTEGER NOT NULL, name VARCHAR(255) NOT NULL, chart_mogul_id VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_3F744E6A7E3C61F9 ON data_source (owner_id)');
        $this->addSql('CREATE TABLE payment (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, subscription_id INTEGER NOT NULL, paddle_id VARCHAR(255) NOT NULL, amount INTEGER NOT NULL, currency VARCHAR(10) NOT NULL, payout_date DATETIME NOT NULL, paid BOOLEAN NOT NULL, is_one_off BOOLEAN NOT NULL, synced BOOLEAN NOT NULL, last_sync_date DATETIME DEFAULT NULL)');
        $this->addSql('CREATE INDEX IDX_6D28840D9A1887DC ON payment (subscription_id)');
        $this->addSql('CREATE INDEX IDX_6D28840D2F4DD3BB ON payment (paddle_id)');
        $this->addSql('CREATE TABLE "plan" (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, paddle_id VARCHAR(255) NOT NULL, name VARCHAR(512) NOT NULL, billing_type VARCHAR(255) NOT NULL, billing_period INTEGER NOT NULL, initial_price DOUBLE PRECISION NOT NULL, recurring_price DOUBLE PRECISION NOT NULL, trial_days INTEGER NOT NULL, chart_mogul_id VARCHAR(255) DEFAULT NULL, last_sync_date DATETIME DEFAULT NULL)');
        $this->addSql('CREATE INDEX IDX_DD5A5B7D2F4DD3BB ON "plan" (paddle_id)');
        $this->addSql('CREATE TABLE subscription (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, plan_id INTEGER NOT NULL, customer_id INTEGER NOT NULL, paddle_id VARCHAR(255) NOT NULL, state VARCHAR(255) NOT NULL, sign_up_date DATETIME NOT NULL, quantity INTEGER NOT NULL, chart_mogul_id VARCHAR(255) DEFAULT NULL, last_sync_date DATETIME DEFAULT NULL, next_payment_date DATE NOT NULL)');
        $this->addSql('CREATE INDEX IDX_A3C664D3E899029B ON subscription (plan_id)');
        $this->addSql('CREATE INDEX IDX_A3C664D39395C3F3 ON subscription (customer_id)');
        $this->addSql('CREATE INDEX IDX_A3C664D32F4DD3BB ON subscription (paddle_id)');
        $this->addSql('CREATE TABLE "user" (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles CLOB NOT NULL --(DC2Type:json)
        , paddle_vendor_id VARCHAR(255) DEFAULT NULL, paddle_api_key VARCHAR(255) DEFAULT NULL, chart_mogul_api_key VARCHAR(255) DEFAULT NULL, sign_up_date DATETIME NOT NULL, last_login_date DATETIME NOT NULL, last_sync_date DATE DEFAULT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON "user" (email)');
        $this->addSql('CREATE TABLE messenger_messages (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, body CLOB NOT NULL, headers CLOB NOT NULL, queue_name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL)');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)');
        $this->addSql('CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)');
        $this->addSql('CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE customer');
        $this->addSql('DROP TABLE data_source');
        $this->addSql('DROP TABLE payment');
        $this->addSql('DROP TABLE "plan"');
        $this->addSql('DROP TABLE subscription');
        $this->addSql('DROP TABLE "user"');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
