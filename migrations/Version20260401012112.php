<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260401012112 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE production_log ADD shift VARCHAR(20) DEFAULT NULL');
        $this->addSql('ALTER TABLE production_log ADD CONSTRAINT FK_24BDAAEF4584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE sales_log ADD shift VARCHAR(20) DEFAULT NULL');
        $this->addSql('ALTER TABLE sales_log ADD CONSTRAINT FK_7A2BC7DF4584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE sales_log ADD CONSTRAINT FK_7A2BC7DF6B20BA36 FOREIGN KEY (worker_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE webauthn_credential ADD CONSTRAINT FK_850123F9A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE production_log DROP FOREIGN KEY FK_24BDAAEF4584665A');
        $this->addSql('ALTER TABLE production_log DROP shift');
        $this->addSql('ALTER TABLE sales_log DROP FOREIGN KEY FK_7A2BC7DF4584665A');
        $this->addSql('ALTER TABLE sales_log DROP FOREIGN KEY FK_7A2BC7DF6B20BA36');
        $this->addSql('ALTER TABLE sales_log DROP shift');
        $this->addSql('ALTER TABLE webauthn_credential DROP FOREIGN KEY FK_850123F9A76ED395');
    }
}
