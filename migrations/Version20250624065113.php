<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250624065113 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE agence (id INT IDENTITY NOT NULL, code NVARCHAR(10) NOT NULL, nom NVARCHAR(100) NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_64C19AA977153098 ON agence (code) WHERE code IS NOT NULL');
        $this->addSql('CREATE TABLE agence_service (id INT IDENTITY NOT NULL, agence_id INT, service_id INT, code NVARCHAR(20) NOT NULL, responsable NVARCHAR(100), PRIMARY KEY (id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_DD69B2E677153098 ON agence_service (code) WHERE code IS NOT NULL');
        $this->addSql('CREATE INDEX IDX_DD69B2E6D725330D ON agence_service (agence_id)');
        $this->addSql('CREATE INDEX IDX_DD69B2E6ED5CA9E6 ON agence_service (service_id)');
        $this->addSql('CREATE TABLE personnel (id INT IDENTITY NOT NULL, agence_service_id INT, nom NVARCHAR(200) NOT NULL, prenom NVARCHAR(255) NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE INDEX IDX_A6BCF3DE29B37C39 ON personnel (agence_service_id)');
        $this->addSql('CREATE TABLE service (id INT IDENTITY NOT NULL, code NVARCHAR(10) NOT NULL, nom NVARCHAR(100) NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE TABLE [user] (id INT IDENTITY NOT NULL, personnel_id INT, username NVARCHAR(180) NOT NULL, roles VARCHAR(MAX) NOT NULL, fullname NVARCHAR(255), email NVARCHAR(255), matricule NVARCHAR(255), numero_telephone NVARCHAR(255), poste NVARCHAR(255), PRIMARY KEY (id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649F85E0677 ON [user] (username) WHERE username IS NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D6491C109075 ON [user] (personnel_id) WHERE personnel_id IS NOT NULL');
        $this->addSql('EXEC sp_addextendedproperty N\'MS_Description\', N\'(DC2Type:json)\', N\'SCHEMA\', \'dbo\', N\'TABLE\', \'user\', N\'COLUMN\', \'roles\'');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT IDENTITY NOT NULL, body VARCHAR(MAX) NOT NULL, headers VARCHAR(MAX) NOT NULL, queue_name NVARCHAR(190) NOT NULL, created_at DATETIME2(6) NOT NULL, available_at DATETIME2(6) NOT NULL, delivered_at DATETIME2(6), PRIMARY KEY (id))');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)');
        $this->addSql('CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)');
        $this->addSql('CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)');
        $this->addSql('ALTER TABLE agence_service ADD CONSTRAINT FK_DD69B2E6D725330D FOREIGN KEY (agence_id) REFERENCES agence (id)');
        $this->addSql('ALTER TABLE agence_service ADD CONSTRAINT FK_DD69B2E6ED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id)');
        $this->addSql('ALTER TABLE personnel ADD CONSTRAINT FK_A6BCF3DE29B37C39 FOREIGN KEY (agence_service_id) REFERENCES agence_service (id)');
        $this->addSql('ALTER TABLE [user] ADD CONSTRAINT FK_8D93D6491C109075 FOREIGN KEY (personnel_id) REFERENCES personnel (id)');
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
        $this->addSql('ALTER TABLE agence_service DROP CONSTRAINT FK_DD69B2E6D725330D');
        $this->addSql('ALTER TABLE agence_service DROP CONSTRAINT FK_DD69B2E6ED5CA9E6');
        $this->addSql('ALTER TABLE personnel DROP CONSTRAINT FK_A6BCF3DE29B37C39');
        $this->addSql('ALTER TABLE [user] DROP CONSTRAINT FK_8D93D6491C109075');
        $this->addSql('DROP TABLE agence');
        $this->addSql('DROP TABLE agence_service');
        $this->addSql('DROP TABLE personnel');
        $this->addSql('DROP TABLE service');
        $this->addSql('DROP TABLE [user]');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
