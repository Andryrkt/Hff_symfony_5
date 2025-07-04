<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250704120605 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'recreation de tous les tables';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE agence (id INT IDENTITY NOT NULL, code NVARCHAR(10) NOT NULL, nom NVARCHAR(100) NOT NULL, created_at DATETIME2(6) NOT NULL, updated_at DATETIME2(6), PRIMARY KEY (id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_64C19AA977153098 ON agence (code) WHERE code IS NOT NULL');
        $this->addSql('CREATE TABLE agence_service (id INT IDENTITY NOT NULL, agence_id INT, service_id INT, code NVARCHAR(20) NOT NULL, responsable NVARCHAR(100), created_at DATETIME2(6) NOT NULL, updated_at DATETIME2(6), PRIMARY KEY (id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_DD69B2E677153098 ON agence_service (code) WHERE code IS NOT NULL');
        $this->addSql('CREATE INDEX IDX_DD69B2E6D725330D ON agence_service (agence_id)');
        $this->addSql('CREATE INDEX IDX_DD69B2E6ED5CA9E6 ON agence_service (service_id)');
        $this->addSql('CREATE TABLE application (id INT IDENTITY NOT NULL, name NVARCHAR(100) NOT NULL, description NVARCHAR(255), PRIMARY KEY (id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_A45BDDC15E237E06 ON application (name) WHERE name IS NOT NULL');
        $this->addSql('CREATE TABLE group_access (id INT IDENTITY NOT NULL, group_id INT, application_id INT, agence_id INT, service_id INT, access_type NVARCHAR(30) NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE INDEX IDX_D5EFF384FE54D947 ON group_access (group_id)');
        $this->addSql('CREATE INDEX IDX_D5EFF3843E030ACD ON group_access (application_id)');
        $this->addSql('CREATE INDEX IDX_D5EFF384D725330D ON group_access (agence_id)');
        $this->addSql('CREATE INDEX IDX_D5EFF384ED5CA9E6 ON group_access (service_id)');
        $this->addSql('CREATE TABLE personnel (id INT IDENTITY NOT NULL, agence_service_id INT, nom NVARCHAR(200) NOT NULL, prenom NVARCHAR(255) NOT NULL, created_at DATETIME2(6) NOT NULL, updated_at DATETIME2(6), PRIMARY KEY (id))');
        $this->addSql('CREATE INDEX IDX_A6BCF3DE29B37C39 ON personnel (agence_service_id)');
        $this->addSql('CREATE TABLE service (id INT IDENTITY NOT NULL, code NVARCHAR(10) NOT NULL, nom NVARCHAR(100) NOT NULL, created_at DATETIME2(6) NOT NULL, updated_at DATETIME2(6), PRIMARY KEY (id))');
        $this->addSql('CREATE TABLE [user] (id INT IDENTITY NOT NULL, personnel_id INT, username NVARCHAR(180) NOT NULL, roles VARCHAR(MAX) NOT NULL, fullname NVARCHAR(255), email NVARCHAR(255), matricule NVARCHAR(255), numero_telephone NVARCHAR(255), poste NVARCHAR(255), created_at DATETIME2(6) NOT NULL, updated_at DATETIME2(6), PRIMARY KEY (id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649F85E0677 ON [user] (username) WHERE username IS NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D6491C109075 ON [user] (personnel_id) WHERE personnel_id IS NOT NULL');
        $this->addSql('EXEC sp_addextendedproperty N\'MS_Description\', N\'(DC2Type:json)\', N\'SCHEMA\', \'dbo\', N\'TABLE\', \'user\', N\'COLUMN\', \'roles\'');
        $this->addSql('CREATE TABLE users_groups (user_id INT NOT NULL, group_id INT NOT NULL, PRIMARY KEY (user_id, group_id))');
        $this->addSql('CREATE INDEX IDX_FF8AB7E0A76ED395 ON users_groups (user_id)');
        $this->addSql('CREATE INDEX IDX_FF8AB7E0FE54D947 ON users_groups (group_id)');
        $this->addSql('CREATE TABLE user_access (id INT IDENTITY NOT NULL, users_id INT, agence_id INT, service_id INT, application_id INT, access_type NVARCHAR(30) NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE INDEX IDX_633B306967B3B43D ON user_access (users_id)');
        $this->addSql('CREATE INDEX IDX_633B3069D725330D ON user_access (agence_id)');
        $this->addSql('CREATE INDEX IDX_633B3069ED5CA9E6 ON user_access (service_id)');
        $this->addSql('CREATE INDEX IDX_633B30693E030ACD ON user_access (application_id)');
        $this->addSql('CREATE TABLE user_group (id INT IDENTITY NOT NULL, name NVARCHAR(100) NOT NULL, description NVARCHAR(255), PRIMARY KEY (id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8F02BF9D5E237E06 ON user_group (name) WHERE name IS NOT NULL');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT IDENTITY NOT NULL, body VARCHAR(MAX) NOT NULL, headers VARCHAR(MAX) NOT NULL, queue_name NVARCHAR(190) NOT NULL, created_at DATETIME2(6) NOT NULL, available_at DATETIME2(6) NOT NULL, delivered_at DATETIME2(6), PRIMARY KEY (id))');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)');
        $this->addSql('CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)');
        $this->addSql('CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)');
        $this->addSql('ALTER TABLE agence_service ADD CONSTRAINT FK_DD69B2E6D725330D FOREIGN KEY (agence_id) REFERENCES agence (id)');
        $this->addSql('ALTER TABLE agence_service ADD CONSTRAINT FK_DD69B2E6ED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id)');
        $this->addSql('ALTER TABLE group_access ADD CONSTRAINT FK_D5EFF384FE54D947 FOREIGN KEY (group_id) REFERENCES user_group (id)');
        $this->addSql('ALTER TABLE group_access ADD CONSTRAINT FK_D5EFF3843E030ACD FOREIGN KEY (application_id) REFERENCES application (id)');
        $this->addSql('ALTER TABLE group_access ADD CONSTRAINT FK_D5EFF384D725330D FOREIGN KEY (agence_id) REFERENCES agence (id)');
        $this->addSql('ALTER TABLE group_access ADD CONSTRAINT FK_D5EFF384ED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id)');
        $this->addSql('ALTER TABLE personnel ADD CONSTRAINT FK_A6BCF3DE29B37C39 FOREIGN KEY (agence_service_id) REFERENCES agence_service (id)');
        $this->addSql('ALTER TABLE [user] ADD CONSTRAINT FK_8D93D6491C109075 FOREIGN KEY (personnel_id) REFERENCES personnel (id)');
        $this->addSql('ALTER TABLE users_groups ADD CONSTRAINT FK_FF8AB7E0A76ED395 FOREIGN KEY (user_id) REFERENCES [user] (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE users_groups ADD CONSTRAINT FK_FF8AB7E0FE54D947 FOREIGN KEY (group_id) REFERENCES user_group (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_access ADD CONSTRAINT FK_633B306967B3B43D FOREIGN KEY (users_id) REFERENCES [user] (id)');
        $this->addSql('ALTER TABLE user_access ADD CONSTRAINT FK_633B3069D725330D FOREIGN KEY (agence_id) REFERENCES agence (id)');
        $this->addSql('ALTER TABLE user_access ADD CONSTRAINT FK_633B3069ED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id)');
        $this->addSql('ALTER TABLE user_access ADD CONSTRAINT FK_633B30693E030ACD FOREIGN KEY (application_id) REFERENCES application (id)');
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
        $this->addSql('ALTER TABLE group_access DROP CONSTRAINT FK_D5EFF384FE54D947');
        $this->addSql('ALTER TABLE group_access DROP CONSTRAINT FK_D5EFF3843E030ACD');
        $this->addSql('ALTER TABLE group_access DROP CONSTRAINT FK_D5EFF384D725330D');
        $this->addSql('ALTER TABLE group_access DROP CONSTRAINT FK_D5EFF384ED5CA9E6');
        $this->addSql('ALTER TABLE personnel DROP CONSTRAINT FK_A6BCF3DE29B37C39');
        $this->addSql('ALTER TABLE [user] DROP CONSTRAINT FK_8D93D6491C109075');
        $this->addSql('ALTER TABLE users_groups DROP CONSTRAINT FK_FF8AB7E0A76ED395');
        $this->addSql('ALTER TABLE users_groups DROP CONSTRAINT FK_FF8AB7E0FE54D947');
        $this->addSql('ALTER TABLE user_access DROP CONSTRAINT FK_633B306967B3B43D');
        $this->addSql('ALTER TABLE user_access DROP CONSTRAINT FK_633B3069D725330D');
        $this->addSql('ALTER TABLE user_access DROP CONSTRAINT FK_633B3069ED5CA9E6');
        $this->addSql('ALTER TABLE user_access DROP CONSTRAINT FK_633B30693E030ACD');
        $this->addSql('DROP TABLE agence');
        $this->addSql('DROP TABLE agence_service');
        $this->addSql('DROP TABLE application');
        $this->addSql('DROP TABLE group_access');
        $this->addSql('DROP TABLE personnel');
        $this->addSql('DROP TABLE service');
        $this->addSql('DROP TABLE [user]');
        $this->addSql('DROP TABLE users_groups');
        $this->addSql('DROP TABLE user_access');
        $this->addSql('DROP TABLE user_group');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
