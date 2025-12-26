<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251223132109 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE dit (id INT IDENTITY NOT NULL, wor_type_document_id INT, wor_niveau_urgence_id INT, categorie_ate_app_id INT, statut_demande_id INT, agence_emetteur_id INT, service_emetteur_id INT, agence_debiteur_id INT, service_debiteur_id INT, numero_dit NVARCHAR(11) NOT NULL, type_reparation NVARCHAR(30) NOT NULL, reparation_realise NVARCHAR(30) NOT NULL, interne_externe NVARCHAR(30) NOT NULL, nom_client NVARCHAR(100), numero_tel_client NVARCHAR(10), mail_client NVARCHAR(100), date_or DATE, date_prevue_travaux DATE NOT NULL, demande_devis NVARCHAR(3) NOT NULL, avis_recouvrement NVARCHAR(3) NOT NULL, client_sous_contrat NVARCHAR(3), object_demande NVARCHAR(100) NOT NULL, detail_demande VARCHAR(MAX) NOT NULL, livraison_partiel NVARCHAR(3) NOT NULL, id_materiel INT NOT NULL, numero_client NVARCHAR(15), libelle_client NVARCHAR(50), numero_or NVARCHAR(8), numero_devis_rattacher NVARCHAR(20), statut_devis NVARCHAR(50), section_affectee NVARCHAR(150), statut_or NVARCHAR(20), date_validation_or DATE, etat_facturation NVARCHAR(50), ri NVARCHAR(10), numero_migration SMALLINT, est_annuler BIT NOT NULL, date_annulation DATETIME2(6), numero_demande_dit_avoir NVARCHAR(11), numero_demande_dit_refacturation NVARCHAR(11) NOT NULL, est_dit_avoir BIT NOT NULL, est_dit_refacturation BIT NOT NULL, est_ate_pol_tana BIT NOT NULL, heure_machine INT NOT NULL, km_machine INT NOT NULL, created_at DATETIME2(6) NOT NULL, updated_at DATETIME2(6), createdBy INT, PRIMARY KEY (id))');
        $this->addSql('CREATE INDEX IDX_53C8DF252DF4BEB0 ON dit (wor_type_document_id)');
        $this->addSql('CREATE INDEX IDX_53C8DF25F7769E30 ON dit (wor_niveau_urgence_id)');
        $this->addSql('CREATE INDEX IDX_53C8DF256BFAE842 ON dit (categorie_ate_app_id)');
        $this->addSql('CREATE INDEX IDX_53C8DF25F5225311 ON dit (statut_demande_id)');
        $this->addSql('CREATE INDEX IDX_53C8DF256F410E1C ON dit (agence_emetteur_id)');
        $this->addSql('CREATE INDEX IDX_53C8DF25B5730820 ON dit (service_emetteur_id)');
        $this->addSql('CREATE INDEX IDX_53C8DF253EC7D81B ON dit (agence_debiteur_id)');
        $this->addSql('CREATE INDEX IDX_53C8DF25E4F5DE27 ON dit (service_debiteur_id)');
        $this->addSql('CREATE INDEX IDX_53C8DF25D3564642 ON dit (createdBy)');
        $this->addSql('ALTER TABLE dit ADD CONSTRAINT FK_53C8DF252DF4BEB0 FOREIGN KEY (wor_type_document_id) REFERENCES wor_type_document (id)');
        $this->addSql('ALTER TABLE dit ADD CONSTRAINT FK_53C8DF25F7769E30 FOREIGN KEY (wor_niveau_urgence_id) REFERENCES wor_niveau_urgence (id)');
        $this->addSql('ALTER TABLE dit ADD CONSTRAINT FK_53C8DF256BFAE842 FOREIGN KEY (categorie_ate_app_id) REFERENCES categorie_ate_app (id)');
        $this->addSql('ALTER TABLE dit ADD CONSTRAINT FK_53C8DF25F5225311 FOREIGN KEY (statut_demande_id) REFERENCES statut_demande (id)');
        $this->addSql('ALTER TABLE dit ADD CONSTRAINT FK_53C8DF256F410E1C FOREIGN KEY (agence_emetteur_id) REFERENCES agence (id)');
        $this->addSql('ALTER TABLE dit ADD CONSTRAINT FK_53C8DF25B5730820 FOREIGN KEY (service_emetteur_id) REFERENCES service (id)');
        $this->addSql('ALTER TABLE dit ADD CONSTRAINT FK_53C8DF253EC7D81B FOREIGN KEY (agence_debiteur_id) REFERENCES agence (id)');
        $this->addSql('ALTER TABLE dit ADD CONSTRAINT FK_53C8DF25E4F5DE27 FOREIGN KEY (service_debiteur_id) REFERENCES service (id)');
        $this->addSql('ALTER TABLE dit ADD CONSTRAINT FK_53C8DF25D3564642 FOREIGN KEY (createdBy) REFERENCES [user] (id)');
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
        $this->addSql('ALTER TABLE dit DROP CONSTRAINT FK_53C8DF252DF4BEB0');
        $this->addSql('ALTER TABLE dit DROP CONSTRAINT FK_53C8DF25F7769E30');
        $this->addSql('ALTER TABLE dit DROP CONSTRAINT FK_53C8DF256BFAE842');
        $this->addSql('ALTER TABLE dit DROP CONSTRAINT FK_53C8DF25F5225311');
        $this->addSql('ALTER TABLE dit DROP CONSTRAINT FK_53C8DF256F410E1C');
        $this->addSql('ALTER TABLE dit DROP CONSTRAINT FK_53C8DF25B5730820');
        $this->addSql('ALTER TABLE dit DROP CONSTRAINT FK_53C8DF253EC7D81B');
        $this->addSql('ALTER TABLE dit DROP CONSTRAINT FK_53C8DF25E4F5DE27');
        $this->addSql('ALTER TABLE dit DROP CONSTRAINT FK_53C8DF25D3564642');
        $this->addSql('DROP TABLE dit');
    }
}
