<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250722120053 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE demande_ordre_mission ADD nombre_jours SMALLINT');
        $this->addSql('ALTER TABLE demande_ordre_mission ADD motif_deplacement VARCHAR(MAX)');
        $this->addSql('ALTER TABLE demande_ordre_mission ADD client NVARCHAR(100)');
        $this->addSql('ALTER TABLE demande_ordre_mission ADD lieu_intervention NVARCHAR(100)');
        $this->addSql('ALTER TABLE demande_ordre_mission ADD vehicule_societe NVARCHAR(3)');
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
        $this->addSql('ALTER TABLE demande_ordre_mission DROP COLUMN nombre_jours');
        $this->addSql('ALTER TABLE demande_ordre_mission DROP COLUMN motif_deplacement');
        $this->addSql('ALTER TABLE demande_ordre_mission DROP COLUMN client');
        $this->addSql('ALTER TABLE demande_ordre_mission DROP COLUMN lieu_intervention');
        $this->addSql('ALTER TABLE demande_ordre_mission DROP COLUMN vehicule_societe');
    }
}
