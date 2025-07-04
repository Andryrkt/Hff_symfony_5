<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250704125221 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'rectification du nom de table agenceService en AgenceServiceIrium';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE personnel DROP CONSTRAINT FK_A6BCF3DE29B37C39');
        $this->addSql('CREATE TABLE agence_service_irium (id INT IDENTITY NOT NULL, agence_id INT, service_id INT, code NVARCHAR(20) NOT NULL, responsable NVARCHAR(100), created_at DATETIME2(6) NOT NULL, updated_at DATETIME2(6), PRIMARY KEY (id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5626BB5277153098 ON agence_service_irium (code) WHERE code IS NOT NULL');
        $this->addSql('CREATE INDEX IDX_5626BB52D725330D ON agence_service_irium (agence_id)');
        $this->addSql('CREATE INDEX IDX_5626BB52ED5CA9E6 ON agence_service_irium (service_id)');
        $this->addSql('ALTER TABLE agence_service_irium ADD CONSTRAINT FK_5626BB52D725330D FOREIGN KEY (agence_id) REFERENCES agence (id)');
        $this->addSql('ALTER TABLE agence_service_irium ADD CONSTRAINT FK_5626BB52ED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id)');
        $this->addSql('ALTER TABLE agence_service DROP CONSTRAINT FK_DD69B2E6D725330D');
        $this->addSql('ALTER TABLE agence_service DROP CONSTRAINT FK_DD69B2E6ED5CA9E6');
        $this->addSql('DROP TABLE agence_service');
        $this->addSql('ALTER TABLE personnel ADD CONSTRAINT FK_A6BCF3DE29B37C39 FOREIGN KEY (agence_service_id) REFERENCES agence_service_irium (id)');
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
        $this->addSql('CREATE TABLE agence_service (id INT IDENTITY NOT NULL, agence_id INT, service_id INT, code NVARCHAR(20) COLLATE French_CI_AS NOT NULL, responsable NVARCHAR(100) COLLATE French_CI_AS, created_at DATETIME2(6) NOT NULL, updated_at DATETIME2(6), PRIMARY KEY (id))');
        $this->addSql('CREATE UNIQUE NONCLUSTERED INDEX UNIQ_DD69B2E677153098 ON agence_service (code) WHERE code IS NOT NULL');
        $this->addSql('CREATE NONCLUSTERED INDEX IDX_DD69B2E6D725330D ON agence_service (agence_id)');
        $this->addSql('CREATE NONCLUSTERED INDEX IDX_DD69B2E6ED5CA9E6 ON agence_service (service_id)');
        $this->addSql('ALTER TABLE agence_service ADD CONSTRAINT FK_DD69B2E6D725330D FOREIGN KEY (agence_id) REFERENCES agence (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE agence_service ADD CONSTRAINT FK_DD69B2E6ED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE agence_service_irium DROP CONSTRAINT FK_5626BB52D725330D');
        $this->addSql('ALTER TABLE agence_service_irium DROP CONSTRAINT FK_5626BB52ED5CA9E6');
        $this->addSql('DROP TABLE agence_service_irium');
        $this->addSql('ALTER TABLE personnel DROP CONSTRAINT FK_A6BCF3DE29B37C39');
        $this->addSql('ALTER TABLE personnel ADD CONSTRAINT FK_A6BCF3DE29B37C39 FOREIGN KEY (agence_service_id) REFERENCES agence_service (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
    }
}
