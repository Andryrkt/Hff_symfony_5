<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250721120753 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE demande_ordre_mission (id INT IDENTITY NOT NULL, numero_ordre_mission NVARCHAR(11) NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE TABLE dom_sous_type_document (id INT IDENTITY NOT NULL, code_sous_type NVARCHAR(50) NOT NULL, PRIMARY KEY (id))');
        $this->addSql('DROP TABLE refresh_tokens');
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
        $this->addSql('CREATE TABLE refresh_tokens (id INT IDENTITY NOT NULL, refresh_token NVARCHAR(128) COLLATE French_CI_AS NOT NULL, username NVARCHAR(255) COLLATE French_CI_AS NOT NULL, valid DATETIME2(6) NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE UNIQUE NONCLUSTERED INDEX UNIQ_9BACE7E1C74F2195 ON refresh_tokens (refresh_token) WHERE refresh_token IS NOT NULL');
        $this->addSql('DROP TABLE demande_ordre_mission');
        $this->addSql('DROP TABLE dom_sous_type_document');
    }
}
