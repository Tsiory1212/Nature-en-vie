<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220325151822 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE facture_abonnement DROP FOREIGN KEY FK_DF0D87BFE36E012C');
        $this->addSql('ALTER TABLE pause_livraison DROP FOREIGN KEY FK_1DCB4789A2300EF3');
        $this->addSql('CREATE TABLE pause_delivry (id INT AUTO_INCREMENT NOT NULL, start_date DATETIME NOT NULL, end_date DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE subscription_plan (id INT AUTO_INCREMENT NOT NULL, product_id_stripe VARCHAR(50) NOT NULL, plan_id_stripe VARCHAR(50) NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, detailed_description VARCHAR(255) DEFAULT NULL, amount DOUBLE PRECISION NOT NULL, interval_unit VARCHAR(255) NOT NULL, trial_period_days INT DEFAULT NULL, status VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('DROP TABLE cart_subscription');
        $this->addSql('DROP TABLE facture_abonnement');
        $this->addSql('ALTER TABLE `order` ADD pause_delivry_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F52993987C91EBBB FOREIGN KEY (pause_delivry_id) REFERENCES pause_delivry (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F52993987C91EBBB ON `order` (pause_delivry_id)');
        $this->addSql('DROP INDEX UNIQ_1DCB4789A2300EF3 ON pause_livraison');
        $this->addSql('ALTER TABLE pause_livraison DROP facture_abonnement_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F52993987C91EBBB');
        $this->addSql('CREATE TABLE cart_subscription (id INT AUTO_INCREMENT NOT NULL, id_product_plan_paypal VARCHAR(30) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, name_subscription_plan VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, description_subscription_plan VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, price_subscription VARCHAR(10) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, duration_month_subscription INT NOT NULL, id_subscription_plan_paypal VARCHAR(30) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, detailed_description LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, interval_unit VARCHAR(10) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, active TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE facture_abonnement (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, cart_subscription_id INT NOT NULL, subscription_id VARCHAR(30) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, created_at DATETIME NOT NULL, subscription_end DATETIME NOT NULL, INDEX IDX_DF0D87BFA76ED395 (user_id), INDEX IDX_DF0D87BFE36E012C (cart_subscription_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE facture_abonnement ADD CONSTRAINT FK_DF0D87BFA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE facture_abonnement ADD CONSTRAINT FK_DF0D87BFE36E012C FOREIGN KEY (cart_subscription_id) REFERENCES cart_subscription (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('DROP TABLE pause_delivry');
        $this->addSql('DROP TABLE subscription_plan');
        $this->addSql('DROP INDEX UNIQ_F52993987C91EBBB ON `order`');
        $this->addSql('ALTER TABLE `order` DROP pause_delivry_id');
        $this->addSql('ALTER TABLE pause_livraison ADD facture_abonnement_id INT NOT NULL');
        $this->addSql('ALTER TABLE pause_livraison ADD CONSTRAINT FK_1DCB4789A2300EF3 FOREIGN KEY (facture_abonnement_id) REFERENCES facture_abonnement (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1DCB4789A2300EF3 ON pause_livraison (facture_abonnement_id)');
    }
}
