<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251103080317 extends AbstractMigration
{
    public function getDescription(): string
    {
        return ' creation de la table Dom';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE Demande_ordre_mission (id INT IDENTITY NOT NULL, id_statut_demande_id INT, sous_type_document_id INT, site_id_id INT, category_id_id INT, agence_emetteur_id INT, service_emetteur_id INT, agence_debiteur_id INT, service_debiteur_id INT, numero_ordre_mission NVARCHAR(11) NOT NULL, matricule NVARCHAR(50) NOT NULL, nom_session_utilisateur NVARCHAR(100) NOT NULL, date_debut DATE NOT NULL, heure_debut NVARCHAR(5) NOT NULL, date_fin DATE NOT NULL, heure_fin NVARCHAR(5) NOT NULL, nombre_jour SMALLINT NOT NULL, motif_deplacement NVARCHAR(100) NOT NULL, client NVARCHAR(100) NOT NULL, lieu_intervention NVARCHAR(100) NOT NULL, vehicule_societe NVARCHAR(3) NOT NULL, indemnite_forfaitaire NVARCHAR(50), total_indemnite_forfaitaire NVARCHAR(50) NOT NULL, motif_autre_depense1 NVARCHAR(50), autres_depense1 NVARCHAR(50), motif_autres_depense2 NVARCHAR(50), autres_depense2 NVARCHAR(50), motif_autres_depense3 NVARCHAR(50), autres_depense3 NVARCHAR(50), total_autres_depenses NVARCHAR(50), total_general_payer NVARCHAR(50), mode_payement NVARCHAR(50), piece_joint01 NVARCHAR(50), piece_joint02 NVARCHAR(50), piece_joint3 NVARCHAR(50), code_statut NVARCHAR(3), numero_tel NVARCHAR(10), nom NVARCHAR(100), prenom NVARCHAR(100), devis NVARCHAR(3), libelle_code_agence_service NVARCHAR(50), fiche NVARCHAR(50), num_vehicule NVARCHAR(50), droit_indemnite NVARCHAR(50), categorie NVARCHAR(50), site NVARCHAR(50), idemnity_depl NVARCHAR(50), emetteur NVARCHAR(50), debiteur NVARCHAR(50), date_heure_modif_statut DATETIME2(6), date_demande DATE, piece_justificatif BIT, created_at DATETIME2(6) NOT NULL, updated_at DATETIME2(6), PRIMARY KEY (id))');
        $this->addSql('CREATE INDEX IDX_4C819A364153B06 ON Demande_ordre_mission (id_statut_demande_id)');
        $this->addSql('CREATE INDEX IDX_4C819A36CCEEF398 ON Demande_ordre_mission (sous_type_document_id)');
        $this->addSql('CREATE INDEX IDX_4C819A36BB1E4E52 ON Demande_ordre_mission (site_id_id)');
        $this->addSql('CREATE INDEX IDX_4C819A369777D11E ON Demande_ordre_mission (category_id_id)');
        $this->addSql('CREATE INDEX IDX_4C819A366F410E1C ON Demande_ordre_mission (agence_emetteur_id)');
        $this->addSql('CREATE INDEX IDX_4C819A36B5730820 ON Demande_ordre_mission (service_emetteur_id)');
        $this->addSql('CREATE INDEX IDX_4C819A363EC7D81B ON Demande_ordre_mission (agence_debiteur_id)');
        $this->addSql('CREATE INDEX IDX_4C819A36E4F5DE27 ON Demande_ordre_mission (service_debiteur_id)');
        $this->addSql('ALTER TABLE Demande_ordre_mission ADD CONSTRAINT FK_4C819A364153B06 FOREIGN KEY (id_statut_demande_id) REFERENCES statut_demande (id)');
        $this->addSql('ALTER TABLE Demande_ordre_mission ADD CONSTRAINT FK_4C819A36CCEEF398 FOREIGN KEY (sous_type_document_id) REFERENCES dom_sous_type_document (id)');
        $this->addSql('ALTER TABLE Demande_ordre_mission ADD CONSTRAINT FK_4C819A36BB1E4E52 FOREIGN KEY (site_id_id) REFERENCES dom_site (id)');
        $this->addSql('ALTER TABLE Demande_ordre_mission ADD CONSTRAINT FK_4C819A369777D11E FOREIGN KEY (category_id_id) REFERENCES dom_categorie (id)');
        $this->addSql('ALTER TABLE Demande_ordre_mission ADD CONSTRAINT FK_4C819A366F410E1C FOREIGN KEY (agence_emetteur_id) REFERENCES agence (id)');
        $this->addSql('ALTER TABLE Demande_ordre_mission ADD CONSTRAINT FK_4C819A36B5730820 FOREIGN KEY (service_emetteur_id) REFERENCES service (id)');
        $this->addSql('ALTER TABLE Demande_ordre_mission ADD CONSTRAINT FK_4C819A363EC7D81B FOREIGN KEY (agence_debiteur_id) REFERENCES agence (id)');
        $this->addSql('ALTER TABLE Demande_ordre_mission ADD CONSTRAINT FK_4C819A36E4F5DE27 FOREIGN KEY (service_debiteur_id) REFERENCES service (id)');
        // $this->addSql('ALTER TABLE user_access DROP ADD CONSTRAINT DF_user_access_created_at');
        // $this->addSql('ALTER TABLE user_access ALTER COLUMN created_at DATETIME2(6) NOT NULL');
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
        $this->addSql('ALTER TABLE Demande_ordre_mission DROP CONSTRAINT FK_4C819A364153B06');
        $this->addSql('ALTER TABLE Demande_ordre_mission DROP CONSTRAINT FK_4C819A36CCEEF398');
        $this->addSql('ALTER TABLE Demande_ordre_mission DROP CONSTRAINT FK_4C819A36BB1E4E52');
        $this->addSql('ALTER TABLE Demande_ordre_mission DROP CONSTRAINT FK_4C819A369777D11E');
        $this->addSql('ALTER TABLE Demande_ordre_mission DROP CONSTRAINT FK_4C819A366F410E1C');
        $this->addSql('ALTER TABLE Demande_ordre_mission DROP CONSTRAINT FK_4C819A36B5730820');
        $this->addSql('ALTER TABLE Demande_ordre_mission DROP CONSTRAINT FK_4C819A363EC7D81B');
        $this->addSql('ALTER TABLE Demande_ordre_mission DROP CONSTRAINT FK_4C819A36E4F5DE27');
        $this->addSql('DROP TABLE Demande_ordre_mission');
        // $this->addSql('ALTER TABLE user_access ALTER COLUMN created_at DATETIME2(6) NOT NULL');
        // $this->addSql('ALTER TABLE user_access ADD CONSTRAINT DF_633B3069_8B8E8428 DEFAULT CURRENT_TIMESTAMP FOR created_at');
    }
}
