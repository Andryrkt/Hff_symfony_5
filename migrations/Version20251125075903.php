<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251125075903 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE Demande_ordre_mission ADD createdBy INT');
        $this->addSql('ALTER TABLE Demande_ordre_mission ADD CONSTRAINT FK_4C819A36D3564642 FOREIGN KEY (createdBy) REFERENCES [user] (id)');
        $this->addSql('CREATE INDEX IDX_4C819A36D3564642 ON Demande_ordre_mission (createdBy)');
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
        $this->addSql('ALTER TABLE Demande_ordre_mission DROP CONSTRAINT FK_4C819A36D3564642');
        $this->addSql('DROP INDEX IDX_4C819A36D3564642 ON Demande_ordre_mission');
        $this->addSql('ALTER TABLE Demande_ordre_mission DROP COLUMN createdBy');
    }
}
