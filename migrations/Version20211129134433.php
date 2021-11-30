<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211129134433 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE facture_abonnement (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, cart_subscription_id INT NOT NULL, subcription_id VARCHAR(30) NOT NULL, created_at DATETIME NOT NULL, subscription_end DATETIME NOT NULL, INDEX IDX_DF0D87BFA76ED395 (user_id), INDEX IDX_DF0D87BFE36E012C (cart_subscription_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE facture_abonnement ADD CONSTRAINT FK_DF0D87BFA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE facture_abonnement ADD CONSTRAINT FK_DF0D87BFE36E012C FOREIGN KEY (cart_subscription_id) REFERENCES cart_subscription (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE facture_abonnement');
    }
}
