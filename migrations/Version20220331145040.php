<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220331145040 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sample_datas ADD category_id INT DEFAULT NULL, ADD classement_id INT DEFAULT NULL, ADD weight DOUBLE PRECISION DEFAULT NULL, ADD detail VARCHAR(255) DEFAULT NULL, ADD image_name VARCHAR(255) DEFAULT NULL, ADD updated_at DATETIME NOT NULL, ADD gamme INT NOT NULL, CHANGE name name VARCHAR(255) NOT NULL, CHANGE description description LONGTEXT DEFAULT NULL, CHANGE price price DOUBLE PRECISION NOT NULL, CHANGE quantity quantity INT DEFAULT 1 NOT NULL, CHANGE ref reference_id VARCHAR(10) NOT NULL');
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
        $this->addSql('ALTER TABLE sample_datas DROP category_id, DROP classement_id, DROP weight, DROP detail, DROP image_name, DROP updated_at, DROP gamme, CHANGE name name VARCHAR(50) CHARACTER SET utf8 NOT NULL COLLATE `utf8_general_ci`, CHANGE price price DOUBLE PRECISION DEFAULT NULL, CHANGE quantity quantity INT NOT NULL, CHANGE description description TEXT CHARACTER SET utf8 NOT NULL COLLATE `utf8_general_ci`, CHANGE reference_id ref VARCHAR(10) CHARACTER SET utf8 NOT NULL COLLATE `utf8_general_ci`');
    }
}
