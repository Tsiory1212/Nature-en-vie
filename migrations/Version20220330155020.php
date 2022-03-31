<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220330155020 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `order` ADD plan_id_id INT DEFAULT NULL, CHANGE cart cart LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\'');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F52993982CE2DBAB FOREIGN KEY (plan_id_id) REFERENCES subscription_plan (id)');
        $this->addSql('CREATE INDEX IDX_F52993982CE2DBAB ON `order` (plan_id_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F52993982CE2DBAB');
        $this->addSql('DROP INDEX IDX_F52993982CE2DBAB ON `order`');
        $this->addSql('ALTER TABLE `order` DROP plan_id_id, CHANGE cart cart LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:array)\'');
    }
}
