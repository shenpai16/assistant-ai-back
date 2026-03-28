<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260328170329 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE payment (id INT AUTO_INCREMENT NOT NULL, stripe_payment_intent_id VARCHAR(255) NOT NULL, amout INT NOT NULL, currency VARCHAR(255) NOT NULL, status VARCHAR(255) NOT NULL, paid_at DATETIME NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE subscription (id INT AUTO_INCREMENT NOT NULL, strip_subscription_id VARCHAR(255) NOT NULL, strip_price_id VARCHAR(255) NOT NULL, status VARCHAR(255) NOT NULL, current_period_end DATETIME NOT NULL, cancel_at_period_end TINYINT NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE user ADD stripe_customer_id VARCHAR(255) NOT NULL, ADD stripe_default_payment_method VARCHAR(255) DEFAULT NULL, ADD stripe_portal_url VARCHAR(255) DEFAULT NULL, ADD subscription_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6499A1887DC FOREIGN KEY (subscription_id) REFERENCES subscription (id)');
        $this->addSql('CREATE INDEX IDX_8D93D6499A1887DC ON user (subscription_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE payment');
        $this->addSql('DROP TABLE subscription');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6499A1887DC');
        $this->addSql('DROP INDEX IDX_8D93D6499A1887DC ON user');
        $this->addSql('ALTER TABLE user DROP stripe_customer_id, DROP stripe_default_payment_method, DROP stripe_portal_url, DROP subscription_id');
    }
}
