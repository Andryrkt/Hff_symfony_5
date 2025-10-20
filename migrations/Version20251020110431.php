<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251020110431 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'ajout de colonne societe dans la table personnel et suppression des tables relatives aux ordres de mission';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE demande_ordre_mission DROP CONSTRAINT FK_DB49BFEE8C1EC44B');
        $this->addSql('ALTER TABLE demande_ordre_mission DROP CONSTRAINT FK_DB49BFEE3F5343F1');
        $this->addSql('ALTER TABLE demande_ordre_mission DROP CONSTRAINT FK_DB49BFEEEC25C3C3');
        $this->addSql('ALTER TABLE demande_ordre_mission DROP CONSTRAINT FK_DB49BFEE3AD3919B');
        $this->addSql('ALTER TABLE demande_ordre_mission DROP CONSTRAINT FK_DB49BFEE65654707');
        $this->addSql('ALTER TABLE demande_ordre_mission DROP CONSTRAINT FK_DB49BFEE5FD929EB');
        $this->addSql('ALTER TABLE demande_ordre_mission DROP CONSTRAINT FK_DB49BFEE892F7BB3');
        $this->addSql('ALTER TABLE demande_ordre_mission DROP CONSTRAINT FK_DB49BFEEECD3392B');
        $this->addSql('ALTER TABLE dom_categorie DROP CONSTRAINT FK_E7D3C1534F1788B4');
        $this->addSql('ALTER TABLE dom_indemnite DROP CONSTRAINT FK_D1109AE64F1788B4');
        $this->addSql('ALTER TABLE dom_indemnite DROP CONSTRAINT FK_D1109AE6335F19AB');
        $this->addSql('ALTER TABLE dom_indemnite DROP CONSTRAINT FK_D1109AE6D3BD5507');
        $this->addSql('ALTER TABLE dom_indemnite DROP CONSTRAINT FK_D1109AE6686614BE');
        $this->addSql('DROP TABLE demande_ordre_mission');
        $this->addSql('DROP TABLE dom_sous_type_document');
        $this->addSql('DROP TABLE dom_categorie');
        $this->addSql('DROP TABLE dom_indemnite');
        $this->addSql('DROP TABLE dom_rmq');
        $this->addSql('DROP TABLE dom_site');
        $this->addSql('ALTER TABLE personnel ADD societe NVARCHAR(3)');
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
        $this->addSql('CREATE TABLE demande_ordre_mission (id INT IDENTITY NOT NULL, dom_demandeur_id INT, dom_personnel_id INT, dom_sous_type_document_id INT, statut_demande_id_id INT, agence_emetteur_id_id INT, service_emetteur_id_id INT, agence_debiteur_id_id INT, service_debiteur_id_id INT, numero_ordre_mission NVARCHAR(11) COLLATE French_CI_AS NOT NULL, date_debut_mission DATETIME2(6), date_fin_mission DATETIME2(6), nombre_jours SMALLINT, motif_deplacement VARCHAR(MAX) COLLATE French_CI_AS, client NVARCHAR(100) COLLATE French_CI_AS, lieu_intervention NVARCHAR(100) COLLATE French_CI_AS, vehicule_societe NVARCHAR(3) COLLATE French_CI_AS, motif_autres_depense1 NVARCHAR(50) COLLATE French_CI_AS, montant_autres_depense1 DOUBLE PRECISION, motif_autres_depense2 NVARCHAR(50) COLLATE French_CI_AS, montant_autres_depense2 DOUBLE PRECISION, motif_autres_depense3 NVARCHAR(50) COLLATE French_CI_AS, montant_autres_depense3 DOUBLE PRECISION NOT NULL, indemnite_forfaitaire DOUBLE PRECISION, total_indemnite_forfaitaire DOUBLE PRECISION, total_general_payer DOUBLE PRECISION, mode_paiement NVARCHAR(50) COLLATE French_CI_AS, piece_jointe1 NVARCHAR(50) COLLATE French_CI_AS, piece_jointe2 NVARCHAR(50) COLLATE French_CI_AS, code_statut NVARCHAR(3) COLLATE French_CI_AS, numero_tel NVARCHAR(10) COLLATE French_CI_AS, devis NVARCHAR(3) COLLATE French_CI_AS, fiche NVARCHAR(20) COLLATE French_CI_AS, numero_vehicule NVARCHAR(20) COLLATE French_CI_AS, supplement_journalier DOUBLE PRECISION, indemnite_chantier DOUBLE PRECISION, PRIMARY KEY (id))');
        $this->addSql('CREATE NONCLUSTERED INDEX IDX_DB49BFEEECD3392B ON demande_ordre_mission (dom_demandeur_id)');
        $this->addSql('CREATE NONCLUSTERED INDEX IDX_DB49BFEE65654707 ON demande_ordre_mission (dom_personnel_id)');
        $this->addSql('CREATE NONCLUSTERED INDEX IDX_DB49BFEE8C1EC44B ON demande_ordre_mission (dom_sous_type_document_id)');
        $this->addSql('CREATE NONCLUSTERED INDEX IDX_DB49BFEE3F5343F1 ON demande_ordre_mission (statut_demande_id_id)');
        $this->addSql('CREATE NONCLUSTERED INDEX IDX_DB49BFEEEC25C3C3 ON demande_ordre_mission (agence_emetteur_id_id)');
        $this->addSql('CREATE NONCLUSTERED INDEX IDX_DB49BFEE5FD929EB ON demande_ordre_mission (service_emetteur_id_id)');
        $this->addSql('CREATE NONCLUSTERED INDEX IDX_DB49BFEE3AD3919B ON demande_ordre_mission (agence_debiteur_id_id)');
        $this->addSql('CREATE NONCLUSTERED INDEX IDX_DB49BFEE892F7BB3 ON demande_ordre_mission (service_debiteur_id_id)');
        $this->addSql('CREATE TABLE dom_sous_type_document (id INT IDENTITY NOT NULL, code_sous_type NVARCHAR(50) COLLATE French_CI_AS NOT NULL, created_at DATETIME2(6) NOT NULL, updated_at DATETIME2(6), PRIMARY KEY (id))');
        $this->addSql('CREATE TABLE dom_categorie (id INT IDENTITY NOT NULL, dom_sous_type_document_id_id INT, description NVARCHAR(60) COLLATE French_CI_AS NOT NULL, created_at DATETIME2(6) NOT NULL, updated_at DATETIME2(6), PRIMARY KEY (id))');
        $this->addSql('CREATE NONCLUSTERED INDEX IDX_E7D3C1534F1788B4 ON dom_categorie (dom_sous_type_document_id_id)');
        $this->addSql('CREATE TABLE dom_indemnite (id INT IDENTITY NOT NULL, dom_site_id_id INT, dom_categorie_id_id INT, dom_rmq_id_id INT, dom_sous_type_document_id_id INT, montant DOUBLE PRECISION NOT NULL, created_at DATETIME2(6) NOT NULL, updated_at DATETIME2(6), PRIMARY KEY (id))');
        $this->addSql('CREATE NONCLUSTERED INDEX IDX_D1109AE6686614BE ON dom_indemnite (dom_site_id_id)');
        $this->addSql('CREATE NONCLUSTERED INDEX IDX_D1109AE6335F19AB ON dom_indemnite (dom_categorie_id_id)');
        $this->addSql('CREATE NONCLUSTERED INDEX IDX_D1109AE6D3BD5507 ON dom_indemnite (dom_rmq_id_id)');
        $this->addSql('CREATE NONCLUSTERED INDEX IDX_D1109AE64F1788B4 ON dom_indemnite (dom_sous_type_document_id_id)');
        $this->addSql('CREATE TABLE dom_rmq (id INT IDENTITY NOT NULL, description NVARCHAR(5) COLLATE French_CI_AS NOT NULL, created_at DATETIME2(6) NOT NULL, updated_at DATETIME2(6), PRIMARY KEY (id))');
        $this->addSql('CREATE TABLE dom_site (id INT IDENTITY NOT NULL, nom_zone NVARCHAR(50) COLLATE French_CI_AS NOT NULL, created_at DATETIME2(6) NOT NULL, updated_at DATETIME2(6), PRIMARY KEY (id))');
        $this->addSql('ALTER TABLE demande_ordre_mission ADD CONSTRAINT FK_DB49BFEE8C1EC44B FOREIGN KEY (dom_sous_type_document_id) REFERENCES dom_sous_type_document (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE demande_ordre_mission ADD CONSTRAINT FK_DB49BFEE3F5343F1 FOREIGN KEY (statut_demande_id_id) REFERENCES statut_demande (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE demande_ordre_mission ADD CONSTRAINT FK_DB49BFEEEC25C3C3 FOREIGN KEY (agence_emetteur_id_id) REFERENCES agence (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE demande_ordre_mission ADD CONSTRAINT FK_DB49BFEE3AD3919B FOREIGN KEY (agence_debiteur_id_id) REFERENCES agence (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE demande_ordre_mission ADD CONSTRAINT FK_DB49BFEE65654707 FOREIGN KEY (dom_personnel_id) REFERENCES personnel (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE demande_ordre_mission ADD CONSTRAINT FK_DB49BFEE5FD929EB FOREIGN KEY (service_emetteur_id_id) REFERENCES service (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE demande_ordre_mission ADD CONSTRAINT FK_DB49BFEE892F7BB3 FOREIGN KEY (service_debiteur_id_id) REFERENCES service (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE demande_ordre_mission ADD CONSTRAINT FK_DB49BFEEECD3392B FOREIGN KEY (dom_demandeur_id) REFERENCES [user] (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE dom_categorie ADD CONSTRAINT FK_E7D3C1534F1788B4 FOREIGN KEY (dom_sous_type_document_id_id) REFERENCES dom_sous_type_document (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE dom_indemnite ADD CONSTRAINT FK_D1109AE64F1788B4 FOREIGN KEY (dom_sous_type_document_id_id) REFERENCES dom_sous_type_document (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE dom_indemnite ADD CONSTRAINT FK_D1109AE6335F19AB FOREIGN KEY (dom_categorie_id_id) REFERENCES dom_categorie (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE dom_indemnite ADD CONSTRAINT FK_D1109AE6D3BD5507 FOREIGN KEY (dom_rmq_id_id) REFERENCES dom_rmq (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE dom_indemnite ADD CONSTRAINT FK_D1109AE6686614BE FOREIGN KEY (dom_site_id_id) REFERENCES dom_site (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE personnel DROP COLUMN societe');
    }
}
