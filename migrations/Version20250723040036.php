<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250723040036 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE demande_ordre_mission ADD motif_autres_depense1 NVARCHAR(50)');
        $this->addSql('ALTER TABLE demande_ordre_mission ADD montant_autres_depense1 DOUBLE PRECISION');
        $this->addSql('ALTER TABLE demande_ordre_mission ADD motif_autres_depense2 NVARCHAR(50)');
        $this->addSql('ALTER TABLE demande_ordre_mission ADD montant_autres_depense2 DOUBLE PRECISION');
        $this->addSql('ALTER TABLE demande_ordre_mission ADD motif_autres_depense3 NVARCHAR(50)');
        $this->addSql('ALTER TABLE demande_ordre_mission ADD montant_autres_depense3 DOUBLE PRECISION NOT NULL');
        $this->addSql('ALTER TABLE demande_ordre_mission ADD indemnite_forfaitaire DOUBLE PRECISION');
        $this->addSql('ALTER TABLE demande_ordre_mission ADD total_indemnite_forfaitaire DOUBLE PRECISION');
        $this->addSql('ALTER TABLE demande_ordre_mission ADD total_general_payer DOUBLE PRECISION');
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
        $this->addSql('ALTER TABLE demande_ordre_mission DROP COLUMN motif_autres_depense1');
        $this->addSql('ALTER TABLE demande_ordre_mission DROP COLUMN montant_autres_depense1');
        $this->addSql('ALTER TABLE demande_ordre_mission DROP COLUMN motif_autres_depense2');
        $this->addSql('ALTER TABLE demande_ordre_mission DROP COLUMN montant_autres_depense2');
        $this->addSql('ALTER TABLE demande_ordre_mission DROP COLUMN motif_autres_depense3');
        $this->addSql('ALTER TABLE demande_ordre_mission DROP COLUMN montant_autres_depense3');
        $this->addSql('ALTER TABLE demande_ordre_mission DROP COLUMN indemnite_forfaitaire');
        $this->addSql('ALTER TABLE demande_ordre_mission DROP COLUMN total_indemnite_forfaitaire');
        $this->addSql('ALTER TABLE demande_ordre_mission DROP COLUMN total_general_payer');
    }
}
