<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251127063316 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE UNIQUE INDEX UNIQ_4C819A36B71377F1 ON Demande_ordre_mission (numero_ordre_mission) WHERE numero_ordre_mission IS NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_A6BCF3DE12B2DC9C ON personnel (matricule) WHERE matricule IS NOT NULL');
        $this->addSql('CREATE INDEX idx_matricule ON personnel (matricule)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D64912B2DC9C ON [user] (matricule) WHERE matricule IS NOT NULL');
        $this->addSql('CREATE INDEX idx_username ON [user] (username)');
        $this->addSql('CREATE INDEX idx_matricule ON [user] (matricule)');
        $this->addSql('EXEC sp_rename N\'user_access.idx_633b30698826afa6\', N\'IDX_633B3069E8B2CC08\', N\'INDEX\'');
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
        $this->addSql('DROP INDEX UNIQ_A6BCF3DE12B2DC9C ON personnel');
        $this->addSql('DROP INDEX idx_matricule ON personnel');
        $this->addSql('DROP INDEX UNIQ_8D93D64912B2DC9C ON [user]');
        $this->addSql('DROP INDEX idx_username ON [user]');
        $this->addSql('DROP INDEX idx_matricule ON [user]');
        $this->addSql('DROP INDEX UNIQ_4C819A36B71377F1 ON Demande_ordre_mission');
        $this->addSql('EXEC sp_rename N\'user_access.idx_633b3069e8b2cc08\', N\'IDX_633B30698826AFA6\', N\'INDEX\'');
    }
}
