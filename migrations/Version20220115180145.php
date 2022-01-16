<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220115180145 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_3F744E6A7E3C61F9');
        $this->addSql('CREATE TEMPORARY TABLE __temp__data_source AS SELECT id, owner_id, name, chart_mogul_id FROM data_source');
        $this->addSql('DROP TABLE data_source');
        $this->addSql('CREATE TABLE data_source (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, owner_id INTEGER NOT NULL, name VARCHAR(255) NOT NULL COLLATE BINARY, chart_mogul_id VARCHAR(255) NOT NULL COLLATE BINARY, CONSTRAINT FK_3F744E6A7E3C61F9 FOREIGN KEY (owner_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO data_source (id, owner_id, name, chart_mogul_id) SELECT id, owner_id, name, chart_mogul_id FROM __temp__data_source');
        $this->addSql('DROP TABLE __temp__data_source');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_3F744E6A7E3C61F9 ON data_source (owner_id)');
        $this->addSql('DROP INDEX IDX_6D28840D2F4DD3BB');
        $this->addSql('DROP INDEX IDX_6D28840D9A1887DC');
        $this->addSql('CREATE TEMPORARY TABLE __temp__payment AS SELECT id, subscription_id, paddle_id, amount, currency, payout_date, paid, is_one_off, synced, last_sync_date FROM payment');
        $this->addSql('DROP TABLE payment');
        $this->addSql('CREATE TABLE payment (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, subscription_id INTEGER NOT NULL, paddle_id VARCHAR(255) NOT NULL COLLATE BINARY, amount INTEGER NOT NULL, currency VARCHAR(10) NOT NULL COLLATE BINARY, payout_date DATETIME NOT NULL, paid BOOLEAN NOT NULL, is_one_off BOOLEAN NOT NULL, synced BOOLEAN NOT NULL, last_sync_date DATETIME DEFAULT NULL, CONSTRAINT FK_6D28840D9A1887DC FOREIGN KEY (subscription_id) REFERENCES subscription (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO payment (id, subscription_id, paddle_id, amount, currency, payout_date, paid, is_one_off, synced, last_sync_date) SELECT id, subscription_id, paddle_id, amount, currency, payout_date, paid, is_one_off, synced, last_sync_date FROM __temp__payment');
        $this->addSql('DROP TABLE __temp__payment');
        $this->addSql('CREATE INDEX IDX_6D28840D2F4DD3BB ON payment (paddle_id)');
        $this->addSql('CREATE INDEX IDX_6D28840D9A1887DC ON payment (subscription_id)');
        $this->addSql('DROP INDEX IDX_DD5A5B7D2F4DD3BB');
        $this->addSql('CREATE TEMPORARY TABLE __temp__plan AS SELECT id, paddle_id, name, billing_type, billing_period, initial_price, recurring_price, trial_days, chart_mogul_id, last_sync_date FROM "plan"');
        $this->addSql('DROP TABLE "plan"');
        $this->addSql('CREATE TABLE "plan" (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, owner_id INTEGER NOT NULL, paddle_id VARCHAR(255) NOT NULL COLLATE BINARY, name VARCHAR(512) NOT NULL COLLATE BINARY, billing_type VARCHAR(255) NOT NULL COLLATE BINARY, billing_period INTEGER NOT NULL, initial_price DOUBLE PRECISION NOT NULL, recurring_price DOUBLE PRECISION NOT NULL, trial_days INTEGER NOT NULL, chart_mogul_id VARCHAR(255) DEFAULT NULL COLLATE BINARY, last_sync_date DATETIME DEFAULT NULL, CONSTRAINT FK_DD5A5B7D7E3C61F9 FOREIGN KEY (owner_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO "plan" (id, paddle_id, name, billing_type, billing_period, initial_price, recurring_price, trial_days, chart_mogul_id, last_sync_date) SELECT id, paddle_id, name, billing_type, billing_period, initial_price, recurring_price, trial_days, chart_mogul_id, last_sync_date FROM __temp__plan');
        $this->addSql('DROP TABLE __temp__plan');
        $this->addSql('CREATE INDEX IDX_DD5A5B7D2F4DD3BB ON "plan" (paddle_id)');
        $this->addSql('CREATE INDEX IDX_DD5A5B7D7E3C61F9 ON "plan" (owner_id)');
        $this->addSql('DROP INDEX IDX_A3C664D32F4DD3BB');
        $this->addSql('DROP INDEX IDX_A3C664D39395C3F3');
        $this->addSql('DROP INDEX IDX_A3C664D3E899029B');
        $this->addSql('CREATE TEMPORARY TABLE __temp__subscription AS SELECT id, plan_id, customer_id, paddle_id, state, sign_up_date, quantity, chart_mogul_id, last_sync_date, next_payment_date FROM subscription');
        $this->addSql('DROP TABLE subscription');
        $this->addSql('CREATE TABLE subscription (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, plan_id INTEGER NOT NULL, customer_id INTEGER NOT NULL, owner_id INTEGER NOT NULL, paddle_id VARCHAR(255) NOT NULL COLLATE BINARY, state VARCHAR(255) NOT NULL COLLATE BINARY, sign_up_date DATETIME NOT NULL, quantity INTEGER NOT NULL, chart_mogul_id VARCHAR(255) DEFAULT NULL COLLATE BINARY, last_sync_date DATETIME DEFAULT NULL, next_payment_date DATE NOT NULL, CONSTRAINT FK_A3C664D3E899029B FOREIGN KEY (plan_id) REFERENCES "plan" (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_A3C664D39395C3F3 FOREIGN KEY (customer_id) REFERENCES customer (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_A3C664D37E3C61F9 FOREIGN KEY (owner_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO subscription (id, plan_id, customer_id, paddle_id, state, sign_up_date, quantity, chart_mogul_id, last_sync_date, next_payment_date) SELECT id, plan_id, customer_id, paddle_id, state, sign_up_date, quantity, chart_mogul_id, last_sync_date, next_payment_date FROM __temp__subscription');
        $this->addSql('DROP TABLE __temp__subscription');
        $this->addSql('CREATE INDEX IDX_A3C664D32F4DD3BB ON subscription (paddle_id)');
        $this->addSql('CREATE INDEX IDX_A3C664D39395C3F3 ON subscription (customer_id)');
        $this->addSql('CREATE INDEX IDX_A3C664D3E899029B ON subscription (plan_id)');
        $this->addSql('CREATE INDEX IDX_A3C664D37E3C61F9 ON subscription (owner_id)');
        $this->addSql('DROP INDEX IDX_75EA56E016BA31DB');
        $this->addSql('DROP INDEX IDX_75EA56E0E3BD61CE');
        $this->addSql('DROP INDEX IDX_75EA56E0FB7336F0');
        $this->addSql('CREATE TEMPORARY TABLE __temp__messenger_messages AS SELECT id, body, headers, queue_name, created_at, available_at, delivered_at FROM messenger_messages');
        $this->addSql('DROP TABLE messenger_messages');
        $this->addSql('CREATE TABLE messenger_messages (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, body CLOB NOT NULL COLLATE BINARY, headers CLOB NOT NULL COLLATE BINARY, queue_name VARCHAR(255) NOT NULL COLLATE BINARY, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL)');
        $this->addSql('INSERT INTO messenger_messages (id, body, headers, queue_name, created_at, available_at, delivered_at) SELECT id, body, headers, queue_name, created_at, available_at, delivered_at FROM __temp__messenger_messages');
        $this->addSql('DROP TABLE __temp__messenger_messages');
        $this->addSql('CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)');
        $this->addSql('CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_3F744E6A7E3C61F9');
        $this->addSql('CREATE TEMPORARY TABLE __temp__data_source AS SELECT id, owner_id, name, chart_mogul_id FROM data_source');
        $this->addSql('DROP TABLE data_source');
        $this->addSql('CREATE TABLE data_source (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, owner_id INTEGER NOT NULL, name VARCHAR(255) NOT NULL, chart_mogul_id VARCHAR(255) NOT NULL)');
        $this->addSql('INSERT INTO data_source (id, owner_id, name, chart_mogul_id) SELECT id, owner_id, name, chart_mogul_id FROM __temp__data_source');
        $this->addSql('DROP TABLE __temp__data_source');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_3F744E6A7E3C61F9 ON data_source (owner_id)');
        $this->addSql('DROP INDEX IDX_6D28840D9A1887DC');
        $this->addSql('DROP INDEX IDX_6D28840D2F4DD3BB');
        $this->addSql('CREATE TEMPORARY TABLE __temp__payment AS SELECT id, subscription_id, paddle_id, amount, currency, payout_date, paid, is_one_off, synced, last_sync_date FROM payment');
        $this->addSql('DROP TABLE payment');
        $this->addSql('CREATE TABLE payment (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, subscription_id INTEGER NOT NULL, paddle_id VARCHAR(255) NOT NULL, amount INTEGER NOT NULL, currency VARCHAR(10) NOT NULL, payout_date DATETIME NOT NULL, paid BOOLEAN NOT NULL, is_one_off BOOLEAN NOT NULL, synced BOOLEAN NOT NULL, last_sync_date DATETIME DEFAULT NULL)');
        $this->addSql('INSERT INTO payment (id, subscription_id, paddle_id, amount, currency, payout_date, paid, is_one_off, synced, last_sync_date) SELECT id, subscription_id, paddle_id, amount, currency, payout_date, paid, is_one_off, synced, last_sync_date FROM __temp__payment');
        $this->addSql('DROP TABLE __temp__payment');
        $this->addSql('CREATE INDEX IDX_6D28840D9A1887DC ON payment (subscription_id)');
        $this->addSql('CREATE INDEX IDX_6D28840D2F4DD3BB ON payment (paddle_id)');
        $this->addSql('DROP INDEX IDX_DD5A5B7D7E3C61F9');
        $this->addSql('DROP INDEX IDX_DD5A5B7D2F4DD3BB');
        $this->addSql('CREATE TEMPORARY TABLE __temp__plan AS SELECT id, paddle_id, name, billing_type, billing_period, initial_price, recurring_price, trial_days, chart_mogul_id, last_sync_date FROM "plan"');
        $this->addSql('DROP TABLE "plan"');
        $this->addSql('CREATE TABLE "plan" (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, paddle_id VARCHAR(255) NOT NULL, name VARCHAR(512) NOT NULL, billing_type VARCHAR(255) NOT NULL, billing_period INTEGER NOT NULL, initial_price DOUBLE PRECISION NOT NULL, recurring_price DOUBLE PRECISION NOT NULL, trial_days INTEGER NOT NULL, chart_mogul_id VARCHAR(255) DEFAULT NULL, last_sync_date DATETIME DEFAULT NULL)');
        $this->addSql('INSERT INTO "plan" (id, paddle_id, name, billing_type, billing_period, initial_price, recurring_price, trial_days, chart_mogul_id, last_sync_date) SELECT id, paddle_id, name, billing_type, billing_period, initial_price, recurring_price, trial_days, chart_mogul_id, last_sync_date FROM __temp__plan');
        $this->addSql('DROP TABLE __temp__plan');
        $this->addSql('CREATE INDEX IDX_DD5A5B7D2F4DD3BB ON "plan" (paddle_id)');
        $this->addSql('DROP INDEX IDX_A3C664D3E899029B');
        $this->addSql('DROP INDEX IDX_A3C664D39395C3F3');
        $this->addSql('DROP INDEX IDX_A3C664D37E3C61F9');
        $this->addSql('DROP INDEX IDX_A3C664D32F4DD3BB');
        $this->addSql('CREATE TEMPORARY TABLE __temp__subscription AS SELECT id, plan_id, customer_id, paddle_id, state, sign_up_date, quantity, chart_mogul_id, last_sync_date, next_payment_date FROM subscription');
        $this->addSql('DROP TABLE subscription');
        $this->addSql('CREATE TABLE subscription (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, plan_id INTEGER NOT NULL, customer_id INTEGER NOT NULL, paddle_id VARCHAR(255) NOT NULL, state VARCHAR(255) NOT NULL, sign_up_date DATETIME NOT NULL, quantity INTEGER NOT NULL, chart_mogul_id VARCHAR(255) DEFAULT NULL, last_sync_date DATETIME DEFAULT NULL, next_payment_date DATE NOT NULL)');
        $this->addSql('INSERT INTO subscription (id, plan_id, customer_id, paddle_id, state, sign_up_date, quantity, chart_mogul_id, last_sync_date, next_payment_date) SELECT id, plan_id, customer_id, paddle_id, state, sign_up_date, quantity, chart_mogul_id, last_sync_date, next_payment_date FROM __temp__subscription');
        $this->addSql('DROP TABLE __temp__subscription');
        $this->addSql('CREATE INDEX IDX_A3C664D3E899029B ON subscription (plan_id)');
        $this->addSql('CREATE INDEX IDX_A3C664D39395C3F3 ON subscription (customer_id)');
        $this->addSql('CREATE INDEX IDX_A3C664D32F4DD3BB ON subscription (paddle_id)');
    }
}
