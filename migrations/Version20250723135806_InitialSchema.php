<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250723135806 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE demande_ordre_mission (id INT IDENTITY NOT NULL, dom_demandeur_id INT, dom_personnel_id INT, dom_sous_type_document_id INT, statut_demande_id_id INT, agence_emetteur_id_id INT, service_emetteur_id_id INT, agence_debiteur_id_id INT, service_debiteur_id_id INT, numero_ordre_mission NVARCHAR(11) NOT NULL, date_debut_mission DATETIME2(6), date_fin_mission DATETIME2(6), nombre_jours SMALLINT, motif_deplacement VARCHAR(MAX), client NVARCHAR(100), lieu_intervention NVARCHAR(100), vehicule_societe NVARCHAR(3), motif_autres_depense1 NVARCHAR(50), montant_autres_depense1 DOUBLE PRECISION, motif_autres_depense2 NVARCHAR(50), montant_autres_depense2 DOUBLE PRECISION, motif_autres_depense3 NVARCHAR(50), montant_autres_depense3 DOUBLE PRECISION NOT NULL, indemnite_forfaitaire DOUBLE PRECISION, total_indemnite_forfaitaire DOUBLE PRECISION, total_general_payer DOUBLE PRECISION, mode_paiement NVARCHAR(50), piece_jointe1 NVARCHAR(50), piece_jointe2 NVARCHAR(50), code_statut NVARCHAR(3), numero_tel NVARCHAR(10), devis NVARCHAR(3), fiche NVARCHAR(20), numero_vehicule NVARCHAR(20), supplement_journalier DOUBLE PRECISION, indemnite_chantier DOUBLE PRECISION, PRIMARY KEY (id))');
        $this->addSql('CREATE INDEX IDX_DB49BFEEECD3392B ON demande_ordre_mission (dom_demandeur_id)');
        $this->addSql('CREATE INDEX IDX_DB49BFEE65654707 ON demande_ordre_mission (dom_personnel_id)');
        $this->addSql('CREATE INDEX IDX_DB49BFEE8C1EC44B ON demande_ordre_mission (dom_sous_type_document_id)');
        $this->addSql('CREATE INDEX IDX_DB49BFEE3F5343F1 ON demande_ordre_mission (statut_demande_id_id)');
        $this->addSql('CREATE INDEX IDX_DB49BFEEEC25C3C3 ON demande_ordre_mission (agence_emetteur_id_id)');
        $this->addSql('CREATE INDEX IDX_DB49BFEE5FD929EB ON demande_ordre_mission (service_emetteur_id_id)');
        $this->addSql('CREATE INDEX IDX_DB49BFEE3AD3919B ON demande_ordre_mission (agence_debiteur_id_id)');
        $this->addSql('CREATE INDEX IDX_DB49BFEE892F7BB3 ON demande_ordre_mission (service_debiteur_id_id)');
        $this->addSql('CREATE TABLE dom_sous_type_document (id INT IDENTITY NOT NULL, code_sous_type NVARCHAR(50) NOT NULL, created_at DATETIME2(6) NOT NULL, updated_at DATETIME2(6), PRIMARY KEY (id))');
        $this->addSql('CREATE TABLE statut_demande (id INT IDENTITY NOT NULL, code_application NVARCHAR(3) NOT NULL, code_statut NVARCHAR(3) NOT NULL, description NVARCHAR(50) NOT NULL, created_at DATETIME2(6) NOT NULL, updated_at DATETIME2(6), PRIMARY KEY (id))');
        $this->addSql('ALTER TABLE demande_ordre_mission ADD CONSTRAINT FK_DB49BFEEECD3392B FOREIGN KEY (dom_demandeur_id) REFERENCES [user] (id)');
        $this->addSql('ALTER TABLE demande_ordre_mission ADD CONSTRAINT FK_DB49BFEE65654707 FOREIGN KEY (dom_personnel_id) REFERENCES personnel (id)');
        $this->addSql('ALTER TABLE demande_ordre_mission ADD CONSTRAINT FK_DB49BFEE8C1EC44B FOREIGN KEY (dom_sous_type_document_id) REFERENCES dom_sous_type_document (id)');
        $this->addSql('ALTER TABLE demande_ordre_mission ADD CONSTRAINT FK_DB49BFEE3F5343F1 FOREIGN KEY (statut_demande_id_id) REFERENCES statut_demande (id)');
        $this->addSql('ALTER TABLE demande_ordre_mission ADD CONSTRAINT FK_DB49BFEEEC25C3C3 FOREIGN KEY (agence_emetteur_id_id) REFERENCES agence (id)');
        $this->addSql('ALTER TABLE demande_ordre_mission ADD CONSTRAINT FK_DB49BFEE5FD929EB FOREIGN KEY (service_emetteur_id_id) REFERENCES service (id)');
        $this->addSql('ALTER TABLE demande_ordre_mission ADD CONSTRAINT FK_DB49BFEE3AD3919B FOREIGN KEY (agence_debiteur_id_id) REFERENCES agence (id)');
        $this->addSql('ALTER TABLE demande_ordre_mission ADD CONSTRAINT FK_DB49BFEE892F7BB3 FOREIGN KEY (service_debiteur_id_id) REFERENCES service (id)');
        $this->addSql('DROP INDEX UNIQ_5626BB5277153098 ON agence_service_irium');
        $this->addSql('ALTER TABLE personnel DROP CONSTRAINT FK_A6BCF3DE29B37C39');
        $this->addSql('DROP INDEX IDX_A6BCF3DE29B37C39 ON personnel');
        $this->addSql('sp_rename \'personnel.agence_service_id\', \'agence_service_irium_id\', \'COLUMN\'');
        $this->addSql('ALTER TABLE personnel ADD CONSTRAINT FK_A6BCF3DEDC45CD36 FOREIGN KEY (agence_service_irium_id) REFERENCES agence_service_irium (id)');
        $this->addSql('CREATE INDEX IDX_A6BCF3DEDC45CD36 ON personnel (agence_service_irium_id)');
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
        $this->addSql('ALTER TABLE demande_ordre_mission DROP CONSTRAINT FK_DB49BFEEECD3392B');
        $this->addSql('ALTER TABLE demande_ordre_mission DROP CONSTRAINT FK_DB49BFEE65654707');
        $this->addSql('ALTER TABLE demande_ordre_mission DROP CONSTRAINT FK_DB49BFEE8C1EC44B');
        $this->addSql('ALTER TABLE demande_ordre_mission DROP CONSTRAINT FK_DB49BFEE3F5343F1');
        $this->addSql('ALTER TABLE demande_ordre_mission DROP CONSTRAINT FK_DB49BFEEEC25C3C3');
        $this->addSql('ALTER TABLE demande_ordre_mission DROP CONSTRAINT FK_DB49BFEE5FD929EB');
        $this->addSql('ALTER TABLE demande_ordre_mission DROP CONSTRAINT FK_DB49BFEE3AD3919B');
        $this->addSql('ALTER TABLE demande_ordre_mission DROP CONSTRAINT FK_DB49BFEE892F7BB3');
        $this->addSql('DROP TABLE demande_ordre_mission');
        $this->addSql('DROP TABLE dom_sous_type_document');
        $this->addSql('DROP TABLE statut_demande');
        $this->addSql('ALTER TABLE personnel DROP CONSTRAINT FK_A6BCF3DEDC45CD36');
        $this->addSql('DROP INDEX IDX_A6BCF3DEDC45CD36 ON personnel');
        $this->addSql('sp_rename \'personnel.agence_service_irium_id\', \'agence_service_id\', \'COLUMN\'');
        $this->addSql('ALTER TABLE personnel ADD CONSTRAINT FK_A6BCF3DE29B37C39 FOREIGN KEY (agence_service_id) REFERENCES agence_service_irium (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE NONCLUSTERED INDEX IDX_A6BCF3DE29B37C39 ON personnel (agence_service_id)');
        $this->addSql('CREATE UNIQUE NONCLUSTERED INDEX UNIQ_5626BB5277153098 ON agence_service_irium (code) WHERE code IS NOT NULL');
    }
}
