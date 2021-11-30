<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211129100928 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cart_subscription ADD name_product_plan VARCHAR(30) NOT NULL, ADD name_subscription_plan VARCHAR(255) NOT NULL, ADD description_subscription_plan VARCHAR(255) NOT NULL, ADD price_subscription VARCHAR(10) NOT NULL, ADD duration_month_subscription INT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cart_subscription DROP name_product_plan, DROP name_subscription_plan, DROP description_subscription_plan, DROP price_subscription, DROP duration_month_subscription');
    }
}
