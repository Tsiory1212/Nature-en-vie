<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220427090240 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F52993987C91EBBB');
        $this->addSql('DROP INDEX UNIQ_F52993987C91EBBB ON `order`');
        $this->addSql('ALTER TABLE `order` DROP pause_delivry_id');
        $this->addSql('ALTER TABLE pause_delivry ADD order_paused_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE pause_delivry ADD CONSTRAINT FK_570EED1EF91C080E FOREIGN KEY (order_paused_id) REFERENCES `order` (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_570EED1EF91C080E ON pause_delivry (order_paused_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `order` ADD pause_delivry_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F52993987C91EBBB FOREIGN KEY (pause_delivry_id) REFERENCES pause_delivry (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F52993987C91EBBB ON `order` (pause_delivry_id)');
        $this->addSql('ALTER TABLE pause_delivry DROP FOREIGN KEY FK_570EED1EF91C080E');
        $this->addSql('DROP INDEX UNIQ_570EED1EF91C080E ON pause_delivry');
        $this->addSql('ALTER TABLE pause_delivry DROP order_paused_id');
    }
}
