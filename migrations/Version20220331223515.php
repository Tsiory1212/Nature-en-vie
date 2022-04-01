<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220331223515 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product ADD availability TINYINT(1) NOT NULL, ADD volume VARCHAR(10) DEFAULT NULL, ADD quantity_unit VARCHAR(20) NOT NULL');
        $this->addSql('ALTER TABLE sample_datas ADD quantity_unit VARCHAR(20) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product DROP availability, DROP volume, DROP quantity_unit');
        $this->addSql('ALTER TABLE sample_datas DROP quantity_unit');
    }
}
