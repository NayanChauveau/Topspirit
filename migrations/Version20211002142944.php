<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211002142944 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE platform ADD end_of_subscription DATE DEFAULT NULL COMMENT \'(DC2Type:date_immutable)\'');
        $this->addSql('ALTER TABLE subscriptions ADD payment_intent VARCHAR(255) NOT NULL, ADD customer_id VARCHAR(255) NOT NULL, ADD product_id VARCHAR(255) NOT NULL, ADD creation_date DATE NOT NULL COMMENT \'(DC2Type:date_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE platform DROP end_of_subscription');
        $this->addSql('ALTER TABLE subscriptions DROP payment_intent, DROP customer_id, DROP product_id, DROP creation_date');
    }
}
