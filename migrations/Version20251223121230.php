<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251223121230 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE categorie_ate_app (id INT IDENTITY NOT NULL, libelle_categorie_ate_app NVARCHAR(50) NOT NULL, created_at DATETIME2(6) NOT NULL, updated_at DATETIME2(6), PRIMARY KEY (id))');
        $this->addSql('CREATE TABLE wor_niveau_urgence (id INT IDENTITY NOT NULL, code NVARCHAR(2) NOT NULL, created_at DATETIME2(6) NOT NULL, updated_at DATETIME2(6), PRIMARY KEY (id))');
        $this->addSql('CREATE TABLE wor_type_document (id INT IDENTITY NOT NULL, code_document NVARCHAR(3) NOT NULL, description NVARCHAR(50) NOT NULL, created_at DATETIME2(6) NOT NULL, updated_at DATETIME2(6), PRIMARY KEY (id))');
        $this->addSql('ALTER TABLE ors ADD created_at DATETIME2(6) NOT NULL');
        $this->addSql('ALTER TABLE ors ADD updated_at DATETIME2(6)');
        $this->addSql('ALTER TABLE ors ADD createdBy INT');
        $this->addSql('ALTER TABLE ors ADD CONSTRAINT FK_68CF6EFDD3564642 FOREIGN KEY (createdBy) REFERENCES [user] (id)');
        $this->addSql('CREATE INDEX IDX_68CF6EFDD3564642 ON ors (createdBy)');
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
        $this->addSql('DROP TABLE categorie_ate_app');
        $this->addSql('DROP TABLE wor_niveau_urgence');
        $this->addSql('DROP TABLE wor_type_document');
        $this->addSql('ALTER TABLE ors DROP CONSTRAINT FK_68CF6EFDD3564642');
        $this->addSql('DROP INDEX IDX_68CF6EFDD3564642 ON ors');
        $this->addSql('ALTER TABLE ors DROP COLUMN created_at');
        $this->addSql('ALTER TABLE ors DROP COLUMN updated_at');
        $this->addSql('ALTER TABLE ors DROP COLUMN createdBy');
    }
}
