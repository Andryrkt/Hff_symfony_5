<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250723043451 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE statut_demande (id INT IDENTITY NOT NULL, code_application NVARCHAR(3) NOT NULL, code_statut NVARCHAR(3) NOT NULL, description NVARCHAR(50) NOT NULL, created_at DATETIME2(6) NOT NULL, updated_at DATETIME2(6), PRIMARY KEY (id))');
        $this->addSql('ALTER TABLE demande_ordre_mission ADD mode_paiement NVARCHAR(50)');
        $this->addSql('ALTER TABLE demande_ordre_mission ADD piece_jointe1 NVARCHAR(50)');
        $this->addSql('ALTER TABLE demande_ordre_mission ADD piece_jointe2 NVARCHAR(50)');
        $this->addSql('ALTER TABLE demande_ordre_mission ADD code_statut NVARCHAR(3)');
        $this->addSql('ALTER TABLE demande_ordre_mission ADD numero_tel NVARCHAR(10)');
        $this->addSql('ALTER TABLE demande_ordre_mission ADD devis NVARCHAR(3)');
        $this->addSql('ALTER TABLE demande_ordre_mission ADD fiche NVARCHAR(20)');
        $this->addSql('ALTER TABLE demande_ordre_mission ADD numero_vehicule NVARCHAR(20)');
        $this->addSql('ALTER TABLE demande_ordre_mission ADD supplement_journalier DOUBLE PRECISION');
        $this->addSql('ALTER TABLE demande_ordre_mission ADD indemnite_chantier DOUBLE PRECISION');
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
        $this->addSql('DROP TABLE statut_demande');
        $this->addSql('ALTER TABLE demande_ordre_mission DROP COLUMN mode_paiement');
        $this->addSql('ALTER TABLE demande_ordre_mission DROP COLUMN piece_jointe1');
        $this->addSql('ALTER TABLE demande_ordre_mission DROP COLUMN piece_jointe2');
        $this->addSql('ALTER TABLE demande_ordre_mission DROP COLUMN code_statut');
        $this->addSql('ALTER TABLE demande_ordre_mission DROP COLUMN numero_tel');
        $this->addSql('ALTER TABLE demande_ordre_mission DROP COLUMN devis');
        $this->addSql('ALTER TABLE demande_ordre_mission DROP COLUMN fiche');
        $this->addSql('ALTER TABLE demande_ordre_mission DROP COLUMN numero_vehicule');
        $this->addSql('ALTER TABLE demande_ordre_mission DROP COLUMN supplement_journalier');
        $this->addSql('ALTER TABLE demande_ordre_mission DROP COLUMN indemnite_chantier');
    }
}
