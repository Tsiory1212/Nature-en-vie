<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220208154520 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE pause_livraison CHANGE facture_abonnemnt_id facture_abonnement_id INT NOT NULL');
        $this->addSql('ALTER TABLE pause_livraison ADD CONSTRAINT FK_1DCB4789A2300EF3 FOREIGN KEY (facture_abonnement_id) REFERENCES facture_abonnement (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1DCB4789A2300EF3 ON pause_livraison (facture_abonnement_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE pause_livraison DROP FOREIGN KEY FK_1DCB4789A2300EF3');
        $this->addSql('DROP INDEX UNIQ_1DCB4789A2300EF3 ON pause_livraison');
        $this->addSql('ALTER TABLE pause_livraison CHANGE facture_abonnement_id facture_abonnemnt_id INT NOT NULL');
    }
}
