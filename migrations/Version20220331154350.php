<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220331154350 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sample_datas ADD category_id INT DEFAULT NULL, ADD classement_id INT DEFAULT NULL, ADD name VARCHAR(255) NOT NULL, ADD price DOUBLE PRECISION NOT NULL, ADD weight DOUBLE PRECISION DEFAULT NULL, ADD quantity INT DEFAULT 1 NOT NULL, ADD detail VARCHAR(255) DEFAULT NULL, ADD description LONGTEXT DEFAULT NULL, ADD image_name VARCHAR(255) DEFAULT NULL, ADD updated_at DATETIME NOT NULL, ADD gamme INT NOT NULL, ADD reference_id VARCHAR(10) NOT NULL');
        $this->addSql('CREATE INDEX IDX_C7C9071512469DE2 ON sample_datas (category_id)');
        $this->addSql('CREATE INDEX IDX_C7C90715A513A63E ON sample_datas (classement_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sample_datas DROP FOREIGN KEY FK_C7C9071512469DE2');
        $this->addSql('ALTER TABLE sample_datas DROP FOREIGN KEY FK_C7C90715A513A63E');
        $this->addSql('DROP INDEX IDX_C7C9071512469DE2 ON sample_datas');
        $this->addSql('DROP INDEX IDX_C7C90715A513A63E ON sample_datas');
        $this->addSql('ALTER TABLE sample_datas DROP category_id, DROP classement_id, DROP name, DROP price, DROP weight, DROP quantity, DROP detail, DROP description, DROP image_name, DROP updated_at, DROP gamme, DROP reference_id');
    }
}
