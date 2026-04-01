<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260331232150 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE product (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, price DOUBLE PRECISION NOT NULL, category VARCHAR(100) NOT NULL, description LONGTEXT DEFAULT NULL, image_path VARCHAR(255) DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE production_log (id INT AUTO_INCREMENT NOT NULL, quantity INT NOT NULL, date DATE NOT NULL, product_id INT NOT NULL, INDEX IDX_24BDAAEF4584665A (product_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE purchase (id INT AUTO_INCREMENT NOT NULL, description VARCHAR(255) NOT NULL, amount DOUBLE PRECISION NOT NULL, date DATE NOT NULL, image_path VARCHAR(255) DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE sales_log (id INT AUTO_INCREMENT NOT NULL, quantity_sold INT NOT NULL, announced_total DOUBLE PRECISION NOT NULL, calculated_total DOUBLE PRECISION NOT NULL, date DATE NOT NULL, product_id INT NOT NULL, worker_id INT NOT NULL, INDEX IDX_7A2BC7DF4584665A (product_id), INDEX IDX_7A2BC7DF6B20BA36 (worker_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(180) NOT NULL, roles JSON NOT NULL, password_hash VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649F85E0677 (username), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE webauthn_credential (id INT AUTO_INCREMENT NOT NULL, public_key LONGTEXT NOT NULL, credential_id VARCHAR(200) NOT NULL, sign_count INT NOT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, last_used_at DATETIME NOT NULL, user_id INT NOT NULL, UNIQUE INDEX UNIQ_850123F92558A7A5 (credential_id), INDEX IDX_850123F9A76ED395 (user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE production_log ADD CONSTRAINT FK_24BDAAEF4584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE sales_log ADD CONSTRAINT FK_7A2BC7DF4584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE sales_log ADD CONSTRAINT FK_7A2BC7DF6B20BA36 FOREIGN KEY (worker_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE webauthn_credential ADD CONSTRAINT FK_850123F9A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE production_log DROP FOREIGN KEY FK_24BDAAEF4584665A');
        $this->addSql('ALTER TABLE sales_log DROP FOREIGN KEY FK_7A2BC7DF4584665A');
        $this->addSql('ALTER TABLE sales_log DROP FOREIGN KEY FK_7A2BC7DF6B20BA36');
        $this->addSql('ALTER TABLE webauthn_credential DROP FOREIGN KEY FK_850123F9A76ED395');
        $this->addSql('DROP TABLE product');
        $this->addSql('DROP TABLE production_log');
        $this->addSql('DROP TABLE purchase');
        $this->addSql('DROP TABLE sales_log');
        $this->addSql('DROP TABLE `user`');
        $this->addSql('DROP TABLE webauthn_credential');
    }
}
