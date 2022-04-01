<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220331185542 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sample_datas ADD volume VARCHAR(10) DEFAULT NULL, ADD availability TINYINT(1) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sample_datas DROP FOREIGN KEY FK_C7C9071512469DE2');
        $this->addSql('ALTER TABLE sample_datas DROP FOREIGN KEY FK_C7C90715A513A63E');
        $this->addSql('ALTER TABLE sample_datas DROP volume, DROP availability');
    }
}
