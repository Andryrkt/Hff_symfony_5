<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250624070707 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE agence ADD created_at DATETIME2(6) NOT NULL');
        $this->addSql('ALTER TABLE agence ADD updated_at DATETIME2(6)');
        $this->addSql('ALTER TABLE agence_service ADD created_at DATETIME2(6) NOT NULL');
        $this->addSql('ALTER TABLE agence_service ADD updated_at DATETIME2(6)');
        $this->addSql('ALTER TABLE personnel ADD created_at DATETIME2(6) NOT NULL');
        $this->addSql('ALTER TABLE personnel ADD updated_at DATETIME2(6)');
        $this->addSql('ALTER TABLE service ADD created_at DATETIME2(6) NOT NULL');
        $this->addSql('ALTER TABLE service ADD updated_at DATETIME2(6)');
        $this->addSql('ALTER TABLE [user] ADD created_at DATETIME2(6) NOT NULL');
        $this->addSql('ALTER TABLE [user] ADD updated_at DATETIME2(6)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA db_accessadmin');
        $this->addSql('CREATE SCHEMA db_backupoperator');
        $this->addSql('CREATE SCHEMA db_datareader');
        $this->addSql('CREATE SCHEMA db_datawriter');
        $this->addSql('CREATE SCHEMA db_ddladmin');
        $this->addSql('CREATE SCHEMA db_denydatareader');
        $this->addSql('CREATE SCHEMA db_denydatawriter');
        $this->addSql('CREATE SCHEMA db_owner');
        $this->addSql('CREATE SCHEMA db_securityadmin');
        $this->addSql('CREATE SCHEMA dbo');
        $this->addSql('ALTER TABLE agence DROP COLUMN created_at');
        $this->addSql('ALTER TABLE agence DROP COLUMN updated_at');
        $this->addSql('ALTER TABLE agence_service DROP COLUMN created_at');
        $this->addSql('ALTER TABLE agence_service DROP COLUMN updated_at');
        $this->addSql('ALTER TABLE personnel DROP COLUMN created_at');
        $this->addSql('ALTER TABLE personnel DROP COLUMN updated_at');
        $this->addSql('ALTER TABLE service DROP COLUMN created_at');
        $this->addSql('ALTER TABLE service DROP COLUMN updated_at');
        $this->addSql('ALTER TABLE [user] DROP COLUMN created_at');
        $this->addSql('ALTER TABLE [user] DROP COLUMN updated_at');
    }
}
