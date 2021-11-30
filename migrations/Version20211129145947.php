<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211129145947 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE pause_livraison (id INT AUTO_INCREMENT NOT NULL, start_date DATETIME NOT NULL, end_date DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE facture_abonnement ADD pause_livraison_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE facture_abonnement ADD CONSTRAINT FK_DF0D87BF3B2B27D8 FOREIGN KEY (pause_livraison_id) REFERENCES pause_livraison (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_DF0D87BF3B2B27D8 ON facture_abonnement (pause_livraison_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE facture_abonnement DROP FOREIGN KEY FK_DF0D87BF3B2B27D8');
        $this->addSql('DROP TABLE pause_livraison');
        $this->addSql('DROP INDEX UNIQ_DF0D87BF3B2B27D8 ON facture_abonnement');
        $this->addSql('ALTER TABLE facture_abonnement DROP pause_livraison_id');
    }
}
