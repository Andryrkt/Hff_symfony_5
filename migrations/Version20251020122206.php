<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251020122206 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        // $this->addSql('ALTER TABLE demande_ordre_mission DROP CONSTRAINT FK_DB49BFEE8C1EC44B');
        // $this->addSql('ALTER TABLE demande_ordre_mission DROP CONSTRAINT FK_DB49BFEE3F5343F1');
        // $this->addSql('ALTER TABLE demande_ordre_mission DROP CONSTRAINT FK_DB49BFEEEC25C3C3');
        // $this->addSql('ALTER TABLE demande_ordre_mission DROP CONSTRAINT FK_DB49BFEE3AD3919B');
        // $this->addSql('ALTER TABLE demande_ordre_mission DROP CONSTRAINT FK_DB49BFEE65654707');
        // $this->addSql('ALTER TABLE demande_ordre_mission DROP CONSTRAINT FK_DB49BFEE5FD929EB');
        // $this->addSql('ALTER TABLE demande_ordre_mission DROP CONSTRAINT FK_DB49BFEE892F7BB3');
        // $this->addSql('ALTER TABLE demande_ordre_mission DROP CONSTRAINT FK_DB49BFEEECD3392B');
        // $this->addSql('DROP TABLE demande_ordre_mission');
        // $this->addSql('ALTER TABLE dom_categorie DROP CONSTRAINT FK_E7D3C1534F1788B4');
        // $this->addSql('DROP INDEX IDX_E7D3C1534F1788B4 ON dom_categorie');
        // $this->addSql('sp_rename \'dom_categorie.dom_sous_type_document_id_id\', \'sous_type_document_id_id\', \'COLUMN\'');
        // $this->addSql('ALTER TABLE dom_categorie ALTER COLUMN description NVARCHAR(60)');
        // $this->addSql('ALTER TABLE dom_categorie ADD CONSTRAINT FK_E7D3C153ED6251B5 FOREIGN KEY (sous_type_document_id_id) REFERENCES dom_sous_type_document (id)');
        // $this->addSql('CREATE INDEX IDX_E7D3C153ED6251B5 ON dom_categorie (sous_type_document_id_id)');
        // $this->addSql('ALTER TABLE dom_indemnite DROP CONSTRAINT FK_D1109AE64F1788B4');
        // $this->addSql('ALTER TABLE dom_indemnite DROP CONSTRAINT FK_D1109AE6335F19AB');
        // $this->addSql('ALTER TABLE dom_indemnite DROP CONSTRAINT FK_D1109AE6D3BD5507');
        // $this->addSql('ALTER TABLE dom_indemnite DROP CONSTRAINT FK_D1109AE6686614BE');
        // $this->addSql('DROP INDEX IDX_D1109AE6686614BE ON dom_indemnite');
        // $this->addSql('DROP INDEX IDX_D1109AE6335F19AB ON dom_indemnite');
        // $this->addSql('DROP INDEX IDX_D1109AE6D3BD5507 ON dom_indemnite');
        // $this->addSql('DROP INDEX IDX_D1109AE64F1788B4 ON dom_indemnite');
        // $this->addSql('ALTER TABLE dom_indemnite ADD site_id_id INT');
        // $this->addSql('ALTER TABLE dom_indemnite ADD categorie_id_id INT');
        // $this->addSql('ALTER TABLE dom_indemnite ADD rmq_id_id INT');
        // $this->addSql('ALTER TABLE dom_indemnite ADD sous_type_document_id_id INT');
        // $this->addSql('ALTER TABLE dom_indemnite DROP COLUMN dom_site_id_id');
        // $this->addSql('ALTER TABLE dom_indemnite DROP COLUMN dom_categorie_id_id');
        // $this->addSql('ALTER TABLE dom_indemnite DROP COLUMN dom_rmq_id_id');
        // $this->addSql('ALTER TABLE dom_indemnite DROP COLUMN dom_sous_type_document_id_id');
        // $this->addSql('ALTER TABLE dom_indemnite ALTER COLUMN montant DOUBLE PRECISION');
        // $this->addSql('ALTER TABLE dom_indemnite ADD CONSTRAINT FK_D1109AE6BB1E4E52 FOREIGN KEY (site_id_id) REFERENCES dom_site (id)');
        // $this->addSql('ALTER TABLE dom_indemnite ADD CONSTRAINT FK_D1109AE68A3C7387 FOREIGN KEY (categorie_id_id) REFERENCES dom_categorie (id)');
        // $this->addSql('ALTER TABLE dom_indemnite ADD CONSTRAINT FK_D1109AE67D134260 FOREIGN KEY (rmq_id_id) REFERENCES dom_rmq (id)');
        // $this->addSql('ALTER TABLE dom_indemnite ADD CONSTRAINT FK_D1109AE6ED6251B5 FOREIGN KEY (sous_type_document_id_id) REFERENCES dom_sous_type_document (id)');
        // $this->addSql('CREATE INDEX IDX_D1109AE6BB1E4E52 ON dom_indemnite (site_id_id)');
        // $this->addSql('CREATE INDEX IDX_D1109AE68A3C7387 ON dom_indemnite (categorie_id_id)');
        // $this->addSql('CREATE INDEX IDX_D1109AE67D134260 ON dom_indemnite (rmq_id_id)');
        // $this->addSql('CREATE INDEX IDX_D1109AE6ED6251B5 ON dom_indemnite (sous_type_document_id_id)');
        // $this->addSql('ALTER TABLE dom_rmq ALTER COLUMN description NVARCHAR(5)');
        // $this->addSql('ALTER TABLE dom_site ALTER COLUMN nom_zone NVARCHAR(50)');
        // $this->addSql('ALTER TABLE dom_sous_type_document ALTER COLUMN code_sous_type NVARCHAR(50)');
        // $this->addSql('ALTER TABLE personnel ADD societe NVARCHAR(3)');
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
        $this->addSql('ALTER TABLE demande_ordre_mission ADD CONSTRAINT FK_DB49BFEE8C1EC44B FOREIGN KEY (dom_sous_type_document_id) REFERENCES dom_sous_type_document (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE demande_ordre_mission ADD CONSTRAINT FK_DB49BFEE3F5343F1 FOREIGN KEY (statut_demande_id_id) REFERENCES statut_demande (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE demande_ordre_mission ADD CONSTRAINT FK_DB49BFEEEC25C3C3 FOREIGN KEY (agence_emetteur_id_id) REFERENCES agence (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE demande_ordre_mission ADD CONSTRAINT FK_DB49BFEE3AD3919B FOREIGN KEY (agence_debiteur_id_id) REFERENCES agence (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE demande_ordre_mission ADD CONSTRAINT FK_DB49BFEE65654707 FOREIGN KEY (dom_personnel_id) REFERENCES personnel (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE demande_ordre_mission ADD CONSTRAINT FK_DB49BFEE5FD929EB FOREIGN KEY (service_emetteur_id_id) REFERENCES service (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE demande_ordre_mission ADD CONSTRAINT FK_DB49BFEE892F7BB3 FOREIGN KEY (service_debiteur_id_id) REFERENCES service (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE demande_ordre_mission ADD CONSTRAINT FK_DB49BFEEECD3392B FOREIGN KEY (dom_demandeur_id) REFERENCES [user] (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE dom_sous_type_document ALTER COLUMN code_sous_type NVARCHAR(50) NOT NULL');
        $this->addSql('ALTER TABLE dom_categorie DROP CONSTRAINT FK_E7D3C153ED6251B5');
        $this->addSql('DROP INDEX IDX_E7D3C153ED6251B5 ON dom_categorie');
        $this->addSql('sp_rename \'dom_categorie.sous_type_document_id_id\', \'dom_sous_type_document_id_id\', \'COLUMN\'');
        $this->addSql('ALTER TABLE dom_categorie ALTER COLUMN description NVARCHAR(60) NOT NULL');
        $this->addSql('ALTER TABLE dom_categorie ADD CONSTRAINT FK_E7D3C1534F1788B4 FOREIGN KEY (dom_sous_type_document_id_id) REFERENCES dom_sous_type_document (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE NONCLUSTERED INDEX IDX_E7D3C1534F1788B4 ON dom_categorie (dom_sous_type_document_id_id)');
        $this->addSql('ALTER TABLE dom_indemnite DROP CONSTRAINT FK_D1109AE6BB1E4E52');
        $this->addSql('ALTER TABLE dom_indemnite DROP CONSTRAINT FK_D1109AE68A3C7387');
        $this->addSql('ALTER TABLE dom_indemnite DROP CONSTRAINT FK_D1109AE67D134260');
        $this->addSql('ALTER TABLE dom_indemnite DROP CONSTRAINT FK_D1109AE6ED6251B5');
        $this->addSql('DROP INDEX IDX_D1109AE6BB1E4E52 ON dom_indemnite');
        $this->addSql('DROP INDEX IDX_D1109AE68A3C7387 ON dom_indemnite');
        $this->addSql('DROP INDEX IDX_D1109AE67D134260 ON dom_indemnite');
        $this->addSql('DROP INDEX IDX_D1109AE6ED6251B5 ON dom_indemnite');
        $this->addSql('ALTER TABLE dom_indemnite ADD dom_site_id_id INT');
        $this->addSql('ALTER TABLE dom_indemnite ADD dom_categorie_id_id INT');
        $this->addSql('ALTER TABLE dom_indemnite ADD dom_rmq_id_id INT');
        $this->addSql('ALTER TABLE dom_indemnite ADD dom_sous_type_document_id_id INT');
        $this->addSql('ALTER TABLE dom_indemnite DROP COLUMN site_id_id');
        $this->addSql('ALTER TABLE dom_indemnite DROP COLUMN categorie_id_id');
        $this->addSql('ALTER TABLE dom_indemnite DROP COLUMN rmq_id_id');
        $this->addSql('ALTER TABLE dom_indemnite DROP COLUMN sous_type_document_id_id');
        $this->addSql('ALTER TABLE dom_indemnite ALTER COLUMN montant DOUBLE PRECISION NOT NULL');
        $this->addSql('ALTER TABLE dom_indemnite ADD CONSTRAINT FK_D1109AE64F1788B4 FOREIGN KEY (dom_sous_type_document_id_id) REFERENCES dom_sous_type_document (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE dom_indemnite ADD CONSTRAINT FK_D1109AE6335F19AB FOREIGN KEY (dom_categorie_id_id) REFERENCES dom_categorie (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE dom_indemnite ADD CONSTRAINT FK_D1109AE6D3BD5507 FOREIGN KEY (dom_rmq_id_id) REFERENCES dom_rmq (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE dom_indemnite ADD CONSTRAINT FK_D1109AE6686614BE FOREIGN KEY (dom_site_id_id) REFERENCES dom_site (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE NONCLUSTERED INDEX IDX_D1109AE6686614BE ON dom_indemnite (dom_site_id_id)');
        $this->addSql('CREATE NONCLUSTERED INDEX IDX_D1109AE6335F19AB ON dom_indemnite (dom_categorie_id_id)');
        $this->addSql('CREATE NONCLUSTERED INDEX IDX_D1109AE6D3BD5507 ON dom_indemnite (dom_rmq_id_id)');
        $this->addSql('CREATE NONCLUSTERED INDEX IDX_D1109AE64F1788B4 ON dom_indemnite (dom_sous_type_document_id_id)');
        $this->addSql('ALTER TABLE dom_rmq ALTER COLUMN description NVARCHAR(5) NOT NULL');
        $this->addSql('ALTER TABLE dom_site ALTER COLUMN nom_zone NVARCHAR(50) NOT NULL');
        $this->addSql('ALTER TABLE personnel DROP COLUMN societe');
    }
}
