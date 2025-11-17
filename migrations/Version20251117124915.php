<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251117124915 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE historique_operation_document (id INT IDENTITY NOT NULL, type_operation_id INT, type_document_id INT, numero_document NVARCHAR(50), utilisateur NVARCHAR(50), created_at DATETIME2(6) NOT NULL, updated_at DATETIME2(6), PRIMARY KEY (id))');
        $this->addSql('CREATE INDEX IDX_7B383AB0C3EF8F86 ON historique_operation_document (type_operation_id)');
        $this->addSql('CREATE INDEX IDX_7B383AB08826AFA6 ON historique_operation_document (type_document_id)');
        $this->addSql('CREATE TABLE type_document (id INT IDENTITY NOT NULL, type_documenet NVARCHAR(10) NOT NULL, libelle_document NVARCHAR(255) NOT NULL, created_at DATETIME2(6) NOT NULL, updated_at DATETIME2(6), PRIMARY KEY (id))');
        $this->addSql('CREATE TABLE type_operation (id INT IDENTITY NOT NULL, type_operation NVARCHAR(50) NOT NULL, created_at DATETIME2(6) NOT NULL, updated_at DATETIME2(6), PRIMARY KEY (id))');
        $this->addSql('ALTER TABLE historique_operation_document ADD CONSTRAINT FK_7B383AB0C3EF8F86 FOREIGN KEY (type_operation_id) REFERENCES type_operation (id)');
        $this->addSql('ALTER TABLE historique_operation_document ADD CONSTRAINT FK_7B383AB08826AFA6 FOREIGN KEY (type_document_id) REFERENCES type_document (id)');
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
        $this->addSql('ALTER TABLE historique_operation_document DROP CONSTRAINT FK_7B383AB0C3EF8F86');
        $this->addSql('ALTER TABLE historique_operation_document DROP CONSTRAINT FK_7B383AB08826AFA6');
        $this->addSql('DROP TABLE historique_operation_document');
        $this->addSql('DROP TABLE type_document');
        $this->addSql('DROP TABLE type_operation');
    }
}
