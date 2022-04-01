<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220331212307 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE sample_datas (id INT AUTO_INCREMENT NOT NULL, category_id INT DEFAULT NULL, classement_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, price DOUBLE PRECISION NOT NULL, weight DOUBLE PRECISION DEFAULT NULL, quantity INT DEFAULT 1 NOT NULL, detail VARCHAR(255) DEFAULT NULL, description LONGTEXT DEFAULT NULL, image_name VARCHAR(255) DEFAULT NULL, updated_at DATETIME NOT NULL, gamme INT DEFAULT NULL, reference_id VARCHAR(10) NOT NULL, volume VARCHAR(10) DEFAULT NULL, availability TINYINT(1) NOT NULL, INDEX IDX_C7C9071512469DE2 (category_id), INDEX IDX_C7C90715A513A63E (classement_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sample_datas ADD CONSTRAINT FK_C7C9071512469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE sample_datas ADD CONSTRAINT FK_C7C90715A513A63E FOREIGN KEY (classement_id) REFERENCES classement (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE sample_datas');
    }
}
